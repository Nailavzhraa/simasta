\set ON_ERROR_STOP on
GRANT CONNECT ON DATABASE db_magang TO :"presensi_role";
GRANT USAGE ON SCHEMA presensi TO :"presensi_role";
GRANT SELECT, INSERT, UPDATE, DELETE ON ALL TABLES IN SCHEMA presensi TO :"presensi_role";
GRANT USAGE, SELECT ON ALL SEQUENCES IN SCHEMA presensi TO :"presensi_role";
