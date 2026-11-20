# Sinkronisasi GitHub dan deployment — 20 November 2026

Repo: `https://github.com/Nailavzhraa/simasta.git`.
Branch kerja lokal: `integration/github-db-deploy-20261120`.
Upstream terbaru yang digabungkan: `edafc2a` (sebelumnya `e7b98df`).

Dua commit upstream `5f449da` dan `edafc2a` sudah masuk lokal melalui fast-forward. Perubahan lokal database PostgreSQL, deployment, autentikasi bersama presensi, dan pengujian dipulihkan di atas versi tersebut. Hasil integrasi disiapkan untuk commit dan push ke branch kerja tersebut dengan identitas Naila. Branch `main` tidak diubah oleh proses pengiriman ini; status pengiriman dapat diperiksa pada riwayat branch di GitHub.

Sebelum penggabungan, patch perubahan tracked dan arsip 48 file source untracked disimpan pada `deploy/state/sync-20261120/`. Stash keselamatan juga dipertahankan. Kredensial OAuth, `.env`, dan artifact privat diabaikan Git.

## Penyesuaian integrasi

- Mempertahankan tampilan dan controller notifikasi/pengajuan terbaru dari GitHub.
- Menambahkan rute notifikasi mahasiswa yang dirujuk komponen baru.
- Menghubungkan layout mahasiswa ke notifikasi database milik pengguna, menggantikan lonceng data contoh pada layout tersebut.
- Memberi nama rute logout agar tombol Keluar pada menu akun baru berfungsi.
- Mempertahankan sumber akun utama SIMASTA, gate pengajuan terbaru `diterima`, penonaktifan akun, dukungan hash presensi lama, dan pencabutan sesi.
- Menambahkan migrasi notifikasi ke paket deployment. Total migrasi menjadi 28; installer menjalankan migrasi yang belum diterapkan tanpa menghapus database.
- Paket memasukkan aset Vite yang sudah dibangun. Baseline dan backup mencakup source GitHub baru serta file aset yang dipasang.

## Pemeriksaan

- `npm run build`: berhasil.
- Laravel: 20 tes, 141 assertion, berhasil pada PostgreSQL.
- Backend presensi: 72 tes, berhasil pada database terisolasi dengan role runtime khusus.
- Smoke test backend dengan feature flag integrasi aktif: berhasil.
- PostgreSQL 16: 28 migrasi, pengulangan migrasi, cache produksi, dan sentinel schema lain berhasil. Schema SIMASTA memiliki 22 tabel sebelum integrasi lintas schema.
- JSON Google: tipe Web application dan callback produksi valid. Secret tidak ditampilkan.

Tes notifikasi memeriksa penerimaan pengajuan, pencegahan notifikasi ganda saat tombol ditekan ulang, penampilan notifikasi pada layout, pembukaan/penandaan hanya oleh pemilik, serta pengabaian URL eksternal yang disisipkan pada data notifikasi.

## Paket server

Staging baru: `/home/bps/simasta-synced-20261120`.
Source: `/home/bps/simasta-synced-20261120/simasta`.
OAuth JSON privat: `/home/bps/simasta-synced-20261120/google-oauth.json`.
Log pengujian: `synced-qa.log`, `migrations-qa.log` pada folder staging.

**Status akhir: produksi aktif.** Penuntasan berhasil pada 20 November 2026. Keempat layanan aktif; kedua domain login HTTP200; pendaftaran akun terpisah presensi ditolak HTTP409. Redirect Google origin/publik terverifikasi benar dan aset presensi publik cocok dengan versi integrasi. Ada 15 akun di kedua aplikasi, 0 tanpa pemetaan, 13 peserta belum disetujui dan 0 sudah diterima. Google consent/callback melalui login nyata masih perlu dicoba pengguna. Source integrasi disiapkan pada branch kerja di atas untuk dikirim ke GitHub.

**Riwayat insiden aktivasi (sudah ditangani).** Source serta dua migrasi baru berhasil terpasang, kemudian installer sempat berhenti pada perbandingan dump riwayat secara mentah. Layanan autentikasi sempat dihentikan sampai pemeriksaan selesai. Backup privat: `/var/backups/simasta/identity-20261120T085029Z`.

Jangan ulangi installer merge. Pembanding dump baru memeriksa seluruh nilai/jumlah rekaman sambil mengabaikan urutan baris/COPY, tanpa mencetak data pribadi. Sudah lulus tes perubahan nilai/duplikasi dan pengujian dump PostgreSQL asli. Layanan web SIMASTA juga dinyalakan secara eksplisit karena `Requires=simasta-php` membuatnya ikut berhenti saat PHP dihentikan.

Perintah penuntasan yang telah berhasil dijalankan, disimpan sebagai riwayat pemulihan:

```bash
sudo bash /home/bps/simasta-synced-20261120/simasta/deploy/identity/finish-release.sh /var/backups/simasta/identity-20261120T085029Z
```

Script memverifikasi snapshot awal/akhir dan riwayat saat ini, schema lain, pemetaan akun, serta source terpasang; kemudian mengoptimalkan cache, menyalakan layanan, menguji login bersama, dan melanjutkan konfigurasi Google. Jika ada perbedaan isi data, script berhenti. Hasil script dan kedua domain telah diverifikasi setelah pemulihan.

Pemeriksaan lanjutan menemukan `presensi.notifications` bertambah 1→2 karena trigger presensi `notify_new_admin` otomatis memberi admin SIMASTA baru salinan antrean permohonan yang masih menunggu. Pembanding kini memverifikasi snapshot profil sebelum/sesudah dan isi permohonan lama; hanya tambahan persis sesuai trigger tersebut diterima. Setiap rekaman lama wajib tetap identik. Kondisi ini telah direproduksi pada PostgreSQL sementara dan lulus bersama seluruh 20/72 tes. Script finish-release di server sudah diperbarui; jangan mengulang merge.

Perintah pemasangan awal berikut disimpan sebagai referensi; **sudah dijalankan, jangan diulang pada database yang kini terhubung**:

```bash
sudo bash /home/bps/simasta-synced-20261120/simasta/deploy/activate-release.sh /home/bps/simasta-synced-20261120/google-oauth.json
```

Perintah memeriksa konfigurasi Google lebih dahulu, lalu memasang source gabungan dan integrasi identitas dengan backup, kemudian mengaktifkan konfigurasi Google dan memeriksa redirect. Schema serta konfigurasi aplikasi lain dipertahankan. Port tetap SIMASTA 5013 dan presensi 5014. Layanan autentikasi terkait akan berhenti sementara selama aktivasi; semua sesi lama perlu login ulang.

Setelah pemasangan, verifikasi hasil akhir installer serta kedua domain. Selesaikan satu login Google nyata untuk memastikan pengaturan Audience/consent dan callback di Google Console. Peserta lama tanpa persetujuan asli di SIMASTA tetap perlu mengikuti alur pengajuan; installer tidak membuat persetujuan otomatis. SMTP masih belum dikonfigurasi.
