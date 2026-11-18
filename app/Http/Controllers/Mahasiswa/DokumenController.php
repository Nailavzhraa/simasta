<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\Mahasiswa;
use App\Models\Pengajuan;
use App\Models\PengajuanDraft;
use App\Models\User;
use App\Notifications\PengajuanBaruNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class DokumenController extends Controller
{
    /**
     * Menampilkan formulir pengajuan magang
     * beserta draft milik mahasiswa.
     */
    public function index(Request $request)
    {
        $user = $request->user();

        $mahasiswa = Mahasiswa::query()
            ->where('user_id', $user->id)
            ->first();

        $pengajuan = Pengajuan::query()
            ->where('user_id', $user->id)
            ->latest('id')
            ->first();

        $draft = PengajuanDraft::query()
            ->where('user_id', $user->id)
            ->first();

        return view('mahasiswa.dokumen', compact(
            'mahasiswa',
            'pengajuan',
            'draft'
        ));
    }

    /**
     * Mengirim pengajuan magang resmi.
     *
     * Dokumen menggunakan link Google Drive.
     * Notifikasi database dikirim kepada Admin
     * setelah pengajuan berhasil dibuat.
     */
    public function store(Request $request)
    {
        $user = $request->user();

        $mahasiswa = Mahasiswa::query()
            ->where('user_id', $user->id)
            ->first();

        /*
        |--------------------------------------------------------------------------
        | VALIDASI FORMULIR
        |--------------------------------------------------------------------------
        */

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

        /*
        |--------------------------------------------------------------------------
        | VALIDASI LINK GOOGLE DRIVE
        |--------------------------------------------------------------------------
        */

        foreach ([
            'link_cv',
            'link_surat',
            'link_proposal',
        ] as $field) {
            $url = $validated[$field];

            $scheme = strtolower(
                (string) parse_url(
                    $url,
                    PHP_URL_SCHEME
                )
            );

            $host = strtolower(
                (string) parse_url(
                    $url,
                    PHP_URL_HOST
                )
            );

            if (
                $scheme !== 'https'
                || !in_array($host, [
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

        /*
        |--------------------------------------------------------------------------
        | HITUNG PERIODE MAGANG
        |--------------------------------------------------------------------------
        */

        $tanggalMulai = Carbon::createFromFormat(
            '!Y-m-d',
            $validated['tanggal_mulai']
        )->startOfDay();

        $durasiBulan = (int) $validated['durasi_bulan'];

        $tanggalSelesai = $tanggalMulai
            ->copy()
            ->addMonthsNoOverflow($durasiBulan)
            ->subDay();

        $mulai = $tanggalMulai->toDateString();
        $selesai = $tanggalSelesai->toDateString();

        /*
        |--------------------------------------------------------------------------
        | SIMPAN PENGAJUAN DAN NOTIFIKASI
        |--------------------------------------------------------------------------
        |
        | Semua operasi database dilakukan dalam
        | satu transaksi agar data konsisten.
        |
        */

        DB::transaction(function () use (
            $user,
            $validated,
            $mulai,
            $selesai,
            $durasiBulan
        ) {
            /*
             * Kunci baris user untuk mencegah
             * dua pengajuan bersamaan.
             */
            DB::table('users')
                ->where('id', $user->id)
                ->lockForUpdate()
                ->first();

            /*
             * Cegah pengajuan aktif ganda.
             */
            $pengajuanAktif = Pengajuan::query()
                ->where('user_id', $user->id)
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

            /*
             * Simpan atau perbarui profil mahasiswa.
             */
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

            /*
             * Sinkronkan nama akun.
             */
            $user->forceFill([
                'name' => $validated['nama'],
            ])->save();

            /*
             * Buat pengajuan resmi.
             */
            $pengajuan = Pengajuan::create([
                'user_id' => $user->id,

                'instansi' => 'BPS Kota Batu',

                'periode_mulai' => $mulai,

                'periode_selesai' => $selesai,

                'durasi_bulan' => $durasiBulan,

                'jenis_pendaftaran' =>
                    $validated['jenis_pendaftaran'],

                'link_cv' =>
                    $validated['link_cv'],

                'link_surat' =>
                    $validated['link_surat'],

                'link_proposal' =>
                    $validated['link_proposal'],

                'tujuan_magang' =>
                    $validated['tujuan_magang'],

                'motivasi' =>
                    $validated['tujuan_magang'],

                'keterangan_tambahan' =>
                    $validated['keterangan_tambahan'] ?? null,

                'status' => 'diproses',

                'status_pelaksanaan' => 'belum_mulai',
            ]);

            /*
             * Hapus draft setelah pengajuan
             * resmi berhasil dibuat.
             */
            PengajuanDraft::query()
                ->where('user_id', $user->id)
                ->delete();

            /*
             * Kirim notifikasi database ke seluruh
             * akun dengan role admin.
             *
             * Tidak membuat notifikasi ganda pada
             * pengajuan aktif yang sama karena
             * pengecekan dilakukan sebelumnya.
             */
            $adminUsers = User::query()
                ->where('role', 'admin')
                ->get();

            foreach ($adminUsers as $admin) {
                $admin->notify(
                    new PengajuanBaruNotification(
                        $pengajuan,
                        $validated['nama']
                    )
                );
            }
        }, 3);

        /*
        |--------------------------------------------------------------------------
        | KEMBALI KE HALAMAN PENGAJUAN
        |--------------------------------------------------------------------------
        */

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
