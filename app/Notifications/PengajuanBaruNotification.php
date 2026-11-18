<?php

namespace App\Notifications;

use App\Models\Pengajuan;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class PengajuanBaruNotification extends Notification
{
    use Queueable;

    public function __construct(
        public Pengajuan $pengajuan,
        public string $namaMahasiswa
    ) {
    }

    /**
     * Simpan notifikasi ke database SIMASTA.
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Data notifikasi untuk Admin.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'pengajuan_baru',

            'title' => 'Pengajuan Magang Baru',

            'message' => $this->namaMahasiswa
                . ' telah mengirim pengajuan magang '
                . 'dan menunggu pemeriksaan Admin.',

            'pengajuan_id' => $this->pengajuan->id,

            'user_id' => $this->pengajuan->user_id,

            'status' => 'diproses',

            'url' => route(
                'admin.pengajuan.show',
                $this->pengajuan->id
            ),
        ];
    }
}
