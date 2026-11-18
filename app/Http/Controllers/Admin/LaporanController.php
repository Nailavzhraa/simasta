<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LaporanMagang;
use App\Models\Logbook;
use App\Models\Pengajuan;
use App\Models\PenilaianBerkas;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class LaporanController extends Controller
{
    /**
     * Dashboard utama Laporan Admin.
     */
    public function index(Request $request)
    {
        $query = Pengajuan::query()
            ->with([
                'user.mahasiswa',
                'divisi',
                'pembimbingRef',
                'laporanMagang',
            ])
            ->where('status', 'diterima')
            ->latest('updated_at');

        // Pencarian nama, email, atau NIM.
        if ($request->filled('q')) {
            $keyword = trim($request->input('q'));

            $query->whereHas('user', function ($q) use ($keyword) {
                $q->where(function ($userQuery) use ($keyword) {
                    $userQuery
                        ->where(
                            'name',
                            'like',
                            "%{$keyword}%"
                        )
                        ->orWhere(
                            'email',
                            'like',
                            "%{$keyword}%"
                        )
                        ->orWhereHas(
                            'mahasiswa',
                            fn ($m) => $m->where(
                                'nim',
                                'like',
                                "%{$keyword}%"
                            )
                        );
                });
            });
        }

        // Filter divisi.
        if ($request->filled('divisi_id')) {
            $query->where(
                'divisi_id',
                $request->input('divisi_id')
            );
        }

        // Filter status laporan.
        $status = $request->query('status');

        if ($status === 'belum') {
            $query->whereDoesntHave('laporanMagang');
        } elseif (
            in_array(
                $status,
                ['menunggu', 'ditinjau', 'revisi', 'disetujui'],
                true
            )
        ) {
            $query->whereHas(
                'laporanMagang',
                fn ($q) => $q->where('status', $status)
            );
        }

        $daftar = $query
            ->paginate(10)
            ->withQueryString();

        // Statistik menggunakan data riil dari database.
        $statistik = [
            'mahasiswa' => User::query()
                ->where('role', 'mahasiswa')
                ->count(),

            'pengajuan' => Pengajuan::query()->count(),

            'diterima' => Pengajuan::query()
                ->where('status', 'diterima')
                ->distinct()
                ->count('user_id'),

            'logbook' => Logbook::query()
                ->whereHas(
                    'pengajuan',
                    fn ($q) => $q->where(
                        'status',
                        'diterima'
                    )
                )
                ->count(),

            'penilaian' => PenilaianBerkas::query()
                ->where('status', 'selesai')
                ->whereHas(
                    'pengajuan',
                    fn ($q) => $q->where(
                        'status',
                        'diterima'
                    )
                )
                ->count(),

            'laporan_total' => LaporanMagang::query()
                ->whereHas(
                    'pengajuan',
                    fn ($q) => $q->where(
                        'status',
                        'diterima'
                    )
                )
                ->count(),

            'laporan_menunggu' => LaporanMagang::query()
                ->where('status', 'menunggu')
                ->whereHas(
                    'pengajuan',
                    fn ($q) => $q->where(
                        'status',
                        'diterima'
                    )
                )
                ->count(),

            'laporan_revisi' => LaporanMagang::query()
                ->where('status', 'revisi')
                ->whereHas(
                    'pengajuan',
                    fn ($q) => $q->where(
                        'status',
                        'diterima'
                    )
                )
                ->count(),

            'laporan_disetujui' => LaporanMagang::query()
                ->where('status', 'disetujui')
                ->whereHas(
                    'pengajuan',
                    fn ($q) => $q->where(
                        'status',
                        'diterima'
                    )
                )
                ->count(),
        ];

        $divisis = \App\Models\Divisi::query()
            ->orderBy('nama')
            ->get();

        return view('admin.laporan.index', [
            'daftar' => $daftar,
            'statistik' => $statistik,
            'divisis' => $divisis,
        ]);
    }

    /**
     * Detail laporan seorang mahasiswa.
     */
    public function show(LaporanMagang $laporan)
    {
        $laporan->load([
            'user.mahasiswa',
            'pengajuan.divisi',
            'pengajuan.pembimbingRef',
            'reviewer',
        ]);

        abort_unless(
            $laporan->pengajuan?->status === 'diterima',
            403,
            'Pengajuan magang tidak aktif.'
        );

        return view('admin.laporan.show', [
            'laporan' => $laporan,
        ]);
    }

    /**
     * Admin mengubah status laporan.
     */
    public function updateStatus(
        Request $request,
        LaporanMagang $laporan
    ) {
        abort_unless(
            $laporan->pengajuan?->status === 'diterima',
            403,
            'Pengajuan magang tidak aktif.'
        );

        $data = $request->validate([
            'status' => [
                'required',
                Rule::in([
                    'ditinjau',
                    'revisi',
                    'disetujui',
                ]),
            ],

            'catatan_admin' => [
                Rule::requiredIf(
                    $request->input('status') === 'revisi'
                ),
                'nullable',
                'string',
                'max:5000',
            ],
        ]);

        // Hindari perubahan pada laporan final.
        if ($laporan->status === 'disetujui') {
            return back()->withErrors([
                'status' =>
                    'Laporan sudah disetujui dan tidak dapat diubah.',
            ]);
        }

        // Setelah meminta revisi, admin menunggu
        // mahasiswa mengirim ulang dokumennya.
        if ($laporan->status === 'revisi') {
            return back()->withErrors([
                'status' =>
                    'Menunggu mahasiswa mengirim revisi laporan.',
            ]);
        }

        $laporan->status = $data['status'];
        $laporan->catatan_admin =
            $data['catatan_admin'] ?? null;

        $laporan->reviewed_by = auth()->id();
        $laporan->ditinjau_at = now();

        if ($data['status'] === 'disetujui') {
            $laporan->disetujui_at = now();
        } else {
            $laporan->disetujui_at = null;
        }

        $laporan->save();

        $pesan = match ($data['status']) {
            'ditinjau' =>
                'Laporan ditandai sedang ditinjau.',

            'revisi' =>
                'Permintaan revisi berhasil dikirim ke mahasiswa.',

            'disetujui' =>
                'Laporan mahasiswa berhasil disetujui.',
        };

        return redirect()
            ->route('admin.laporan.show', $laporan->id)
            ->with('success', $pesan);
    }

    /**
     * Admin mengunduh laporan mahasiswa.
     */
    public function unduh(LaporanMagang $laporan)
    {
        abort_unless(
            $laporan->pengajuan?->status === 'diterima',
            403
        );

        abort_unless(
            $laporan->file_path,
            404,
            'Laporan ini hanya menggunakan tautan eksternal.'
        );

        abort_unless(
            Storage::disk('local')->exists($laporan->file_path),
            404,
            'File laporan tidak ditemukan.'
        );

        return Storage::disk('local')->download(
            $laporan->file_path,
            $laporan->file_nama
                ?: basename($laporan->file_path)
        );
    }

    /**
     * Export rekap CSV yang dapat dibuka Excel.
     */
    public function exportCsv()
    {
        $filename = 'rekap-laporan-magang-'
            . now()->format('Ymd-His')
            . '.csv';

        $query = Pengajuan::query()
            ->with([
                'user.mahasiswa',
                'divisi',
                'laporanMagang',
            ])
            ->where('status', 'diterima')
            ->orderBy('id');

        return response()->streamDownload(
            function () use ($query) {
                $output = fopen('php://output', 'w');

                // BOM untuk kompatibilitas Excel.
                fwrite($output, "\xEF\xBB\xBF");

                fputcsv($output, [
                    'Nama Mahasiswa',
                    'NIM',
                    'Email',
                    'Divisi',
                    'Judul Laporan',
                    'Status Laporan',
                    'Tanggal Kirim',
                    'Tanggal Persetujuan',
                ]);

                foreach ($query->cursor() as $pengajuan) {
                    $user = $pengajuan->user;
                    $laporan = $pengajuan->laporanMagang;

                    $values = [
                        $user?->name ?? '-',
                        $user?->mahasiswa?->nim ?? '-',
                        $user?->email ?? '-',
                        $pengajuan->divisi?->nama ?? '-',
                        $laporan?->judul ?? '-',
                        $laporan?->status_label ?? 'Belum Dikirim',
                        $laporan?->dikirim_at?->format('d/m/Y H:i')
                            ?? '-',
                        $laporan?->disetujui_at?->format('d/m/Y H:i')
                            ?? '-',
                    ];

                    // Cegah formula injection saat CSV dibuka Excel.
                    $values = array_map(
                        function ($value) {
                            $value = (string) $value;

                            if (preg_match('/^[\s]*[=+\-@]/u', $value)) {
                                return "'" . $value;
                            }

                            return $value;
                        },
                        $values
                    );

                    fputcsv($output, $values);
                }

                fclose($output);
            },
            $filename,
            [
                'Content-Type' =>
                    'text/csv; charset=UTF-8',
            ]
        );
    }
}
