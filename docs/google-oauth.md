# Google OAuth SIMASTA

Status 20 November 2026: JSON OAuth Web application dari pengguna sudah dipasang pada produksi. Redirect origin dan domain publik telah terverifikasi memuat client ID, callback produksi tepat, state, dan scopes login. JSON privat tetap pada `/home/bps/simasta-synced-20261120/google-oauth.json` dengan izin 0600. Pengguna masih perlu menyelesaikan satu login Google nyata untuk memastikan consent, Audience, dan callback. Jangan menyatakan login Google end-to-end sudah diuji sebelum langkah tersebut. Pemasangan gabungan tercatat pada `docs/sinkronisasi-github.md`.

1. Buka Google Cloud Console dan pilih proyek yang benar.
2. Pilih **Google Auth Platform → Clients**, lalu buka OAuth client bertipe **Web application**, atau buat client Web application bernama SIMASTA.
3. Tambahkan Authorized redirect URI berikut persis:

   `https://simasta.bpskotabatu.com/auth/google/callback`

4. Simpan tanpa menghapus URI/origin aplikasi lain. Unduh JSON client tersebut ke `deploy/state/google-oauth.json` di workspace. File di `deploy/state` diabaikan Git dan tidak dimasukkan ke paket source.
5. Jika client baru memerlukan konfigurasi Branding/Audience, lengkapi identitas aplikasi milik pengguna. Jika proyek masih dibatasi untuk pengujian, pastikan akun penguji diizinkan pada pengaturan Audience sebelum uji login.

Panduan resmi: [OAuth untuk aplikasi server](https://developers.google.com/identity/protocols/oauth2/web-server), [membuat client](https://developers.google.com/workspace/guides/create-credentials).

Pemasang `deploy/google/activate.sh` menerima path JSON privat di server, memeriksa tipe client dan callback, membuat backup `.env` root-only, memperbarui hanya tiga variabel Google, memperbarui cache konfigurasi, dan memeriksa redirect OAuth dari aplikasi. Kegagalan mengembalikan konfigurasi lama.

```bash
sudo bash /home/bps/simasta-synced-20261120/simasta/deploy/google/activate.sh /home/bps/simasta-synced-20261120/google-oauth.json
```

Perintah tersebut sudah dijalankan untuk konfigurasi saat ini. Untuk perubahan konfigurasi berikutnya, jalankan hanya setelah installer dan JSON yang sudah diperiksa berada di lokasi tersebut. Jangan menempel client secret ke chat atau menjalankannya sebagai argumen shell. Jangan mencetak file JSON atau `.env` pada output bersama.

Setelah redirect aplikasi terbukti benar, pengguna perlu menyelesaikan satu login Google nyata melalui browser. Pemeriksaan ini memastikan consent, pembatasan Audience, dan callback berhasil. Login peserta ke presensi tetap memerlukan pengajuan terbaru yang diterima di SIMASTA.

Konfigurasi ini tidak mengaktifkan SMTP. Google Sign-In memverifikasi identitas Google; OTP/reset akun melalui email tetap memerlukan layanan pengiriman email sendiri.
