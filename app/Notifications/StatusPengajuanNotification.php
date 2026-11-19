<?php

namespace App\Notifications;

use App\Models\Pengajuan;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class StatusPengajuanNotification extends Notification
{
    use Queueable;

    public function __construct(
        public Pengajuan $pengajuan,
        public string $status
    ) {
    }

    /**
     * Tahap awal: notifikasi database.
     * Email dan WhatsApp Pandawa akan
     * dihubungkan setelah pengujian berhasil.
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $divisi = $this->pengajuan->divisi?->nama
            ?? 'Divisi yang ditentukan Admin';

        $pembimbing = $this->pengajuan->pembimbingRef?->nama
            ?? 'Pembimbing yang ditentukan Admin';

        $judul = '';
        $pesan = '';
        $jenis = 'info';

        switch ($this->status) {
            case 'diverifikasi':
                $judul = 'Berkas Magang Diverifikasi';
                $pesan = 'Berkas pengajuan magangmu '
                    . 'telah diverifikasi oleh Admin '
                    . 'BPS Kota Batu. Silakan menunggu '
                    . 'keputusan selanjutnya.';
                $jenis = 'info';
                break;

            case 'diterima':
                $judul = 'Pengajuan Magang Diterima';
                $pesan = 'Selamat! Pengajuan magangmu '
                    . 'telah diterima oleh BPS Kota Batu. '
                    . 'Penempatan: ' . $divisi
                    . '. Pembimbing: ' . $pembimbing
                    . '. Silakan lihat detail pengajuan '
                    . 'dan informasi penempatanmu.';
                $jenis = 'success';
                break;

            case 'ditolak':
                $judul = 'Pengajuan Magang Ditolak';

                $alasan = trim(
                    (string) $this->pengajuan->catatan_admin
                );

                $pesan = 'Pengajuan magangmu belum '
                    . 'dapat diterima oleh BPS Kota Batu. '
                    . 'Alasan: '
                    . ($alasan !== ''
                        ? $alasan
                        : 'Silakan hubungi Admin untuk '
                          . 'informasi lebih lanjut.');

                $jenis = 'danger';
                break;

            default:
                $judul = 'Pembaruan Pengajuan Magang';
                $pesan = 'Ada perubahan status '
                    . 'pada pengajuan magangmu.';
        }

        return [
            'type' => $jenis,
            'jenis' => 'status_pengajuan',
            'title' => $judul,
            'message' => $pesan,
            'pengajuan_id' => $this->pengajuan->id,
            'status' => $this->status,
            'catatan_admin' =>
                $this->pengajuan->catatan_admin,
            'url' => route('mahasiswa.dokumen'),
        ];
    }
}
