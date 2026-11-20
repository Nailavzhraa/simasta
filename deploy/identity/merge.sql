\set ON_ERROR_STOP on
BEGIN;
LOCK TABLE simasta.users, presensi.users, presensi.profiles IN SHARE ROW EXCLUSIVE MODE;
CREATE TEMP TABLE original_presensi_ids ON COMMIT DROP AS SELECT id FROM presensi.users;
DO $$
BEGIN
    IF EXISTS (SELECT 1 FROM simasta.users GROUP BY lower(btrim(email)) HAVING count(*)>1) THEN
        RAISE EXCEPTION 'Duplicate normalized SIMASTA emails; review before matching';
    END IF;
    IF EXISTS (SELECT 1 FROM presensi.users WHERE password_hash IS NOT NULL
        AND password_hash<>'!google-only' AND password_hash NOT LIKE 'scrypt$32768$8$1$%' AND password_hash NOT LIKE '$2%') THEN
        RAISE EXCEPTION 'Unsupported legacy password format';
    END IF;
    IF EXISTS (SELECT 1 FROM simasta.users s JOIN presensi.users p ON lower(btrim(s.email))=p.email
        WHERE s.email_verified_at IS NULL AND s.password<>p.password_hash) THEN
        RAISE EXCEPTION 'Unverified conflicting SIMASTA account requires review before linking';
    END IF;
    IF EXISTS (SELECT 1 FROM simasta.users s JOIN presensi.users u ON lower(btrim(s.email))=u.email
        JOIN presensi.profiles p ON p.id=u.id WHERE
        (CASE WHEN s.role='admin' THEN 'admin' ELSE 'peserta' END)<>p.role OR s.password<>u.password_hash) THEN
        RAISE EXCEPTION 'Existing role or password conflict requires review before linking';
    END IF;
    IF EXISTS (SELECT 1 FROM simasta.users s JOIN presensi.users p ON lower(btrim(s.email))=p.email
        JOIN presensi.google_identities g ON g.user_id=p.id WHERE s.google_id IS NOT NULL AND s.google_id<>g.subject) THEN
        RAISE EXCEPTION 'Google identity conflict requires review';
    END IF;
END $$;

ALTER TABLE simasta.users ADD COLUMN IF NOT EXISTS is_active boolean NOT NULL DEFAULT true;
ALTER TABLE simasta.users ADD COLUMN IF NOT EXISTS presensi_migrated_at timestamp;
CREATE UNIQUE INDEX IF NOT EXISTS users_normalized_email_unique ON simasta.users (lower(btrim(email)));
UPDATE simasta.users SET email=lower(btrim(email)) WHERE email<>lower(btrim(email));

-- Import unmatched legacy identities, preserving their credential and active state.
-- Do not assert email verification for old password accounts: record migration separately.
INSERT INTO simasta.users(name,email,password,role,email_verified_at,google_id,is_active,presensi_migrated_at,created_at,updated_at)
SELECT p.nama_lengkap,u.email,coalesce(u.password_hash,'!google-only'),
    CASE WHEN p.role='admin' THEN 'admin' ELSE 'mahasiswa' END,
    NULL,g.subject,u.active,localtimestamp,u.created_at AT TIME ZONE 'Asia/Jakarta',localtimestamp
FROM presensi.users u JOIN presensi.profiles p ON p.id=u.id
LEFT JOIN presensi.google_identities g ON g.user_id=u.id
WHERE NOT EXISTS (SELECT 1 FROM simasta.users s WHERE lower(btrim(s.email))=u.email);

UPDATE simasta.users s SET google_id=g.subject
FROM presensi.users p JOIN presensi.google_identities g ON g.user_id=p.id
WHERE s.email=p.email AND s.google_id IS NULL;

ALTER TABLE presensi.users ADD COLUMN simasta_user_id bigint UNIQUE REFERENCES simasta.users(id) ON DELETE RESTRICT;
ALTER TABLE presensi.sessions ADD COLUMN identity_version text;
UPDATE presensi.users p SET simasta_user_id=s.id FROM simasta.users s WHERE s.email=p.email;

