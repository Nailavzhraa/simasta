\set ON_ERROR_STOP on
BEGIN READ ONLY;
SELECT 'simasta_accounts' AS metric, count(*) AS value FROM simasta.users
UNION ALL SELECT 'presensi_accounts', count(*) FROM presensi.users
UNION ALL SELECT 'same_email', count(*) FROM simasta.users s JOIN presensi.users p ON lower(btrim(s.email))=p.email
UNION ALL SELECT 'presensi_to_import', count(*) FROM presensi.users p WHERE NOT EXISTS (SELECT 1 FROM simasta.users s WHERE lower(btrim(s.email))=p.email)
UNION ALL SELECT 'simasta_to_provision', count(*) FROM simasta.users s WHERE NOT EXISTS (SELECT 1 FROM presensi.users p WHERE p.email=lower(btrim(s.email)))
UNION ALL SELECT 'duplicate_simasta_normalized_email', count(*) FROM (SELECT lower(btrim(email)) FROM simasta.users GROUP BY 1 HAVING count(*)>1) d
UNION ALL SELECT 'role_conflicts', count(*) FROM simasta.users s JOIN presensi.users p ON lower(btrim(s.email))=p.email JOIN presensi.profiles r ON r.id=p.id WHERE r.role <> CASE WHEN s.role='admin' THEN 'admin' ELSE 'peserta' END
UNION ALL SELECT 'password_conflicts', count(*) FROM simasta.users s JOIN presensi.users p ON lower(btrim(s.email))=p.email WHERE s.password<>p.password_hash
UNION ALL SELECT 'unverified_existing_simasta_match', count(*) FROM simasta.users s JOIN presensi.users p ON lower(btrim(s.email))=p.email WHERE s.email_verified_at IS NULL
UNION ALL SELECT 'google_subject_conflicts', count(*) FROM simasta.users s JOIN presensi.users p ON lower(btrim(s.email))=p.email JOIN presensi.google_identities g ON g.user_id=p.id WHERE s.google_id IS NOT NULL AND s.google_id<>g.subject;
SELECT CASE WHEN password_hash LIKE 'scrypt$%' THEN 'scrypt'
    WHEN password_hash='!google-only' THEN 'google_only'
    WHEN password_hash LIKE '$2%' THEN 'bcrypt' ELSE 'unsupported' END AS password_format,
    count(*) FROM presensi.users GROUP BY 1 ORDER BY 1;
COMMIT;
