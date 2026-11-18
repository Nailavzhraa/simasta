<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\Pengajuan;
use App\Models\PenilaianBerkas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class PenilaianController extends Controller
{
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
     * Halaman penilaian mahasiswa.
     */
    public function index()
    {
        $pengajuan = $this->pengajuanAktif();

        $berkas = PenilaianBerkas::query()
            ->where('pengajuan_id', $pengajuan->id)
            ->where('user_id', auth()->id())
            ->first();

        $sertifikat = \App\Models\Sertifikat::query()
            ->where('pengajuan_id', $pengajuan->id)
            ->where('user_id', auth()->id())
            ->where('status', 'terbit')
            ->first();

        return view('mahasiswa.penilaian', [
            'pengajuan' => $pengajuan,
            'berkas' => $berkas,
            'sertifikat' => $sertifikat,
        ]);
    }

    /**
     * Mahasiswa mengirim template penilaian kampus.
     */
    public function kirimTemplate(Request $request)
    {
        $pengajuan = $this->pengajuanAktif();

        $validator = Validator::make(
            $request->all(),
            [
                'template_file' => [
                    'nullable',
                    'file',
                    'mimes:pdf,doc,docx,xls,xlsx',
                    'max:10240',
                ],
                'template_url' => [
                    'nullable',
                    'url:http,https',
                    'max:2000',
                ],
                'catatan_mahasiswa' => [
                    'nullable',
                    'string',
                    'max:2000',
                ],
            ],
            [
                'template_file.mimes' =>
                    'File harus berupa PDF, Word, atau Excel.',
                'template_file.max' =>
                    'Ukuran file maksimal 10 MB.',
                'template_url.url' =>
                    'Masukkan tautan HTTP/HTTPS yang valid.',
            ]
        );

        $validator->after(function ($validator) use ($request) {
            if (
                !$request->hasFile('template_file') &&
                !$request->filled('template_url')
            ) {
                $validator->errors()->add(
                    'template_file',
                    'Unggah file atau masukkan tautan Google Drive.'
                );
            }
        });

        $data = $validator->validate();

        $berkas = PenilaianBerkas::firstOrNew([
            'pengajuan_id' => $pengajuan->id,
        ]);

        // Dokumen yang sudah selesai tidak dapat diganti
        // langsung oleh mahasiswa.
        if ($berkas->exists && $berkas->status === 'selesai') {
            return back()->withErrors([
                'template_file' =>
                    'Penilaian sudah selesai. Hubungi admin jika perlu revisi.',
            ]);
        }

        $pathBaru = null;
        $namaBaru = null;

        if ($request->hasFile('template_file')) {
            $file = $request->file('template_file');

            $pathBaru = $file->store(
                'simasta/template-penilaian',
                'local'
            );

            $namaBaru = $file->getClientOriginalName();
        }

        // Simpan lokasi file lama untuk pembersihan.
        $pathLama = $berkas->template_path;

        $berkas->user_id = auth()->id();
        $berkas->pengajuan_id = $pengajuan->id;

        // Pengiriman baru menggantikan dokumen sebelumnya.
        $berkas->template_path = $pathBaru;
        $berkas->template_nama = $namaBaru;
        $berkas->template_url =
            $data['template_url'] ?? null;

        $berkas->template_dikirim_at = now();
        $berkas->catatan_mahasiswa =
            $data['catatan_mahasiswa'] ?? null;

        $berkas->status = 'menunggu_penilaian';
        $berkas->save();

        if (
            $pathLama &&
            $pathLama !== $pathBaru &&
            Storage::disk('local')->exists($pathLama)
        ) {
            Storage::disk('local')->delete($pathLama);
        }

        return redirect()
            ->route('mahasiswa.penilaian')
            ->with(
                'success',
                'Template penilaian berhasil dikirim ke admin BPS.'
            );
    }

    /**
     * Mahasiswa mengunduh hasil penilaian dari admin.
     */
    public function unduhHasil(PenilaianBerkas $berkas)
    {
        abort_unless(
            (int) $berkas->user_id === (int) auth()->id(),
            403
        );

        $pengajuan = $this->pengajuanAktif();

        abort_unless(
            (int) $berkas->pengajuan_id === (int) $pengajuan->id,
            403
        );

        abort_unless(
            $berkas->status === 'selesai' &&
            $berkas->hasil_path,
            404
        );

        abort_unless(
            Storage::disk('local')->exists($berkas->hasil_path),
            404,
            'File hasil penilaian tidak ditemukan.'
        );

        return Storage::disk('local')->download(
            $berkas->hasil_path,
            $berkas->hasil_nama
                ?: basename($berkas->hasil_path)
        );
    }
}
