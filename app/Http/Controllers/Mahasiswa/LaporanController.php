<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\LaporanMagang;
use App\Models\Pengajuan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class LaporanController extends Controller
{
    /**
     * Ambil pengajuan diterima milik mahasiswa login.
     */
    private function pengajuanAktif(): Pengajuan
    {
        return Pengajuan::query()
            ->with(['divisi', 'pembimbingRef'])
            ->where('user_id', auth()->id())
            ->where('status', 'diterima')
            ->latest('updated_at')
            ->firstOrFail();
    }

    /**
     * Halaman Laporan Akhir Mahasiswa.
     */
    public function index()
    {
        $pengajuan = $this->pengajuanAktif();

        $laporan = LaporanMagang::query()
            ->where('pengajuan_id', $pengajuan->id)
            ->where('user_id', auth()->id())
            ->first();

        return view('mahasiswa.laporan', [
            'pengajuan' => $pengajuan,
            'laporan' => $laporan,
        ]);
    }

    /**
     * Mahasiswa mengirim laporan pertama
     * atau mengirim ulang setelah status revisi.
     */
    public function store(Request $request)
    {
        $pengajuan = $this->pengajuanAktif();

        $data = $request->validate(
            [
                'judul' => [
                    'required',
                    'string',
                    'max:255',
                ],
                'deskripsi' => [
                    'nullable',
                    'string',
                    'max:5000',
                ],
                'file_laporan' => [
                    'nullable',
                    'file',
                    'mimes:pdf,doc,docx',
                    'max:20480',
                ],
                'file_url' => [
                    'nullable',
                    'url:http,https',
                    'max:2000',
                ],
            ],
            [
                'judul.required' =>
                    'Judul laporan wajib diisi.',

                'file_laporan.mimes' =>
                    'Format laporan harus PDF, DOC, atau DOCX.',

                'file_laporan.max' =>
                    'Ukuran file maksimal 20 MB.',

                'file_url.url' =>
                    'Masukkan link HTTP/HTTPS yang valid.',
            ]
        );

        if (
            !$request->hasFile('file_laporan') &&
            !$request->filled('file_url')
        ) {
            throw ValidationException::withMessages([
                'file_laporan' =>
                    'Unggah dokumen atau masukkan link Google Drive.',
            ]);
        }

        $pathBaru = null;

        if ($request->hasFile('file_laporan')) {
            $file = $request->file('file_laporan');

            $pathBaru = $file->store(
                'simasta/laporan-mahasiswa',
                'local'
            );
        }

        try {
            $pathLama = DB::transaction(
                function () use ($pengajuan, $data, $request, $pathBaru) {

                    $laporan = LaporanMagang::query()
                        ->where('pengajuan_id', $pengajuan->id)
                        ->lockForUpdate()
                        ->first();

                    // Laporan yang sedang diperiksa atau sudah
                    // disetujui tidak dapat ditimpa mahasiswa.
                    if (
                        $laporan &&
                        !in_array(
                            $laporan->status,
                            ['revisi'],
                            true
                        )
                    ) {
                        throw ValidationException::withMessages([
                            'judul' =>
                                'Laporan sudah dikirim. Tunggu pemeriksaan '
                                . 'atau permintaan revisi dari Admin BPS.',
                        ]);
                    }

                    $pathLama = $laporan?->file_path;

                    if (!$laporan) {
                        $laporan = new LaporanMagang();

                        $laporan->pengajuan_id = $pengajuan->id;
                        $laporan->user_id = auth()->id();
                    }

                    $laporan->judul = $data['judul'];
                    $laporan->deskripsi = $data['deskripsi'] ?? null;

                    // Dokumen baru menggantikan lampiran lama.
                    $laporan->file_path = $pathBaru;

                    $laporan->file_nama =
                        $request->hasFile('file_laporan')
                            ? $request->file('file_laporan')
                                ->getClientOriginalName()
                            : null;

                    $laporan->file_url = $data['file_url'] ?? null;

                    $laporan->status = 'menunggu';
                    $laporan->catatan_admin = null;
                    $laporan->reviewed_by = null;
                    $laporan->ditinjau_at = null;
                    $laporan->disetujui_at = null;
                    $laporan->dikirim_at = now();

                    $laporan->save();

                    return $pathLama;
                }
            );
        } catch (\Throwable $e) {
            // Jika DB gagal, hapus file baru yang telanjur diunggah.
            if ($pathBaru) {
                Storage::disk('local')->delete($pathBaru);
            }

            throw $e;
        }

        // File lama dihapus hanya setelah transaksi sukses.
        if ($pathLama && $pathLama !== $pathBaru) {
            Storage::disk('local')->delete($pathLama);
        }

        return redirect()
            ->route('mahasiswa.laporan')
            ->with(
                'success',
                'Laporan magang berhasil dikirim ke Admin BPS.'
            );
    }

    /**
     * Mahasiswa mengunduh file miliknya sendiri.
     */
    public function unduh(LaporanMagang $laporan)
    {
        abort_unless(
            (int) $laporan->user_id === (int) auth()->id(),
            403,
            'Anda tidak memiliki akses ke laporan ini.'
        );

        $pengajuan = $this->pengajuanAktif();

        abort_unless(
            (int) $laporan->pengajuan_id ===
                (int) $pengajuan->id,
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
}
