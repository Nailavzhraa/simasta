<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pengajuan;
use App\Models\PenilaianBerkas;
use App\Models\Sertifikat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class PenilaianController extends Controller
{
    /**
     * Halaman Penilaian & Sertifikat Admin.
     */
    public function index(Request $request)
    {
        $query = Pengajuan::query()
            ->with([
                'user.mahasiswa',
                'divisi',
                'pembimbingRef',
                'penilaianBerkas',
                'sertifikat',
            ])
            ->where('status', 'diterima')
            ->latest('updated_at');

        if ($request->filled('q')) {
            $keyword = trim($request->input('q'));

            $query->whereHas('user', function ($q) use ($keyword) {
                $q->where(function ($userQuery) use ($keyword) {
                    $userQuery
                        ->where('name', 'like', "%{$keyword}%")
                        ->orWhere('email', 'like', "%{$keyword}%")
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

        if ($request->filled('status')) {
            if ($request->status === 'belum') {
                $query->whereDoesntHave('penilaianBerkas');
            } elseif ($request->status === 'menunggu') {
                $query->whereHas(
                    'penilaianBerkas',
                    fn ($q) => $q->where(
                        'status',
                        'menunggu_penilaian'
                    )
                );
            } elseif ($request->status === 'selesai') {
                $query->whereHas(
                    'penilaianBerkas',
                    fn ($q) => $q->where(
                        'status',
                        'selesai'
                    )
                );
            }
        }

        $daftar = $query
            ->paginate(10)
            ->withQueryString();

        $statistik = [
            'mahasiswa' => Pengajuan::query()
                ->where('status', 'diterima')
                ->distinct()
                ->count('user_id'),

            'menunggu' => PenilaianBerkas::query()
                ->where('status', 'menunggu_penilaian')
                ->whereHas('pengajuan', fn ($q) =>
                    $q->where('status', 'diterima')
                )
                ->count(),

            'selesai' => PenilaianBerkas::query()
                ->where('status', 'selesai')
                ->whereHas('pengajuan', fn ($q) =>
                    $q->where('status', 'diterima')
                )
                ->count(),

            'sertifikat' => Sertifikat::query()
                ->where('status', 'terbit')
                ->whereHas('pengajuan', fn ($q) =>
                    $q->where('status', 'diterima')
                )
                ->count(),
        ];

        $tab = in_array(
            $request->query('tab'),
            ['penilaian', 'sertifikat'],
            true
        ) ? $request->query('tab') : 'penilaian';

        return view('admin.penilaian.index', [
            'daftar' => $daftar,
            'statistik' => $statistik,
            'tab' => $tab,
        ]);
    }

    /**
     * Admin mengunduh template dari mahasiswa.
     */
    public function unduhTemplate(PenilaianBerkas $berkas)
    {
        abort_unless(
            $berkas->pengajuan?->status === 'diterima',
            403
        );

        abort_unless(
            $berkas->template_path,
            404,
            'Mahasiswa tidak mengunggah file template.'
        );

        abort_unless(
            Storage::disk('local')->exists($berkas->template_path),
            404,
            'File template tidak ditemukan.'
        );

        return Storage::disk('local')->download(
            $berkas->template_path,
            $berkas->template_nama
                ?: basename($berkas->template_path)
        );
    }

    /**
     * Admin mengirim hasil penilaian ke mahasiswa.
     */
    public function kirimHasil(
        Request $request,
        Pengajuan $pengajuan
    ) {
        abort_unless(
            $pengajuan->status === 'diterima',
            403,
            'Pengajuan belum diterima.'
        );

        $berkas = PenilaianBerkas::query()
            ->where('pengajuan_id', $pengajuan->id)
            ->firstOrFail();

        // Hanya bisa dinilai setelah template dikirim.
        abort_unless(
            $berkas->template_path || $berkas->template_url,
            422,
            'Mahasiswa belum mengirim template penilaian.'
        );

        $validator = Validator::make(
            $request->all(),
            [
                'hasil_file' => [
                    'nullable',
                    'file',
                    'mimes:pdf,doc,docx,xls,xlsx',
                    'max:10240',
                ],
                'hasil_url' => [
                    'nullable',
                    'url:http,https',
                    'max:2000',
                ],
                'catatan_admin' => [
                    'nullable',
                    'string',
                    'max:2000',
                ],
            ],
            [
                'hasil_file.mimes' =>
                    'Hasil penilaian harus PDF, Word, atau Excel.',
                'hasil_file.max' =>
                    'Ukuran file maksimal 10 MB.',
            ]
        );

        $validator->after(function ($validator) use ($request) {
            if (
                !$request->hasFile('hasil_file') &&
                !$request->filled('hasil_url')
            ) {
                $validator->errors()->add(
                    'hasil_file',
                    'Unggah hasil penilaian atau isi tautan Drive.'
                );
            }
        });

        $data = $validator->validate();

        $pathBaru = null;
        $namaBaru = null;

        if ($request->hasFile('hasil_file')) {
            $file = $request->file('hasil_file');

            $pathBaru = $file->store(
                'simasta/hasil-penilaian',
                'local'
            );

            $namaBaru = $file->getClientOriginalName();
        }

        $pathLama = $berkas->hasil_path;

        $berkas->update([
            'hasil_path' => $pathBaru,
            'hasil_nama' => $namaBaru,
            'hasil_url' => $data['hasil_url'] ?? null,
            'hasil_dikirim_at' => now(),
            'catatan_admin' =>
                $data['catatan_admin'] ?? null,
            'status' => 'selesai',
        ]);

        if (
            $pathLama &&
            $pathLama !== $pathBaru &&
            Storage::disk('local')->exists($pathLama)
        ) {
            Storage::disk('local')->delete($pathLama);
        }

        return redirect()
            ->route('admin.penilaian.index')
            ->with(
                'success',
                'Hasil penilaian berhasil dikirim ke mahasiswa.'
            );
    }
}
