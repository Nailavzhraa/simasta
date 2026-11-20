# Analisis SIMASTA — 18 November 2026

## Ruang lingkup dan kondisi awal

Project ini adalah aplikasi manajemen magang Laravel, terpisah secara fungsi dari aplikasi presensi. Pemeriksaan mencakup source, rute, controller, model, migration, template, konfigurasi, dependency, pengujian, dan konfigurasi origin server. Ini bukan audit penetrasi atau jaminan bahwa seluruh proses bisnis sudah selesai.

Source memiliki 20 controller, 13 model, 26 migration, dan 39 template Blade. `composer.json` meminta PHP 8.3+ dan Laravel 13. Frontend memakai Blade, CSS/JavaScript, serta Vite/Tailwind. Tidak ada frontend SPA terpisah. Repository awal masih memakai README bawaan Laravel dan memiliki penanda konflik merge di `.gitignore`.

Konfigurasi lokal memakai SQLite. PostgreSQL tersedia pada server `192.168.100.10` dengan database `db_magang`. Search path awal Laravel adalah `public`, sehingga belum cocok untuk pemisahan aplikasi di database bersama.

## Hasil pemeriksaan server

Server `server-web` menyediakan Ubuntu, PostgreSQL 16, Nginx 1.24, PHP 8.3/8.4-FPM, dan sejumlah aplikasi lain. Akun `bps` dapat diakses melalui SSH dengan kunci deployment yang pengguna izinkan. Penggunaan `sudo` membutuhkan password; SSH yang berhasil tidak otomatis memberikan akses root tanpa password.

Sebelum perubahan, dua virtual host berikut sama-sama meneruskan request ke port 5014:

| Hostname | Origin awal | Folder aplikasi awal |
| --- | --- | --- |
| `simasta.bpskotabatu.com` | `localhost:5014` | `/var/www/presensi.simasta.bpskotabatu.com/public` |
| `presensi-simasta.bpskotabatu.com` | `localhost:5014` | `/var/www/presensi.simasta.bpskotabatu.com/public` |

Jadi dugaan bahwa SIMASTA menampilkan file presensi terbukti dari virtual host dan konfigurasi service `presensi-web`. Hostname dengan titik, `presensi.simasta.bpskotabatu.com`, tidak ditemukan pada DNS saat pemeriksaan; pengguna memilih mempertahankan hostname dengan tanda hubung.

Target baru yang disetujui:

| Aplikasi | Hostname | Port internal | Direktori |
| --- | --- | --- | --- |
| SIMASTA | `simasta.bpskotabatu.com` | `127.0.0.1:5013` | `/var/www/simasta.bpskotabatu.com` |
| Presensi | `presensi-simasta.bpskotabatu.com` | `127.0.0.1:5014` | `/var/www/presensi.simasta.bpskotabatu.com` |

## Alur aplikasi dan status modul

1. Pengunjung membuka beranda, melakukan registrasi, lalu memverifikasi OTP email. Login password dan integrasi Google tersedia pada source.
2. Mahasiswa melengkapi profil, identitas akademik, foto, dan pembimbing kampus. Middleware membatasi fitur sampai profil lengkap.
3. Form pengajuan menyimpan draft terpisah dari pengajuan resmi. Pengiriman final menyimpan periode, tujuan magang, serta tautan dokumen.
4. Admin memeriksa, memverifikasi, menerima, atau menolak pengajuan. Penempatan mengaitkan divisi, tim kerja, pembimbing, dan jobdesk.
5. Fase pelaksanaan membedakan belum mulai, berlangsung, dan selesai. Mahasiswa yang diterima dapat mengisi logbook dan mengirim template penilaian; admin meninjau logbook dan mengirim hasil penilaian.
6. Presensi tetap dilayani aplikasi lain. Rute presensi SIMASTA mengalihkan pengguna ke hostname presensi yang aktif; ini tidak membuat SSO, sinkronisasi akun, atau relasi silang antar-schema.

| Modul | Hasil pemeriksaan |
| --- | --- |
| Beranda, login, registrasi, OTP, reset password | Source tersedia; halaman diuji. Pengiriman email asli belum dikonfigurasi. |
| Google OAuth | Implementasi dan tes mock tersedia; kredensial produksi belum disiapkan. |
| Profil, draft, pengajuan, keputusan admin | Implementasi tersedia. Rute pengajuan lama sekarang mengarah ke form yang aktif. |
| Divisi, tim kerja, pembimbing, penempatan | CRUD dan relasi tersedia; halaman awal admin diuji. |
| Logbook dan penilaian berkas | Implementasi tersedia; halaman mahasiswa diterima dan admin diuji. |
| Pembimbing | Rute dashboard sudah diperbaiki; isi dashboard masih angka contoh dan belum menyediakan alur pembimbing lengkap. |
| Laporan akhir | Model dan controller tersedia. Template `mahasiswa.laporan` dan `admin.laporan.show` belum ada. Rute pengiriman laporan mahasiswa belum terpasang. Halaman daftar/rekap admin tersedia. |
| Sertifikat | Tabel, model, dan tampilan status tersedia; generator/penerbitan sertifikat lengkap belum terhubung melalui rute yang ada. |

## Database dan relasi

Seluruh tabel aplikasi ditujukan ke `db_magang.simasta`, menggunakan `DB_CONNECTION=pgsql` dan `DB_SCHEMA=simasta`, tanpa fallback ke `public`. Akun produksi baru bernama `simasta_app`, pemilik schema `simasta`, tanpa hak superuser, membuat database, atau membuat role. Izin bawaan PostgreSQL pada schema lain tidak diubah.

