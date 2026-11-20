\set ON_ERROR_STOP on
BEGIN READ ONLY;
DO $$ BEGIN
    IF EXISTS (SELECT 1 FROM simasta.users s LEFT JOIN presensi.users p ON p.simasta_user_id=s.id WHERE p.id IS NULL)
       OR EXISTS (SELECT 1 FROM presensi.users p LEFT JOIN simasta.users s ON s.id=p.simasta_user_id
           LEFT JOIN presensi.profiles r ON r.id=p.id WHERE s.id IS NULL OR r.id IS NULL
           OR p.email<>s.email OR p.active<>s.is_active OR r.nama_lengkap<>s.name
           OR p.password_hash<>'!simasta-managed'
           OR r.role<>CASE WHEN s.role='admin' THEN 'admin' ELSE 'peserta' END) THEN
        RAISE EXCEPTION 'Identity synchronization check failed';
    END IF;
    IF has_table_privilege('presensi_identity_app','simasta.users','SELECT')
       OR NOT has_table_privilege('presensi_identity_app','simasta.presensi_accounts','SELECT')
       OR has_table_privilege('magang','simasta.presensi_accounts','SELECT') THEN
        RAISE EXCEPTION 'Unexpected central identity permissions';
    END IF;
    IF EXISTS (SELECT 1 FROM simasta.presensi_accounts WHERE role<>'admin' AND can_authenticate
        AND (role<>'mahasiswa' OR magang_status IS DISTINCT FROM 'diterima')) THEN
        RAISE EXCEPTION 'Participant access does not follow internship acceptance';
    END IF;
END $$;
SELECT 'simasta_accounts' AS metric,count(*) AS value FROM simasta.users
UNION ALL SELECT 'linked_presensi_accounts',count(*) FROM presensi.users
UNION ALL SELECT 'accepted_participants',count(*) FROM simasta.presensi_accounts WHERE role='mahasiswa' AND can_authenticate AND is_active
UNION ALL SELECT 'participants_waiting_approval',count(*) FROM simasta.presensi_accounts WHERE role='mahasiswa' AND NOT can_authenticate
UNION ALL SELECT 'unlinked_presensi_accounts',count(*) FROM presensi.users WHERE simasta_user_id IS NULL;
COMMIT;
