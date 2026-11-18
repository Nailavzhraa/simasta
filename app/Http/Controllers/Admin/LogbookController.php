<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Divisi;
use App\Models\Logbook;
use App\Models\Pengajuan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LogbookController extends Controller
{
    /**
     * Halaman Monitoring Logbook Admin.
     */
    public function index(Request $request)
    {
        $query = Pengajuan::query()
            ->with([
                'user.mahasiswa',
                'divisi',
                'timKerja',
                'pembimbingRef',
            ])
            ->where('status', 'diterima')
            ->latest('id');

        // ==========================================
        // PENCARIAN MAHASISWA
        // ==========================================

        if ($request->filled('q')) {
            $keyword = trim(
                (string) $request->input('q')
            );

            $query->whereHas(
                'user',
                function ($builder) use ($keyword) {
                    $builder
                        ->where('name', 'like', "%{$keyword}%")
                        ->orWhere('email', 'like', "%{$keyword}%")
                        ->orWhereHas(
                            'mahasiswa',
                            function ($mhs) use ($keyword) {
                                $mhs
                                    ->where(
                                        'nama',
                                        'like',
                                        "%{$keyword}%"
                                    )
                                    ->orWhere(
                                        'nim',
                                        'like',
                                        "%{$keyword}%"
                                    )
                                    ->orWhere(
                                        'perguruan_tinggi',
                                        'like',
                                        "%{$keyword}%"
                                    )
                                    ->orWhere(
                                        'program_studi',
                                        'like',
                                        "%{$keyword}%"
                                    );
                            }
                        );
                }
            );
        }

        // ==========================================
        // FILTER DIVISI
        // ==========================================

        if ($request->filled('divisi_id')) {
            $divisiId = $request->input('divisi_id');

            $query->where(
                'divisi_id',
                $divisiId
            );
        }

        // ==========================================
        // FILTER PENGISIAN LOGBOOK
        // ==========================================

        $pengisian = $request->input('pengisian');

        if ($pengisian === 'sudah') {
            $query->whereHas('logbooks');
        } elseif ($pengisian === 'belum') {
            $query->whereDoesntHave('logbooks');
        }

        // ==========================================
        // PAGINATION
        // ==========================================

        $daftar = $query
            ->paginate(10)
            ->withQueryString();

        $pengajuanIds = $daftar
            ->getCollection()
            ->pluck('id')
            ->all();

        // ==========================================
        // JUMLAH LOGBOOK PER PENGAJUAN
        // ==========================================

        $jumlahLogbook = Logbook::query()
            ->select('pengajuan_id')
            ->selectRaw('COUNT(*) AS total')
            ->selectRaw(
                "SUM(CASE WHEN status = 'menunggu' " .
                "THEN 1 ELSE 0 END) AS menunggu"
            )
            ->selectRaw(
                "SUM(CASE WHEN status = 'ditinjau' " .
                "THEN 1 ELSE 0 END) AS ditinjau"
            )
            ->whereIn(
                'pengajuan_id',
                $pengajuanIds
            )
            ->groupBy('pengajuan_id')
            ->get()
            ->keyBy('pengajuan_id');

        // ==========================================
        // STATISTIK KESELURUHAN
        // ==========================================

        $statistik = [
            'mahasiswa' => Pengajuan::query()
                ->where('status', 'diterima')
                ->distinct()
                ->count('user_id'),

            'total' => Logbook::query()
                ->whereHas(
                    'pengajuan',
                    fn ($q) => $q->where(
                        'status',
                        'diterima'
                    )
                )
                ->count(),

            'menunggu' => Logbook::query()
                ->where('status', 'menunggu')
                ->whereHas(
                    'pengajuan',
                    fn ($q) => $q->where(
                        'status',
                        'diterima'
                    )
                )
                ->count(),

            'ditinjau' => Logbook::query()
                ->where('status', 'ditinjau')
                ->whereHas(
                    'pengajuan',
                    fn ($q) => $q->where(
                        'status',
                        'diterima'
                    )
                )
                ->count(),
        ];

        return view(
            'admin.logbook.index',
            [
                'daftar' => $daftar,
                'jumlahLogbook' => $jumlahLogbook,
                'statistik' => $statistik,
                'divisis' => Divisi::orderBy('nama')->get(),
            ]
        );
    }

    /**
     * Detail riwayat logbook seorang mahasiswa.
     */
    public function show(Request $request, User $user)
    {
        $user->loadMissing('mahasiswa');

        // Cari pengajuan diterima terbaru.
        $pengajuan = Pengajuan::query()
            ->with([
                'divisi',
                'timKerja',
                'pembimbingRef',
            ])
            ->where('user_id', $user->id)
            ->where('status', 'diterima')
            ->latest('id')
            ->firstOrFail();

        // ==========================================
        // DAFTAR KEGIATAN MAHASISWA
        // ==========================================

        $query = Logbook::query()
            ->where(
                'pengajuan_id',
                $pengajuan->id
            )
            ->where(
                'user_id',
                $user->id
            )
            ->orderByDesc('tanggal')
            ->orderByDesc('id');

        // Filter status tinjauan.
        if (
            in_array(
                $request->input('status'),
                ['menunggu', 'ditinjau'],
                true
            )
        ) {
            $query->where(
                'status',
                $request->input('status')
            );
        }

        $logbooks = $query
            ->paginate(10)
            ->withQueryString();

        // ==========================================
        // STATISTIK PER MAHASISWA
        // ==========================================

        $total = Logbook::query()
            ->where('pengajuan_id', $pengajuan->id)
            ->where('user_id', $user->id)
            ->count();

        $menunggu = Logbook::query()
            ->where('pengajuan_id', $pengajuan->id)
            ->where('user_id', $user->id)
            ->where('status', 'menunggu')
            ->count();

        $ditinjau = Logbook::query()
            ->where('pengajuan_id', $pengajuan->id)
            ->where('user_id', $user->id)
            ->where('status', 'ditinjau')
            ->count();

        return view(
            'admin.logbook.show',
            [
                'mahasiswa' => $user,
                'pengajuan' => $pengajuan,
                'logbooks' => $logbooks,
                'total' => $total,
                'menunggu' => $menunggu,
                'ditinjau' => $ditinjau,
            ]
        );
    }

    /**
     * Admin meninjau satu logbook mahasiswa.
     */
    public function tinjau(
        Request $request,
        Logbook $logbook
    ) {
        $data = $request->validate([
            'catatan_admin' => [
                'nullable',
                'string',
                'max:2000',
            ],
        ], [
            'catatan_admin.max' =>
                'Catatan Admin maksimal 2000 karakter.',
        ]);

        DB::transaction(function () use (
            $logbook,
            $data
        ) {
            // Kunci record untuk menghindari
            // peninjauan bersamaan.
            $logbook = Logbook::query()
                ->whereKey($logbook->id)
                ->lockForUpdate()
                ->firstOrFail();

            $pengajuan = Pengajuan::query()
                ->whereKey($logbook->pengajuan_id)
                ->firstOrFail();

            // Pastikan logbook memang berasal
            // dari mahasiswa pemilik pengajuan.
            abort_unless(
                (int) $pengajuan->user_id ===
                (int) $logbook->user_id,
                403,
                'Logbook tidak sesuai dengan pemilik pengajuan.'
            );

            // Hanya logbook dari pengajuan
            // diterima yang boleh ditinjau.
            abort_unless(
                $pengajuan->status === 'diterima',
                403,
                'Pengajuan magang belum diterima.'
            );

            $logbook->update([
                'status' => 'ditinjau',
                'catatan_admin' =>
                    $data['catatan_admin'] ?? null,
                'ditinjau_at' => now(),
            ]);
        }, 3);

        return back()->with(
            'success',
            'Logbook mahasiswa berhasil ditinjau.'
        );
    }
}