Hasil migration PostgreSQL: 21 tabel, termasuk tabel pelacakan migration.

| Kelompok | Tabel |
| --- | --- |
| Akun dan autentikasi | `users`, `mahasiswas`, `email_otps`, `password_reset_tokens`, `sessions` |
| Proses magang | `pengajuans`, `pengajuan_drafts`, `divisis`, `tim_kerjas`, `pembimbings`, `logbooks` |
| Hasil magang | `penilaian_berkas`, `sertifikat_templates`, `sertifikats`, `laporan_magangs` |
| Infrastruktur Laravel | `cache`, `cache_locks`, `jobs`, `job_batches`, `failed_jobs`, `migrations` |

`users` menghubungkan profil mahasiswa, OTP, pengajuan, logbook, penilaian, sertifikat, serta laporan. Pengajuan menghubungkan penempatan divisi/tim kerja/pembimbing. Penilaian dan laporan memakai pengajuan sebagai unit proses. Foreign key hasil uji PostgreSQL seluruhnya tetap berada dalam schema `simasta`.

Data SQLite pengembangan tidak dimasukkan ke paket deployment dan tidak otomatis diimpor. Instalasi baru menyiapkan struktur, enam divisi awal, dan satu admin dengan password acak; tidak menyalin akun contoh berpassword tetap.

## Masalah yang diperbaiki

- Migration `password_reset_tokens` ganda menyebabkan instalasi database baru gagal. Migration lanjutan sekarang memeriksa keberadaan tabel dan tidak menghapus tabel milik migration awal ketika rollback.
- PostgreSQL menolak perubahan kolom `semester` dari teks menjadi angka secara implisit. Migration sekarang memberi cast eksplisit, mempertahankan angka yang valid, dan menolak nilai lama yang tidak dapat dikonversi.
- Rute reset password berada di blok `APP_ENV=local`, sehingga hilang di produksi. Rute dipindahkan keluar dari blok preview.
- Login pembimbing mengarah ke dashboard yang belum memiliki rute. Rute dan tujuan login telah diselaraskan.
- Rute presensi menunjuk template yang tidak ada. Rute sekarang mengarah ke aplikasi presensi terpisah.
- Rute pengajuan lama menunjuk template yang tidak ada. Rute sekarang mengarah ke halaman Dokumen Magang yang aktif.
- Relasi pengajuan pada `User` sebelumnya dapat membaca pengajuan paling lama. Relasi sekarang memakai pengajuan terbaru, termasuk ketika pengajuan lama ditolak dan pengajuan baru diterima.
- Seeder akun contoh dibatasi di luar produksi dan dibuat idempoten.
- Penanda konflik merge `.gitignore` dibersihkan; direktori paket/private state deployment dikecualikan dari Git.

## Konfigurasi deployment

Paket `deploy/` menyediakan service Nginx port 5013, PHP-FPM khusus SIMASTA, virtual host origin, konfigurasi produksi, pembentukan schema/role, admin awal, backup virtual host, dan smoke test login/logout. Runtime PHP privat diekstrak dari paket resmi repository Ubuntu/PPA yang sudah dikonfigurasi pada server. Runtime tersebut tidak mengganti PHP global atau merestart service presensi.

Session menggunakan cookie `simasta_session` khusus host dengan Secure, HttpOnly, SameSite Lax, dan payload terenkripsi. Penyimpanan foto publik terpisah dari dokumen privat. `.env`, database SQLite, log lokal, `node_modules`, serta file Vite `public/hot` tidak dikirim sebagai source produksi.

Installer menolak target/schema/role yang sudah ada dan port yang sudah digunakan. Sebelum perubahan origin, installer memeriksa backend baru. Jika pengujian setelah pengalihan gagal, konfigurasi origin lama dipulihkan. Schema baru yang sudah dibuat dipertahankan untuk diagnosis; installer tidak menghapus database atau mencoba mengulang instalasi secara destruktif.

## Validasi dan batasan

- `npm run build`: berhasil.
- Test suite lokal SQLite: 11 tes, 90 assertion, lulus.
- Test suite Linux dengan PHP privat dan PostgreSQL 16: dijalankan dalam cluster sementara dengan role aplikasi terbatas.
- Seluruh 26 migration berhasil; eksekusi ulang tidak menambahkan migration baru.
- Konfigurasi schema yang salah ditolak oleh `simasta:check-database`.
- Pembuatan dan pembersihan cache konfigurasi, rute, event, dan Blade berhasil di mode produksi.
- Nginx aplikasi dan PHP-FPM khusus lolos pemeriksaan sintaks.
- Sentinel pada schema `public` dan `presensi` tidak berubah selama pengujian; tidak ada foreign key SIMASTA menuju schema lain.
- Pengujian di atas tidak mengirim email, tidak login Google sungguhan, dan tidak memakai data pribadi produksi.

Pengguna menunda SMTP dan Google OAuth. Karena itu OTP dan tautan reset hanya dicatat dalam log sampai email dikonfigurasi; alur registrasi belum dapat diselesaikan melalui email sungguhan. Tidak ada bypass verifikasi email yang ditambahkan.

Produksi aktif dan domain diperiksa pada 18 November 2026: SIMASTA di 5013 dan presensi tetap di 5014. Kedua service SIMASTA serta service presensi aktif dan enabled saat boot. Halaman publik SIMASTA menghasilkan HTTP 200 dengan penanda backend SIMASTA; domain presensi juga HTTP 200. Detail aktivasi dan pemulihan smoke test awal dicatat pada [panduan deployment](deployment.md). Fitur laporan akhir, generator sertifikat, dan dashboard pembimbing lengkap memerlukan pekerjaan lanjutan.
