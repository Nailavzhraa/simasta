# Integrasi akun SIMASTA dan presensi

SIMASTA menyimpan identitas utama pada `db_magang.simasta.users`. Presensi tetap memiliki schema, folder, layanan, UUID peserta, dan riwayatnya sendiri. Kolom `presensi.users.simasta_user_id` menghubungkan kedua aplikasi tanpa mengganti UUID lama.

## Aturan akses

- Daftar akun melalui SIMASTA. Form daftar pada web presensi mengarah ke SIMASTA; API daftar presensi menolak pembuatan akun terpisah.
- Login kedua aplikasi memakai email dan password yang sama. Password presensi lama berformat scrypt tetap didukung secara utuh; perubahan password baru menggunakan bcrypt maksimal 72 byte.
- Peserta memerlukan akun aktif, verifikasi akun (atau identitas presensi lama yang tercatat saat migrasi), dan pengajuan magang **terbaru** berstatus `diterima` di SIMASTA.
- Admin memakai role admin yang dipetakan dari identitas utama. Role pembimbing tidak otomatis memperoleh akses peserta presensi.
- Perubahan password/email/role/status aktif atau perubahan status pengajuan mencabut sesi terkait. Aplikasi tetap memeriksa kelayakan pada setiap permintaan API.
- Login Google presensi harus sesuai dengan Google subject yang sudah terhubung di SIMASTA dan tetap mengikuti syarat penerimaan magang.
- Kedua aplikasi memiliki sesi login sendiri. Kesamaan akun tidak menyatukan cookie login lintas domain.

Data presensi lama diimpor sebagai akun utama tanpa membuat pengajuan atau menyatakan peserta diterima secara otomatis. Peserta lama yang belum mempunyai persetujuan asli perlu mengikuti alur pengajuan SIMASTA. Riwayat kehadirannya tetap tersimpan.

## Aktivasi di server

Integrasi telah aktif dan diverifikasi pada 20 November 2026 menggunakan paket `/home/bps/simasta-synced-20261120`. Lihat [catatan sinkronisasi](sinkronisasi-github.md) untuk status produksi dan riwayat pemulihan. Perintah aktivasi awal berikut disimpan sebagai referensi historis; jangan dijalankan ulang pada database yang sudah terhubung:

```bash
sudo bash /home/bps/simasta-identity-20261118/simasta/deploy/identity/activate.sh
```

Installer memeriksa baseline kode, membuat backup privat kedua schema dan file terkait, memasang role `presensi_identity_app`, menerapkan pemetaan/trigger, memperbarui aplikasi, lalu menjalankan pemeriksaan login. Role database bersama `magang` tidak diberi akses ke view kredensial utama. Konfigurasi Google/mail yang sudah ada dipertahankan.

SIMASTA tetap pada port 5013 dan presensi pada 5014; backend API presensi tetap 5010. Installer membandingkan data riwayat presensi serta definisi schema lain sebelum/sesudah. Semua sesi lama perlu login ulang.

Pemeriksaan manual setelah aktivasi:

```bash
sudo -u postgres psql -X -d db_magang < /home/bps/simasta-synced-20261120/simasta/deploy/identity/verify.sql
sudo bash -c 'node /home/bps/simasta-synced-20261120/simasta/deploy/identity/verify-live.mjs < /etc/simasta/admin-bootstrap.json'
```

Perintah kedua memakai kredensial admin bootstrap yang tersimpan privat. Jika admin telah mengganti password sejak bootstrap, perbarui metode smoke test secara aman; jangan mengubah password pengguna untuk membuat tes lulus.

## Kegagalan dan pemulihan

Backup berada di `/var/backups/simasta/identity-<waktu-UTC>`, hanya root yang dapat membukanya. Lokasi aktivasi sukses disimpan pada `/etc/simasta/identity-backup-path`.

Jika gagal sebelum transaksi pemetaan tersimpan, installer mengembalikan kode/config semula dan menyalakan layanan. Kolom status tambahan yang sudah dimigrasikan aman dibiarkan.

Jika transaksi pemetaan sudah tersimpan tetapi pemeriksaan berikutnya gagal, installer menghentikan layanan autentikasi yang terkait dan menyimpan diagnostik. Perbaiki penyebabnya lalu lanjutkan kode integrasi; jangan langsung mengaktifkan kode autentikasi lama karena password lokal presensi sudah menjadi marker `!simasta-managed`. Pemulihan database penuh memerlukan pemeriksaan aktivitas sesudah backup agar perubahan pengguna tidak hilang. Jangan menjalankan kembali merge pada database yang sudah terhubung.

## Verifikasi pengembangan

`deploy/identity/qa.sh` membangun cluster PostgreSQL sementara dengan role pemilik presensi lama dan role runtime baru yang terpisah. Script menjalankan tes Laravel, tes backend Node, verifikasi izin database, serta login melalui backend yang dimulai dengan konfigurasi integrasi produksi. Fixture pengajuan diterima hanya dibuat dalam cluster sementara.

Hasil versi gabungan GitHub pada 20 November 2026: 20 tes Laravel / 141 assertion, 72 tes Node, dan smoke test backend berhasil. Cakupannya meliputi akun lama, akun baru, password dua arah, akun nonaktif, Google subject/nonce, persetujuan dicabut, pengajuan terbaru yang belum diterima, pembatasan role database, riwayat presensi tetap utuh, notifikasi penerimaan tanpa duplikasi, serta pembatasan notifikasi berdasarkan pemilik. Seluruh 28 migrasi PostgreSQL juga lulus, termasuk pengulangan migrasi, cache produksi, dan isolasi schema.

Paket lama 18 November digantikan paket gabungan `/home/bps/simasta-synced-20261120`. Pembaruan GitHub, integrasi akun, dan konfigurasi Google sudah terpasang; lihat `docs/sinkronisasi-github.md` untuk hasil verifikasi. Jangan ulangi merge identitas.

Google OAuth SIMASTA memerlukan client ID dan client secret milik proyek pengguna. Callback yang dikonfigurasi aplikasi: `https://simasta.bpskotabatu.com/auth/google/callback`. SMTP masih belum dikonfigurasi; registrasi email/password dan reset melalui email memerlukan konfigurasi pengiriman email sebelum dapat digunakan mandiri oleh pengguna umum.