INSERT INTO simasta.mahasiswas(user_id,nama,email,nim,perguruan_tinggi,no_telepon,no_telp,created_at,updated_at)
SELECT s.id,s.name,s.email,
    CASE WHEN nullif(btrim(p.nim),'') IS NOT NULL
         AND (SELECT count(*) FROM presensi.profiles other WHERE other.nim=p.nim)=1
         AND NOT EXISTS (SELECT 1 FROM simasta.mahasiswas m WHERE m.nim=p.nim) THEN p.nim ELSE NULL END,
    p.asal_instansi,p.no_hp,p.no_hp,localtimestamp,localtimestamp
FROM simasta.users s JOIN presensi.users u ON u.simasta_user_id=s.id JOIN presensi.profiles p ON p.id=u.id
WHERE s.role='mahasiswa' AND NOT EXISTS (SELECT 1 FROM simasta.mahasiswas m WHERE m.user_id=s.id);

ALTER TABLE presensi.profiles DROP CONSTRAINT IF EXISTS profiles_nama_lengkap_check;
ALTER TABLE presensi.profiles ADD CONSTRAINT profiles_nama_lengkap_check CHECK (length(btrim(nama_lengkap)) BETWEEN 1 AND 255);

CREATE OR REPLACE FUNCTION simasta.sync_presensi_identity(account_id bigint) RETURNS void
LANGUAGE plpgsql SECURITY DEFINER SET search_path=pg_catalog AS $$
DECLARE account simasta.users%ROWTYPE; student simasta.mahasiswas%ROWTYPE; attendance_id uuid;
BEGIN
    SELECT * INTO account FROM simasta.users WHERE id=account_id;
    IF NOT FOUND THEN RETURN; END IF;
    SELECT * INTO student FROM simasta.mahasiswas WHERE user_id=account_id;
    INSERT INTO presensi.users(email,password_hash,active,simasta_user_id)
    VALUES (account.email,'!simasta-managed',account.is_active,account.id)
    ON CONFLICT (simasta_user_id) DO UPDATE SET email=excluded.email,
        active=excluded.active,password_hash='!simasta-managed'
    RETURNING id INTO attendance_id;
    INSERT INTO presensi.profiles(id,nama_lengkap,role,nim,asal_instansi,no_hp)
    VALUES (attendance_id,account.name,CASE WHEN account.role='admin' THEN 'admin' ELSE 'peserta' END,
        student.nim,student.perguruan_tinggi,student.no_telepon)
    ON CONFLICT (id) DO UPDATE SET nama_lengkap=excluded.nama_lengkap,role=excluded.role,
        nim=coalesce(excluded.nim,presensi.profiles.nim),
        asal_instansi=coalesce(excluded.asal_instansi,presensi.profiles.asal_instansi),
        no_hp=coalesce(excluded.no_hp,presensi.profiles.no_hp);
END $$;
REVOKE ALL ON FUNCTION simasta.sync_presensi_identity(bigint) FROM PUBLIC;

CREATE OR REPLACE FUNCTION simasta.on_identity_change() RETURNS trigger
LANGUAGE plpgsql SECURITY DEFINER SET search_path=pg_catalog AS $$
BEGIN
    PERFORM simasta.sync_presensi_identity(NEW.id);
    IF TG_OP='UPDATE' THEN
        IF OLD.password IS DISTINCT FROM NEW.password OR OLD.is_active IS DISTINCT FROM NEW.is_active
           OR OLD.google_id IS DISTINCT FROM NEW.google_id OR OLD.role IS DISTINCT FROM NEW.role
           OR OLD.email IS DISTINCT FROM NEW.email
           OR OLD.email_verified_at IS DISTINCT FROM NEW.email_verified_at
           OR OLD.presensi_migrated_at IS DISTINCT FROM NEW.presensi_migrated_at THEN
            DELETE FROM presensi.sessions WHERE user_id IN (SELECT id FROM presensi.users WHERE simasta_user_id=NEW.id);
            DELETE FROM simasta.sessions WHERE user_id=NEW.id;
        END IF;
    END IF;
    RETURN NEW;
END $$;
REVOKE ALL ON FUNCTION simasta.on_identity_change() FROM PUBLIC;
CREATE TRIGGER sync_presensi_identity AFTER INSERT OR UPDATE ON simasta.users
FOR EACH ROW EXECUTE FUNCTION simasta.on_identity_change();

