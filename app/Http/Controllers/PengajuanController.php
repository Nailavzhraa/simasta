<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Pengajuan;
use Illuminate\Support\Facades\Auth;

class PengajuanController extends Controller
{
    /**
     * Menampilkan status pengajuan mahasiswa.
     */
    public function index()
    {
        $pengajuan = Pengajuan::where(
            'user_id',
            Auth::id()
        )
        ->latest()
        ->first();

        return view(
            'mahasiswa.pengajuan',
            compact('pengajuan')
        );
    }

    /**
     * Pengiriman pengajuan dilakukan melalui
     * Mahasiswa\DokumenController.
     *
     * Route lama dipertahankan sementara agar
     * tidak membuat pengajuan ganda.
     */
    public function store()
    {
        return redirect()
            ->route('mahasiswa.dokumen')
            ->with(
                'info',
                'Silakan kirim pengajuan melalui halaman Dokumen Magang.'
            );
    }
}
