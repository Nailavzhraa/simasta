<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pengajuan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class NotifikasiController extends Controller
{
    /**
     * Hanya pemilik notifikasi yang dapat membuka dan menandainya dibaca.
     * POST digunakan karena status read_at berubah.
     */
    public function buka(Request $request, string $notificationId): RedirectResponse
    {
        $notif = $request->user()
            ->notifications()
            ->whereKey($notificationId)
            ->firstOrFail();

        $notif->markAsRead();

        // Tujuan dibangun dari ID pengajuan, bukan URL bebas dari JSON.
        $pengajuanId = data_get($notif->data, 'pengajuan_id');
        if (is_numeric($pengajuanId)) {
            $pengajuan = Pengajuan::query()->find((int) $pengajuanId);
            if ($pengajuan) {
                return redirect()->route('admin.pengajuan.show', $pengajuan);
            }
        }

        return redirect()->route('admin.pengajuan.index');
    }

    /** Tandai seluruh notifikasi Admin yang login sebagai dibaca. */
    public function bacaSemua(Request $request): RedirectResponse
    {
        $request->user()->unreadNotifications()->update([
            'read_at' => now(),
        ]);

        return back()->with('success', 'Semua notifikasi telah ditandai dibaca.');
    }
}
