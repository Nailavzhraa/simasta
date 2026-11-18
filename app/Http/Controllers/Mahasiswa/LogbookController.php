<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\Logbook;
use App\Models\Pengajuan;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class LogbookController extends Controller
{
    /**
     * Kalender bulanan dan riwayat logbook mahasiswa.
     */
    public function index(Request $request)
    {
        $userId = auth()->id();

        $pengajuan = Pengajuan::query()
            ->with(['divisi', 'pembimbingRef'])
            ->where('user_id', $userId)
            ->where('status', 'diterima')
            ->latest('id')
            ->firstOrFail();

        // Bulan yang sedang dilihat di kalender.
        $bulan = (string) $request->query(
            'bulan',
            now()->format('Y-m')
        );

        if (!preg_match(
            '/^\d{4}-(0[1-9]|1[0-2])$/',
            $bulan
        )) {
            $bulan = now()->format('Y-m');
        }

        $awalBulan = Carbon::createFromFormat(
            '!Y-m-d',
            $bulan . '-01'
        );

        $akhirBulan = $awalBulan->copy()->endOfMonth();

        // Semua logbook pada bulan yang dipilih.
        // Tidak dibatasi pagination supaya seluruh
        // penanda tanggal pada kalender muncul.
        $kalenderLogbooks = Logbook::query()
            ->where('user_id', $userId)
            ->where('pengajuan_id', $pengajuan->id)
            ->whereBetween('tanggal', [
                $awalBulan->toDateString(),
                $akhirBulan->toDateString(),
            ])
            ->orderBy('id')
            ->get()
            ->mapWithKeys(function (Logbook $logbook) {
                $tanggal = $logbook->tanggal->toDateString();

                return [
                    $tanggal => [
                        'tanggal' => $tanggal,
                        'kegiatan' => $logbook->kegiatan,
                        'capaian' => $logbook->capaian,
                        'link_bukti' => $logbook->link_bukti,
                        'status' => $logbook->status,
                        'catatan_admin' => $logbook->catatan_admin,
                    ],
                ];
            });

        // Riwayat kegiatan mahasiswa.
        $logbooks = Logbook::query()
            ->where('user_id', $userId)
            ->where('pengajuan_id', $pengajuan->id)
            ->orderByDesc('tanggal')
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        // Statistik.
        $counts = Logbook::query()
            ->where('user_id', $userId)
            ->where('pengajuan_id', $pengajuan->id)
            ->selectRaw('COUNT(*) AS total')
            ->selectRaw(
                "SUM(CASE WHEN status = 'menunggu' " .
                "THEN 1 ELSE 0 END) AS menunggu"
            )
            ->selectRaw(
                "SUM(CASE WHEN status = 'ditinjau' " .
                "THEN 1 ELSE 0 END) AS ditinjau"
            )
            ->first();

        $statistik = [
            'total' => (int) ($counts->total ?? 0),
            'menunggu' => (int) ($counts->menunggu ?? 0),
            'ditinjau' => (int) ($counts->ditinjau ?? 0),
        ];

        return view('mahasiswa.logbook', [
            'pengajuan' => $pengajuan,
            'logbooks' => $logbooks,
            'statistik' => $statistik,
            'kalenderLogbooks' => $kalenderLogbooks,
            'awalBulan' => $awalBulan,
        ]);
    }

    /**
     * Simpan aktivitas dari popup kalender.
     *
     * Mahasiswa boleh mengisi tanggal sebelumnya
     * selama masih berada dalam periode magang.
     */
    public function store(Request $request)
    {
        $userId = auth()->id();

        $pengajuan = Pengajuan::query()
            ->where('user_id', $userId)
            ->where('status', 'diterima')
            ->latest('id')
            ->firstOrFail();

        // Gunakan tanggal aktual jika tersedia,
        // selain itu gunakan periode rencana.
        $awal = $pengajuan->tanggal_mulai_aktual
            ?: $pengajuan->periode_mulai;

        $akhir = $pengajuan->tanggal_selesai_aktual
            ?: $pengajuan->periode_selesai;

        $tanggalMulai = $awal
            ? Carbon::parse($awal)->toDateString()
            : null;

        $tanggalAkhir = $akhir
            ? Carbon::parse($akhir)->toDateString()
            : null;

        // Validasi rentang tanggal.
        $aturanTanggal = [
            'required',
            'date_format:Y-m-d',
            'before_or_equal:today',
        ];

        if ($tanggalMulai) {
            $aturanTanggal[] =
                'after_or_equal:' . $tanggalMulai;
        }

        if ($tanggalAkhir) {
            $aturanTanggal[] =
                'before_or_equal:' . $tanggalAkhir;
        }

        $data = $request->validate([
            'tanggal' => $aturanTanggal,

            'kegiatan' => [
                'required',
                'string',
                'max:5000',
            ],

            'capaian' => [
                'nullable',
                'string',
                'max:3000',
            ],

            'link_bukti' => [
                'required',
                'url',
                'max:2048',
                function ($attribute, $value, $fail) {
                    $scheme = strtolower(
                        (string) parse_url(
                            $value,
                            PHP_URL_SCHEME
                        )
                    );

                    $host = strtolower(
                        (string) parse_url(
                            $value,
                            PHP_URL_HOST
                        )
                    );

                    $validHosts = [
                        'drive.google.com',
                        'docs.google.com',
                    ];

                    if (
                        $scheme !== 'https' ||
                        !in_array($host, $validHosts, true)
                    ) {
                        $fail(
                            'Bukti kegiatan harus menggunakan ' .
                            'link HTTPS Google Drive atau Google Docs.'
                        );
                    }
                },
            ],
        ], [
            'tanggal.required' =>
                'Tanggal kegiatan wajib dipilih.',

            'tanggal.date_format' =>
                'Format tanggal tidak valid.',

            'tanggal.before_or_equal' =>
                'Tanggal tidak boleh melewati hari ini atau akhir masa magang.',

            'tanggal.after_or_equal' =>
                'Tanggal tidak boleh sebelum masa magang dimulai.',

            'kegiatan.required' =>
                'Deskripsi aktivitas wajib diisi.',

            'kegiatan.max' =>
                'Deskripsi aktivitas maksimal 5000 karakter.',

            'capaian.max' =>
                'Capaian maksimal 3000 karakter.',

            'link_bukti.required' =>
                'Link foto bukti kegiatan Google Drive wajib diisi.',

            'link_bukti.url' =>
                'Link Google Drive tidak valid.',
        ]);

        // Cegah logbook ganda pada tanggal sama.
        DB::transaction(function () use (
            $userId,
            $pengajuan,
            $data
        ) {
            // Kunci baris user supaya dua request
            // dari mahasiswa yang sama tidak
            // membuat dua logbook bersamaan.
            DB::table('users')
                ->where('id', $userId)
                ->lockForUpdate()
                ->firstOrFail();

            $sudahAda = Logbook::query()
                ->where('user_id', $userId)
                ->where('pengajuan_id', $pengajuan->id)
                ->whereDate('tanggal', $data['tanggal'])
                ->exists();

            if ($sudahAda) {
                throw ValidationException::withMessages([
                    'tanggal' =>
                        'Logbook pada tanggal ini sudah ada. ' .
                        'Klik tanggalnya untuk melihat kegiatan.',
                ]);
            }

            Logbook::create([
                'user_id' => $userId,
                'pengajuan_id' => $pengajuan->id,
                'tanggal' => $data['tanggal'],
                'kegiatan' => $data['kegiatan'],
                'capaian' => $data['capaian'] ?? null,
                'link_bukti' => $data['link_bukti'],
                'pembelajaran' => null,
                'kendala' => null,
                'status' => 'menunggu',
                'catatan_admin' => null,
                'ditinjau_at' => null,
            ]);
        }, 3);

        // Kembali ke bulan kegiatan yang diisi.
        $bulan = Carbon::parse(
            $data['tanggal']
        )->format('Y-m');

        $tanggalTampil = Carbon::parse(
            $data['tanggal']
        )->translatedFormat('d F Y');

        return redirect()
            ->route('mahasiswa.logbook', [
                'bulan' => $bulan,
            ])
            ->with(
                'success',
                'Logbook tanggal ' . $tanggalTampil .
                ' berhasil disimpan dan menunggu tinjauan Admin.'
            );
    }
}
