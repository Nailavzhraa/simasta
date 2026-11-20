# Deployment SIMASTA

Target: `https://simasta.bpskotabatu.com`, backend khusus `127.0.0.1:5013`, PostgreSQL `db_magang.simasta`. Presensi tetap di `https://presensi-simasta.bpskotabatu.com`, port 5014 dan folder lamanya.

## Instalasi awal - 18 November 2026

Staging server: `/home/bps/simasta-deploy-20261118/app`.
Runtime privat: `/home/bps/simasta-deploy-20261118/runtime`.
Installer: `deploy/install-server.sh`.

Instalasi pertama membutuhkan root untuk membuat akun sistem, role PostgreSQL, direktori `/var/www`, unit systemd, dan virtual host Nginx. Akun SSH `bps` meminta password untuk sudo. Jalankan di terminal SSH server setelah paket selesai diverifikasi:

```sh
sudo bash /home/bps/simasta-deploy-20261118/app/deploy/install-server.sh
```

Perintah ini tidak boleh dipakai ulang untuk memperbarui instalasi yang sudah ada; installer menolak menimpa schema, role, atau direktori produksi.

## Hasil yang dibuat installer

- Aplikasi: `/var/www/simasta.bpskotabatu.com`.
- Schema dan role baru: `simasta`, `simasta_app` di database `db_magang`.
- Service: `simasta-php.service` dan `simasta-web.service`, aktif otomatis saat boot jika seluruh smoke test berhasil.
- Konfigurasi: `/etc/simasta/php.ini`, `php-fpm.conf`, `nginx-app.conf`.
- Konfigurasi virtual host hanya untuk `simasta.bpskotabatu.com`.
- Session/cache/dokumen khusus SIMASTA.
- Backup origin dan snapshot struktur schema lain: `/var/backups/simasta/<waktu-UTC>/`.
- Admin awal dengan password acak pada `/etc/simasta/admin-first-login.txt`, hanya terbaca root.

Untuk melihat akun awal, jalankan sendiri di server:

```sh
sudo cat /etc/simasta/admin-first-login.txt
```

Jangan kirim password atau isi `.env` ke chat, repository, atau folder publik.

## Pemeriksaan setelah pemasangan

```sh
systemctl is-active simasta-php simasta-web presensi-web
curl -I http://127.0.0.1:5013/login
curl -I http://127.0.0.1:5014/
curl -I https://simasta.bpskotabatu.com/login
curl -I https://presensi-simasta.bpskotabatu.com/
```

Installer juga menguji login, seluruh halaman awal admin, logout, reset-password pages, penolakan akses `.env`, dan tidak tersedianya rute preview di produksi. Pengujian tidak mengirim email.

Perintah Artisan produksi perlu runtime privat dan identitas akun SIMASTA:

```sh
sudo -u simasta env PHP_INI_SCAN_DIR= \
  /var/www/simasta.bpskotabatu.com/runtime/usr/bin/php8.4 \
  -c /etc/simasta/php.ini \
  /var/www/simasta.bpskotabatu.com/artisan simasta:check-database
```

Log layanan tersedia melalui `journalctl -u simasta-php -u simasta-web`. Log aplikasi berada di `storage/logs` pada folder SIMASTA.

## Jika instalasi gagal

Simpan output error. Jika origin sudah dialihkan, trap installer memulihkan virtual host sebelumnya dan menghentikan dua service baru. Schema/role yang sudah dibuat tetap ada untuk diperiksa. Jangan menghapus schema atau menjalankan `migrate:fresh` pada database kantor untuk mengulang instalasi.

## Konfigurasi email dan Google

SMTP masih ditunda atas permintaan pengguna. `MAIL_MAILER=log` hanya mencatat email secara lokal; OTP dan reset password belum terkirim kepada pengguna.

Konfigurasi Google OAuth telah dipasang pada 20 November 2026 dengan callback `https://simasta.bpskotabatu.com/auth/google/callback`. Redirect otorisasi telah diverifikasi; login Google nyata sampai callback masih perlu dicoba pengguna. Kredensial disimpan di luar repository. Lihat [panduan Google OAuth](google-oauth.md).

## Riwayat aktivasi awal - 18 November 2026

Paket dan pengujian telah disiapkan pada 18 November 2026. Instalasi pertama telah dijalankan pengguna. Smoke test awal tidak menemukan token CSRF pada respons setelah reload origin; installer memulihkan routing lama dan menghentikan service baru. Folder, schema, dan akun yang sudah dibuat dipertahankan.

Halaman login dari source produksi telah diuji dan memiliki token CSRF. Konfigurasi backend sekarang menyertakan header `X-Simasta-App: simasta-5013`, dan aktivasi menunggu header tersebut setelah reload agar tidak menguji respons dari worker lama.

Untuk melanjutkan instalasi yang sudah terbentuk tanpa membuat ulang schema/akun, gunakan script aktivasi:

```sh
sudo bash /home/bps/simasta-deploy-20261118/app/deploy/activate-server.sh
```

Aktivasi lanjutan selesai dan diverifikasi pada 18 November 2026:

- `simasta-php.service`, `simasta-web.service`, dan `presensi-web.service` aktif dan enabled saat boot.
- SIMASTA mendengarkan hanya pada `127.0.0.1:5013`; presensi tetap `127.0.0.1:5014`.
- HTTPS publik `/`, `/login`, dan `/forgot-password` pada `simasta.bpskotabatu.com` menghasilkan HTTP 200 dengan header `X-Simasta-App: simasta-5013`.
- Domain `presensi-simasta.bpskotabatu.com` tetap menghasilkan HTTP 200.
- Aktivasi menyalakan otomatis service hanya setelah smoke test login/logout dan halaman admin serta pemeriksaan integritas presensi/schema lain berhasil.
- Konfigurasi database aplikasi memakai role baru `simasta_app` dan search path `simasta` pada `db_magang`.

Pada 20 November 2026, pembaruan GitHub, integrasi akun presensi, dan konfigurasi Google telah dipasang serta layanan produksi telah diverifikasi aktif. Rincian versi dan pemeriksaan terbaru tersedia di [catatan sinkronisasi](sinkronisasi-github.md). Jangan mengulang installer awal atau merge identitas pada database yang sudah terhubung. SMTP dan modul yang belum selesai tetap mengikuti batasan pada [laporan analisis](analisis-project.md).