CREATE OR REPLACE FUNCTION simasta.on_student_identity_change() RETURNS trigger
LANGUAGE plpgsql SECURITY DEFINER SET search_path=pg_catalog AS $$
BEGIN
    IF TG_OP='DELETE' THEN
        IF OLD.user_id IS NOT NULL THEN PERFORM simasta.sync_presensi_identity(OLD.user_id); END IF;
        RETURN OLD;
    END IF;
    IF NEW.user_id IS NOT NULL THEN PERFORM simasta.sync_presensi_identity(NEW.user_id); END IF;
    RETURN NEW;
END $$;
REVOKE ALL ON FUNCTION simasta.on_student_identity_change() FROM PUBLIC;
CREATE TRIGGER sync_presensi_student AFTER INSERT OR UPDATE OR DELETE ON simasta.mahasiswas
FOR EACH ROW EXECUTE FUNCTION simasta.on_student_identity_change();

SELECT simasta.sync_presensi_identity(id) FROM simasta.users;
ALTER TABLE presensi.users ALTER COLUMN simasta_user_id SET NOT NULL;

CREATE VIEW simasta.presensi_accounts WITH (security_barrier=true) AS
SELECT s.id,s.name,s.email,s.password,s.role,s.is_active,s.google_id,
    latest.status AS magang_status,
    (s.role='admin' OR (s.role='mahasiswa' AND latest.status='diterima'
        AND (s.email_verified_at IS NOT NULL OR s.presensi_migrated_at IS NOT NULL))) IS TRUE AS can_authenticate
FROM simasta.users s LEFT JOIN LATERAL (
    SELECT status FROM simasta.pengajuans WHERE user_id=s.id ORDER BY id DESC LIMIT 1
) latest ON true;
GRANT USAGE ON SCHEMA simasta TO :"presensi_role";
GRANT SELECT ON simasta.presensi_accounts TO :"presensi_role";

CREATE OR REPLACE FUNCTION simasta.on_presensi_access_change() RETURNS trigger
LANGUAGE plpgsql SECURITY DEFINER SET search_path=pg_catalog AS $$
DECLARE account_id bigint;
BEGIN
    IF TG_OP='DELETE' THEN account_id=OLD.user_id; ELSE account_id=NEW.user_id; END IF;
    DELETE FROM presensi.sessions WHERE user_id IN (SELECT id FROM presensi.users WHERE simasta_user_id=account_id);
    IF TG_OP='UPDATE' THEN
        IF OLD.user_id IS DISTINCT FROM NEW.user_id THEN
            DELETE FROM presensi.sessions WHERE user_id IN (SELECT id FROM presensi.users WHERE simasta_user_id=OLD.user_id);
        END IF;
    END IF;
    IF TG_OP='DELETE' THEN RETURN OLD; ELSE RETURN NEW; END IF;
END $$;
REVOKE ALL ON FUNCTION simasta.on_presensi_access_change() FROM PUBLIC;
CREATE TRIGGER refresh_presensi_access AFTER INSERT OR UPDATE OF status,user_id OR DELETE ON simasta.pengajuans
FOR EACH ROW EXECUTE FUNCTION simasta.on_presensi_access_change();

CREATE OR REPLACE FUNCTION simasta.presensi_change_password(attendance_id uuid, expected_hash text, next_hash text) RETURNS boolean
LANGUAGE plpgsql SECURITY DEFINER SET search_path=pg_catalog AS $$
DECLARE affected integer;
BEGIN
    IF next_hash !~ '^\$2[aby]\$[0-9]{2}\$[./A-Za-z0-9]{53}$' THEN RAISE EXCEPTION 'Invalid password hash'; END IF;
    UPDATE simasta.users s SET password=next_hash,remember_token=NULL,updated_at=localtimestamp
    FROM presensi.users p WHERE p.id=attendance_id AND p.simasta_user_id=s.id
        AND s.password=expected_hash AND s.is_active AND p.active;
    GET DIAGNOSTICS affected=ROW_COUNT;
    RETURN affected=1;
END $$;
REVOKE ALL ON FUNCTION simasta.presensi_change_password(uuid,text,text) FROM PUBLIC;
GRANT EXECUTE ON FUNCTION simasta.presensi_change_password(uuid,text,text) TO :"presensi_role";

