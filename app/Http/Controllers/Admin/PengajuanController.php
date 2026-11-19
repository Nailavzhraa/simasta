<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Divisi;
use App\Models\Pembimbing;
use App\Models\Pengajuan;
use App\Notifications\StatusPengajuanNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PengajuanController extends Controller
{
    /**
     * Menampilkan daftar pengajuan magang.
     */
    public function index(Request $request)
    {
        $query = Pengajuan::query()
            ->with('user.mahasiswa')
            ->latest();

        if ($request->filled('status')) {
            $query->where(
                'status',
                $request->input('status')
            );
        }

        if ($request->filled('q')) {
            $q = trim((string) $request->input('q'));

            $query->where(function ($builder) use ($q) {
                $builder->whereHas('user', function ($user) use ($q) {
                    $user->where(
                        'name',
                        'like',
                        "%{$q}%"
                    )
                    ->orWhere(
                        'email',
                        'like',
                        "%{$q}%"
                    )
                    ->orWhereHas(
                        'mahasiswa',
                        function ($mhs) use ($q) {
                            $mhs->where(
                                'nim',
                                'like',
                                "%{$q}%"
                            )
                            ->orWhere(
                                'perguruan_tinggi',
                                'like',
                                "%{$q}%"
                            );
                        }
                    );
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
     * Menampilkan detail pengajuan.
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
     * Dokumen baru menggunakan Google Drive.
     * Dokumen lama tetap menggunakan PDF lokal.
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

        /*
         * Prioritas: link Google Drive.
         */
        $link = trim(
            (string) ($pengajuan->{$kolom['link']} ?? '')
        );

        if ($link !== '') {
            abort_unless(
                $this->isValidGoogleDocumentUrl($link),
                403,
                'Tautan Google Drive tidak valid.'
            );

            return redirect()->away($link);
        }

        /*
         * Fallback: dokumen PDF lama.
         */
        $path = $pengajuan->{$kolom['file']};

        abort_unless(
            is_string($path) && $path !== '',
            404,
            'Dokumen belum tersedia.'
        );

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
            $relativePath !== ''
            && !str_contains($relativePath, '\\')
            && !in_array(
                '..',
                explode('/', $relativePath),
                true
            )
            && !str_contains($relativePath, "\0"),
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

        if ($request->boolean('download')) {
            return $disk->download(
                $path,
                $filename,
                [
                    'Content-Type' => 'application/pdf',
                ]
            );
        }

        return response()->file(
            $disk->path($path),
            [
                'Content-Type' => 'application/pdf',

                'Content-Disposition' =>
                    'inline; filename="'
                    . $filename
                    . '"',

                'X-Content-Type-Options' => 'nosniff',

                'Cache-Control' => 'private, no-store',
            ]
        );
    }

    /**
     * Memeriksa keamanan URL Google Drive.
     */
    private function isValidGoogleDocumentUrl(
        string $url
    ): bool {
        if (
            strlen($url) > 2048
            || !filter_var(
                $url,
                FILTER_VALIDATE_URL
            )
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
            && in_array(
                $host,
                [
                    'drive.google.com',
                    'docs.google.com',
                ],
                true
            )
            && !isset($parts['user'])
            && !isset($parts['pass'])
            && !isset($parts['port']);
    }

    /**
     * Verifikasi dokumen mahasiswa.
     *
     * Status berubah:
     * diproses -> diverifikasi.
     */
    public function verifikasi(Pengajuan $pengajuan)
    {
        $berhasil = DB::transaction(
            function () use ($pengajuan) {
                $data = Pengajuan::query()
                    ->whereKey($pengajuan->id)
                    ->lockForUpdate()
                    ->firstOrFail();

                if ($data->status !== 'diproses') {
                    return false;
                }

                $data->update([
                    'status' => 'diverifikasi',
                ]);

                $data->loadMissing([
                    'divisi',
                    'pembimbingRef',
                ]);

                /*
                 * Kirim satu notifikasi ke
                 * mahasiswa pemilik pengajuan.
                 */
                $mahasiswa = $data->user;

                if ($mahasiswa) {
                    $mahasiswa->notify(
                        new StatusPengajuanNotification(
                            $data,
                            'diverifikasi'
                        )
                    );
                }

                return true;
            },
            3
        );

        if (!$berhasil) {
            return back()->with(
                'error',
                'Pengajuan ini tidak dapat diverifikasi '
                . 'karena statusnya sudah berubah.'
            );
        }

        return back()->with(
            'success',
            'Berkas mahasiswa berhasil diverifikasi. '
            . 'Notifikasi telah disimpan untuk mahasiswa.'
        );
    }

    /**
     * Menerima pengajuan dan menentukan penempatan.
     *
     * Status berubah:
     * diproses/diverifikasi -> diterima.
     */
    public function terima(
        Request $request,
        Pengajuan $pengajuan
    ) {
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

        /*
         * Pembimbing harus sesuai divisi.
         */
        if (
            $pembimbing->divisi_id
            && (int) $pembimbing->divisi_id
                !== (int) $divisi->id
        ) {
            return back()->with(
                'error',
                'Pembimbing tidak berada di divisi '
                . 'yang dipilih.'
            );
        }

        $berhasil = DB::transaction(
            function () use (
                $pengajuan,
                $data,
                $divisi,
                $pembimbing
            ) {
                /*
                 * Kunci pengajuan untuk mencegah
                 * persetujuan ganda.
                 */
                $pengajuanTerkunci = Pengajuan::query()
                    ->whereKey($pengajuan->id)
                    ->lockForUpdate()
                    ->firstOrFail();

                if (
                    !in_array(
                        $pengajuanTerkunci->status,
                        [
                            'diproses',
                            'diverifikasi',
                        ],
                        true
                    )
                ) {
                    return false;
                }

                /*
                 * Simpan hasil approval Admin.
                 */
                $pengajuanTerkunci->update([
                    'divisi_id' => $divisi->id,

                    'pembimbing_id' => $pembimbing->id,

                    'jobdesk' => $data['jobdesk'],

                    'bidang' => $divisi->nama,

                    'pembimbing' => $pembimbing->nama,

                    'status' => 'diterima',

                    'catatan_admin' => null,
                ]);

                $pengajuanTerkunci->load([
                    'divisi',
                    'pembimbingRef',
                ]);

                /*
                 * Kirim notifikasi penerimaan.
                 */
                $mahasiswa = $pengajuanTerkunci->user;

                if ($mahasiswa) {
                    $mahasiswa->notify(
                        new StatusPengajuanNotification(
                            $pengajuanTerkunci,
                            'diterima'
                        )
                    );
                }

                return true;
            },
            3
        );

        if (!$berhasil) {
            return back()->with(
                'error',
                'Pengajuan ini tidak bisa diterima '
                . 'karena sudah diputuskan atau '
                . 'statusnya telah berubah.'
            );
        }

        $nama = $pengajuan->user?->name
            ?? 'Mahasiswa';

        return redirect()
            ->route('admin.pengajuan.index')
            ->with(
                'success',
                "Pengajuan {$nama} berhasil diterima. "
                . 'Notifikasi telah disimpan untuk mahasiswa.'
            );
    }

    /**
     * Menolak pengajuan disertai alasan.
     *
     * Status berubah:
     * diproses/diverifikasi -> ditolak.
     */
    public function tolak(
        Request $request,
        Pengajuan $pengajuan
    ) {
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

        $berhasil = DB::transaction(
            function () use (
                $pengajuan,
                $data
            ) {
                /*
                 * Kunci baris pengajuan agar
                 * keputusan tidak diproses ganda.
                 */
                $pengajuanTerkunci = Pengajuan::query()
                    ->whereKey($pengajuan->id)
                    ->lockForUpdate()
                    ->firstOrFail();

                if (
                    !in_array(
                        $pengajuanTerkunci->status,
                        [
                            'diproses',
                            'diverifikasi',
                        ],
                        true
                    )
                ) {
                    return false;
                }

                /*
                 * Simpan keputusan dan alasan.
                 */
                $pengajuanTerkunci->update([
                    'status' => 'ditolak',

                    'catatan_admin' =>
                        $data['catatan_admin'],
                ]);

                /*
                 * Kirim notifikasi penolakan.
                 */
                $mahasiswa = $pengajuanTerkunci->user;

                if ($mahasiswa) {
                    $mahasiswa->notify(
                        new StatusPengajuanNotification(
                            $pengajuanTerkunci,
                            'ditolak'
                        )
                    );
                }

                return true;
            },
            3
        );

        if (!$berhasil) {
            return back()->with(
                'error',
                'Pengajuan ini tidak bisa ditolak '
                . 'karena sudah diputuskan atau '
                . 'statusnya telah berubah.'
            );
        }

        $nama = $pengajuan->user?->name
            ?? 'Mahasiswa';

        return redirect()
            ->route('admin.pengajuan.index')
            ->with(
                'success',
                "Pengajuan {$nama} berhasil ditolak. "
                . 'Notifikasi telah disimpan untuk mahasiswa.'
            );
    }
}
