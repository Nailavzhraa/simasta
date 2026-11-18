<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\Mahasiswa;
use App\Models\Pengajuan;
use App\Models\PengajuanDraft;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class DokumenController extends Controller
{
    /**
     * Menampilkan formulir pengajuan magang
     * serta draft milik mahasiswa yang login.
     */
    public function index(Request $request)
    {
        $user = $request->user();

        $mahasiswa = Mahasiswa::where(
            'user_id',
            $user->id
        )->first();

        $pengajuan = Pengajuan::where(
            'user_id',
            $user->id
        )->latest('id')->first();

        $draft = PengajuanDraft::where(
            'user_id',
            $user->id
        )->first();

        return view('mahasiswa.dokumen', compact(
            'mahasiswa',
            'pengajuan',
            'draft'
        ));
    }

    /**
     * Mengirim pengajuan magang final.
     *
     * Formulir baru menggunakan link Google Drive,
     * bukan upload PDF ke server.
     */
    public function store(Request $request)
    {
        $user = $request->user();

        $mahasiswa = Mahasiswa::where(
            'user_id',
            $user->id
        )->first();

        // ==========================================
        // VALIDASI DATA MAHASISWA DAN AKADEMIK
        // ==========================================

        $validated = $request->validate([

            'nama' => [
                'required',
                'string',
                'max:255',
            ],

            'nim' => [
                'required',
                'string',
                'max:255',
                Rule::unique('mahasiswas', 'nim')
                    ->ignore($mahasiswa?->id),
            ],

            'no_telepon' => [
                'nullable',
                'string',
                'max:20',
            ],

            'no_wa' => [
                'nullable',
                'string',
                'max:20',
            ],

            'perguruan_tinggi' => [
                'required',
                'string',
                'max:255',
            ],

            'program_studi' => [
                'required',
                'string',
                'max:255',
            ],

            'tahun_angkatan' => [
                'required',
                'digits:4',
            ],

            'semester' => [
                'required',
                'integer',
                'between:1,14',
            ],

            // ======================================
            // PERIODE MAGANG
            // ======================================

            'tanggal_mulai' => [
                'required',
                'date_format:Y-m-d',
            ],

            'durasi_bulan' => [
                'required',
                'integer',
                'between:1,12',
            ],

            'jenis_pendaftaran' => [
                'required',
                Rule::in([
                    'reguler',
                    'susulan',
                ]),
            ],

            // ======================================
            // DOKUMEN GOOGLE DRIVE
            // ======================================

            'link_cv' => [
                'required',
                'url',
                'max:2048',
            ],

            'link_surat' => [
                'required',
                'url',
                'max:2048',
            ],

            'link_proposal' => [
                'required',
                'url',
                'max:2048',
            ],

            // ======================================
            // TUJUAN DAN KETERANGAN
            // ======================================

            'tujuan_magang' => [
                'required',
                'string',
                'min:10',
                'max:3000',
            ],

            'keterangan_tambahan' => [
                'nullable',
                'string',
                'max:3000',
            ],

        ], [
            'nim.unique' =>
                'NIM sudah digunakan oleh mahasiswa lain.',

            'tahun_angkatan.digits' =>
                'Tahun angkatan harus terdiri dari 4 angka.',

            'tanggal_mulai.date_format' =>
                'Format tanggal mulai harus YYYY-MM-DD.',

            'durasi_bulan.between' =>
                'Durasi magang harus antara 1 sampai 12 bulan.',
        ]);

        // ==========================================
        // VALIDASI DOMAIN GOOGLE DRIVE
        // ==========================================

        foreach ([
            'link_cv',
            'link_surat',
            'link_proposal',
        ] as $field) {

            $url = $validated[$field];

            $scheme = strtolower(
                (string) parse_url($url, PHP_URL_SCHEME)
            );

            $host = strtolower(
                (string) parse_url($url, PHP_URL_HOST)
            );

            if (
                $scheme !== 'https' ||
                !in_array($host, [
                    'drive.google.com',
                    'docs.google.com',
                ], true)
            ) {
                throw ValidationException::withMessages([
                    $field =>
                        'Dokumen harus menggunakan link '
                        . 'Google Drive atau Google Docs '
                        . 'dengan HTTPS.',
                ]);
            }
        }

        // ==========================================
        // HITUNG TANGGAL SELESAI OTOMATIS
        // ==========================================

        $tanggalMulai = Carbon::createFromFormat(
            '!Y-m-d',
            $validated['tanggal_mulai']
        )->startOfDay();

        $durasiBulan = (int) $validated['durasi_bulan'];

        // Aturan: durasi bulan kalender,
        // tanggal akhir inklusif.
        $tanggalSelesai = $tanggalMulai
            ->copy()
            ->addMonthsNoOverflow($durasiBulan)
            ->subDay();

        $mulai = $tanggalMulai->toDateString();
        $selesai = $tanggalSelesai->toDateString();

        // ==========================================
        // SIMPAN DATA DALAM TRANSAKSI DATABASE
        // ==========================================

        DB::transaction(function () use (
            $user,
            $validated,
            $mulai,
            $selesai,
            $durasiBulan
        ) {

            // Mengunci user agar dua proses submit
            // tidak berjalan bersamaan.
            DB::table('users')
                ->where('id', $user->id)
                ->lockForUpdate()
                ->first();

            // Cegah pengajuan aktif yang ganda.
            $pengajuanAktif = Pengajuan::where(
                'user_id',
                $user->id
            )
                ->whereIn('status', [
                    'diproses',
                    'diverifikasi',
                    'diterima',
                ])
                ->exists();

            if ($pengajuanAktif) {
                throw ValidationException::withMessages([
                    'pengajuan' =>
                        'Kamu masih memiliki pengajuan '
                        . 'yang sedang diproses atau '
                        . 'sudah diterima.',
                ]);
            }

            // ======================================
            // SIMPAN DATA PROFIL MAHASISWA
            // ======================================

            Mahasiswa::updateOrCreate(
                [
                    'user_id' => $user->id,
                ],
                [
                    'nama' => $validated['nama'],
                    'nim' => $validated['nim'],
                    'email' => $user->email,

                    'no_telepon' =>
                        $validated['no_telepon'] ?? null,

                    'no_wa' =>
                        $validated['no_wa'] ?? null,

                    'perguruan_tinggi' =>
                        $validated['perguruan_tinggi'],

                    'program_studi' =>
                        $validated['program_studi'],

                    'tahun_angkatan' =>
                        $validated['tahun_angkatan'],

                    'semester' =>
                        $validated['semester'],
                ]
            );

            // Sinkronkan nama akun agar tampilan
            // Dashboard konsisten dengan profil.
            $user->forceFill([
                'name' => $validated['nama'],
            ])->save();

            // ======================================
            // BUAT PENGAJUAN RESMI
            // ======================================

            Pengajuan::create([

                'user_id' => $user->id,

                'instansi' => 'BPS Kota Batu',

                'periode_mulai' => $mulai,

                'periode_selesai' => $selesai,

                'durasi_bulan' => $durasiBulan,

                'jenis_pendaftaran' =>
                    $validated['jenis_pendaftaran'],

                // Link Google Drive
                'link_cv' =>
                    $validated['link_cv'],

                'link_surat' =>
                    $validated['link_surat'],

                'link_proposal' =>
                    $validated['link_proposal'],

                // Informasi pengajuan
                'tujuan_magang' =>
                    $validated['tujuan_magang'],

                'motivasi' =>
                    $validated['tujuan_magang'],

                'keterangan_tambahan' =>
                    $validated['keterangan_tambahan'] ?? null,

                // Status awal
                'status' => 'diproses',

                'status_pelaksanaan' => 'belum_mulai',
            ]);

            // ======================================
            // HAPUS DRAFT SETELAH BERHASIL SUBMIT
            // ======================================

            PengajuanDraft::where(
                'user_id',
                $user->id
            )->delete();

        }, 3);

        // ==========================================
        // KEMBALI KE HALAMAN PENGAJUAN
        // ==========================================

        return redirect()
            ->route('mahasiswa.dokumen')
            ->with(
                'success',
                'Pengajuan magang berhasil dikirim. '
                . 'Data sudah tercatat dan menunggu '
                . 'verifikasi Admin BPS Kota Batu.'
            );
    }
}