CREATE OR REPLACE FUNCTION simasta.presensi_update_profile(attendance_id uuid, fields jsonb) RETURNS void
LANGUAGE plpgsql SECURITY DEFINER SET search_path=pg_catalog AS $$
DECLARE account simasta.users%ROWTYPE;
BEGIN
    IF jsonb_typeof(fields)<>'object' OR EXISTS (
        SELECT 1 FROM jsonb_object_keys(fields) k WHERE k NOT IN ('nama_lengkap','nim','asal_instansi','no_hp'))
        THEN RAISE EXCEPTION 'Invalid profile fields'; END IF;
    SELECT s.* INTO account FROM simasta.users s JOIN presensi.users p ON p.simasta_user_id=s.id
        WHERE p.id=attendance_id AND s.is_active AND p.active FOR UPDATE OF s;
    IF NOT FOUND THEN RAISE EXCEPTION 'Account unavailable'; END IF;
    IF fields ? 'nama_lengkap' THEN
        IF length(btrim(fields->>'nama_lengkap')) NOT BETWEEN 1 AND 150 OR fields->>'nama_lengkap' IS NULL
            THEN RAISE EXCEPTION 'Invalid name'; END IF;
        UPDATE simasta.users SET name=btrim(fields->>'nama_lengkap'),updated_at=localtimestamp WHERE id=account.id;
    END IF;
    IF account.role='mahasiswa' THEN
        INSERT INTO simasta.mahasiswas(user_id,nama,email,created_at,updated_at)
        VALUES(account.id,coalesce(btrim(fields->>'nama_lengkap'),account.name),account.email,localtimestamp,localtimestamp)
        ON CONFLICT (user_id) DO NOTHING;
        UPDATE simasta.mahasiswas SET
            nama=CASE WHEN fields ? 'nama_lengkap' THEN btrim(fields->>'nama_lengkap') ELSE nama END,
            nim=CASE WHEN fields ? 'nim' THEN nullif(btrim(fields->>'nim'),'') ELSE nim END,
            perguruan_tinggi=CASE WHEN fields ? 'asal_instansi' THEN fields->>'asal_instansi' ELSE perguruan_tinggi END,
            no_telepon=CASE WHEN fields ? 'no_hp' THEN fields->>'no_hp' ELSE no_telepon END,
            no_telp=CASE WHEN fields ? 'no_hp' THEN fields->>'no_hp' ELSE no_telp END,
            updated_at=localtimestamp WHERE user_id=account.id;
    END IF;
    -- Admin contact fields have no student-profile counterpart in SIMASTA.
    UPDATE presensi.profiles SET
        nim=CASE WHEN fields ? 'nim' THEN nullif(btrim(fields->>'nim'),'') ELSE nim END,
        asal_instansi=CASE WHEN fields ? 'asal_instansi' THEN fields->>'asal_instansi' ELSE asal_instansi END,
        no_hp=CASE WHEN fields ? 'no_hp' THEN fields->>'no_hp' ELSE no_hp END WHERE id=attendance_id;
END $$;
REVOKE ALL ON FUNCTION simasta.presensi_update_profile(uuid,jsonb) FROM PUBLIC;
GRANT EXECUTE ON FUNCTION simasta.presensi_update_profile(uuid,jsonb) TO :"presensi_role";

-- Existing sessions used separate credentials; require one fresh login at cutover.
DELETE FROM presensi.sessions;
DELETE FROM simasta.sessions;
DO $$ BEGIN
    IF EXISTS (SELECT id FROM original_presensi_ids EXCEPT SELECT id FROM presensi.users) THEN
        RAISE EXCEPTION 'An original presensi UUID is missing';
    END IF;
    IF EXISTS (SELECT 1 FROM presensi.users p JOIN simasta.users s ON s.id=p.simasta_user_id
        JOIN presensi.profiles r ON r.id=p.id WHERE p.email<>s.email OR r.nama_lengkap<>s.name
        OR r.role<>CASE WHEN s.role='admin' THEN 'admin' ELSE 'peserta' END) THEN
        RAISE EXCEPTION 'Identity matching verification failed';
    END IF;
END $$;
COMMIT;
