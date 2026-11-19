<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class NotifikasiController extends Controller
{
    /**
     * Buka satu notifikasi milik mahasiswa
     * dan tandai sebagai sudah dibaca.
     */
    public function buka(
        Request $request,
        string $notificationId
    ): RedirectResponse {
        $notifikasi = $request->user()
            ->notifications()
            ->whereKey($notificationId)
            ->firstOrFail();

        $notifikasi->markAsRead();

        // Tujuan dibuat tetap agar tidak bisa
        // diarahkan ke URL asing dari data notifikasi.
        return redirect()->route('mahasiswa.dokumen');
    }

    /**
     * Tandai seluruh notifikasi milik mahasiswa
     * yang sedang login sebagai sudah dibaca.
     */
    public function bacaSemua(
        Request $request
    ): RedirectResponse {
        $request->user()
            ->unreadNotifications()
            ->update([
                'read_at' => now(),
            ]);

        return back()->with(
            'success',
            'Semua notifikasi berhasil ditandai dibaca.'
        );
    }
}
