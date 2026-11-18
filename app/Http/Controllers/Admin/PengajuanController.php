<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Divisi;
use App\Models\Pembimbing;
use App\Models\Pengajuan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class PengajuanController extends Controller
{
    /**
     * Daftar pengajuan magang.
     */
    public function index(Request $request)
    {
        $query = Pengajuan::with('user.mahasiswa')
            ->latest();

        if ($request->filled('status')) {
            $query->where(
                'status',
                $request->input('status')
            );
        }

        if ($request->filled('q')) {
            $q = trim($request->input('q'));

            $query->where(function ($builder) use ($q) {
                $builder->whereHas('user', function ($user) use ($q) {
                    $user->where('name', 'like', "%{$q}%")
                        ->orWhere('email', 'like', "%{$q}%")
                        ->orWhereHas('mahasiswa', function ($mhs) use ($q) {
                            $mhs->where('nim', 'like', "%{$q}%")
                                ->orWhere(
                                    'perguruan_tinggi',
                                    'like',
                                    "%{$q}%"
                                );
                        });
                });
            });
        }

        return view('admin.pengajuan.index', [
            'daftar' => $query
                ->paginate(10)
                ->withQueryString(),

            'hitung' => [
                'semua' => Pengajuan::count(),

                'diproses' => Pengajuan::where(
                    'status',
                    'diproses'
                )->count(),

                'diverifikasi' => Pengajuan::where(
                    'status',
                    'diverifikasi'
                )->count(),

                // Dipertahankan untuk kompatibilitas
                // tampilan Admin yang lama.
                'perlu_revisi' => Pengajuan::where(
                    'status',
                    'perlu_revisi'
                )->count(),

                'diterima' => Pengajuan::where(
                    'status',
                    'diterima'
                )->count(),

                'ditolak' => Pengajuan::where(
                    'status',
                    'ditolak'
                )->count(),
            ],

            'divisis' => Divisi::orderBy('nama')->get(),

            'pembimbings' => Pembimbing::orderBy('nama')->get(),
        ]);
    }

    /**
     * Detail pengajuan.
     *
     * Informasi periode, durasi, dan link Google Drive
     * sudah tersedia melalui objek $pengajuan.
     */
    public function show(Pengajuan $pengajuan)
    {
        $pengajuan->load([
            'user.mahasiswa',
            'divisi',
            'timKerja',
            'pembimbingRef',
        ]);

        return view('admin.pengajuan.show', [
            'pengajuan' => $pengajuan,

            'divisis' => Divisi::orderBy('nama')->get(),

            'pembimbings' => Pembimbing::orderBy('nama')->get(),
        ]);
    }

    /**
     * Membuka dokumen pengajuan.
     *
     * Pengajuan baru:
     *   Mengarah ke tautan Google Drive/Docs.
     *
     * Pengajuan lama:
     *   Membuka PDF privat di penyimpanan lokal.
     *
     * Route ini wajib dilindungi middleware Admin.
     */
    public function dokumen(
        Request $request,
        Pengajuan $pengajuan,
        string $jenis
    ) {
        $kolom = [
            'cv' => [
                'link' => 'link_cv',
                'file' => 'file_cv',
            ],
            'surat' => [
                'link' => 'link_surat',
                'file' => 'file_surat',
            ],
            'proposal' => [
                'link' => 'link_proposal',
                'file' => 'file_proposal',
            ],
        ][$jenis] ?? null;

        abort_unless($kolom !== null, 404);

        // ======================================
        // 1. PRIORITAS DOKUMEN GOOGLE DRIVE
        // ======================================

        $link = trim(
            (string) ($pengajuan->{$kolom['link']} ?? '')
        );

        if ($link !== '') {
            abort_unless(
                $this->isValidGoogleDocumentUrl($link),
                403,
                'Tautan Google Drive tidak valid.'
            );

            // Buka tautan asli Google Drive.
            // Izin akses dokumen tetap diatur
            // oleh pemilik file di Google Drive.
            return redirect()->away($link);
        }

        // ======================================
        // 2. FALLBACK DOKUMEN PDF LAMA
        // ======================================

        $path = $pengajuan->{$kolom['file']};

        abort_unless(
            is_string($path) && $path !== '',
            404,
            'Dokumen belum tersedia.'
        );

        // Validasi lokasi dokumen sebelum
        // memeriksa atau membaca file.
        $prefix = 'dokumen-magang/'
            . $pengajuan->user_id
            . '/';

        abort_unless(
            str_starts_with($path, $prefix),
            403,
            'Lokasi dokumen tidak diizinkan.'
        );

        $relativePath = substr(
            $path,
            strlen($prefix)
        );

        abort_unless(
            $relativePath !== '' &&
            !str_contains($relativePath, '\\') &&
            !in_array('..', explode('/', $relativePath), true) &&
            !str_contains($relativePath, "\0"),
            403,
            'Path dokumen tidak valid.'
        );

        $disk = Storage::disk('local');

        abort_unless(
            $disk->exists($path),
            404,
            'Dokumen tidak ditemukan.'
        );

        $filename = $jenis
            . '-pengajuan-'
            . $pengajuan->id
            . '.pdf';

        // Download PDF lokal jika diminta.
        if ($request->boolean('download')) {
            return $disk->download(
                $path,
                $filename,
                [
                    'Content-Type' => 'application/pdf',
                ]
            );
        }

        // Preview PDF di browser Admin.
        return response()->file(
            $disk->path($path),
            [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' =>
                    'inline; filename="' . $filename . '"',

                'X-Content-Type-Options' => 'nosniff',

                'Cache-Control' => 'private, no-store',
            ]
        );
    }

    /**
     * Memastikan URL mengarah ke domain
     * Google Drive atau Google Docs.
     */
    private function isValidGoogleDocumentUrl(
        string $url
    ): bool {
        if (
            strlen($url) > 2048 ||
            !filter_var($url, FILTER_VALIDATE_URL)
        ) {
            return false;
        }

        $parts = parse_url($url);

        if ($parts === false) {
            return false;
        }

        $scheme = strtolower(
            $parts['scheme'] ?? ''
        );

        $host = strtolower(
            $parts['host'] ?? ''
        );

        return $scheme === 'https'
            && in_array($host, [
                'drive.google.com',
                'docs.google.com',
            ], true)
            && !isset($parts['user'])
            && !isset($parts['pass'])
            && !isset($parts['port']);
    }

    /**
     * Verifikasi berkas mahasiswa.
     */
    public function verifikasi(Pengajuan $pengajuan)
    {
        if ($pengajuan->status !== 'diproses') {
            return back()->with(
                'error',
                'Pengajuan ini tidak dapat diverifikasi.'
            );
        }

        $pengajuan->update([
            'status' => 'diverifikasi',
        ]);

        return back()->with(
            'success',
            'Berkas mahasiswa berhasil diverifikasi.'
        );
    }

    /**
     * Menerima pengajuan dan menentukan
     * penempatan mahasiswa.
     */
    public function terima(
        Request $request,
        Pengajuan $pengajuan
    ) {
        if (
            !in_array(
                $pengajuan->status,
                ['diproses', 'diverifikasi'],
                true
            )
        ) {
            return back()->with(
                'error',
                'Pengajuan ini tidak bisa diterima.'
            );
        }

        $data = $request->validate([
            'divisi_id' => [
                'required',
                'exists:divisis,id',
            ],

            'pembimbing_id' => [
                'required',
                'exists:pembimbings,id',
            ],

            'jobdesk' => [
                'required',
                'string',
                'max:2000',
            ],
        ], [
            'divisi_id.required' =>
                'Divisi penempatan wajib dipilih.',

            'pembimbing_id.required' =>
                'Pembimbing wajib dipilih.',

            'jobdesk.required' =>
                'Jobdesk wajib diisi.',
        ]);

        $divisi = Divisi::findOrFail(
            $data['divisi_id']
        );

        $pembimbing = Pembimbing::findOrFail(
            $data['pembimbing_id']
        );

        // Pembimbing harus sesuai divisi.
        if (
            $pembimbing->divisi_id &&
            (int) $pembimbing->divisi_id !==
            (int) $divisi->id
        ) {
            return back()->with(
                'error',
                'Pembimbing tidak berada di divisi yang dipilih.'
            );
        }

        $pengajuan->update([
            'divisi_id' => $divisi->id,

            'pembimbing_id' => $pembimbing->id,

            'jobdesk' => $data['jobdesk'],

            'bidang' => $divisi->nama,

            'pembimbing' => $pembimbing->nama,

            'status' => 'diterima',

            'catatan_admin' => null,
        ]);

        return redirect()
            ->route('admin.pengajuan.index')
            ->with(
                'success',
                "Pengajuan {$pengajuan->user->name} berhasil diterima."
            );
    }

    /**
     * Menolak pengajuan dengan catatan Admin.
     */
    public function tolak(
        Request $request,
        Pengajuan $pengajuan
    ) {
        if (
            !in_array(
                $pengajuan->status,
                ['diproses', 'diverifikasi'],
                true
            )
        ) {
            return back()->with(
                'error',
                'Pengajuan ini tidak bisa ditolak.'
            );
        }

        $data = $request->validate([
            'catatan_admin' => [
                'required',
                'string',
                'min:5',
                'max:1000',
            ],
        ], [
            'catatan_admin.required' =>
                'Alasan penolakan wajib diisi.',

            'catatan_admin.min' =>
                'Alasan penolakan minimal 5 karakter.',
        ]);

        $pengajuan->update([
            'status' => 'ditolak',

            'catatan_admin' =>
                $data['catatan_admin'],
        ]);

        return redirect()
            ->route('admin.pengajuan.index')
            ->with(
                'success',
                "Pengajuan {$pengajuan->user->name} berhasil ditolak."
            );
    }
}
