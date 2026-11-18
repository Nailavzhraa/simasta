<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Divisi;
use App\Models\Pembimbing;
use App\Models\Pengajuan;
use App\Models\TimKerja;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Carbon;

class PenempatanController extends Controller
{
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
            ->latest('updated_at');

        // Pencarian mahasiswa
        if ($request->filled('q')) {
            $keyword = trim($request->input('q'));

            $query->where(function ($q) use ($keyword) {
                $q->whereHas('user', function ($user) use ($keyword) {
                    $user->where('name', 'like', "%{$keyword}%")
                        ->orWhere('email', 'like', "%{$keyword}%");
                })->orWhere('instansi', 'like', "%{$keyword}%");
            });
        }

        // Filter status penempatan
        if ($request->input('status') === 'sudah') {
            $query->whereNotNull('divisi_id')
                ->whereNotNull('pembimbing_id');
        }

        if ($request->input('status') === 'belum') {
            $query->where(function ($q) {
                $q->whereNull('divisi_id')
                    ->orWhereNull('pembimbing_id');
            });
        }

        // Filter divisi
        if ($request->filled('divisi_id')) {
            $query->where('divisi_id', $request->input('divisi_id'));
        }

        // Statistik penempatan
        $total = Pengajuan::where('status', 'diterima')->count();

        $sudah = Pengajuan::where('status', 'diterima')
            ->whereNotNull('divisi_id')
            ->whereNotNull('pembimbing_id')
            ->count();

        $belum = $total - $sudah;

        $divisiTerisi = Pengajuan::where('status', 'diterima')
            ->whereNotNull('divisi_id')
            ->whereNotNull('pembimbing_id')
            ->distinct()
            ->count('divisi_id');

        return view('admin.penempatan.index', [
            'penempatans' => $query
                ->paginate(10)
                ->withQueryString(),

            'divisis' => Divisi::orderBy('nama')->get(),

            'timKerjas' => TimKerja::orderBy('nama')->get(),

            'pembimbings' => Pembimbing::orderBy('nama')->get(),

            'statistik' => [
                'total' => $total,
                'sudah' => $sudah,
                'belum' => $belum,
                'divisi' => $divisiTerisi,
            ],
        ]);
    }

    public function simpan(Request $request, Pengajuan $pengajuan)
    {
        if ($pengajuan->status !== 'diterima') {
            return redirect()
                ->route('admin.penempatan.index')
                ->with(
                    'error',
                    'Hanya mahasiswa yang diterima yang dapat ditempatkan.'
                );
        }

        // Penempatan tidak boleh diubah setelah magang dimulai
        if ($pengajuan->status_pelaksanaan !== 'belum_mulai') {
            return back()->with(
                'error',
                'Penempatan tidak dapat diubah setelah magang dimulai.'
            );
        }

        $data = $request->validate([
            'divisi_id' => [
                'required',
                'exists:divisis,id',
            ],
            'tim_kerja_id' => [
                'nullable',
                'exists:tim_kerjas,id',
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
        ]);

        $divisi = Divisi::findOrFail($data['divisi_id']);
        $pembimbing = Pembimbing::findOrFail($data['pembimbing_id']);

        // Validasi pembimbing sesuai divisi
        if (
            $pembimbing->divisi_id !== null &&
            (int) $pembimbing->divisi_id !== (int) $divisi->id
        ) {
            throw ValidationException::withMessages([
                'pembimbing_id' =>
                    'Pembimbing tidak sesuai dengan divisi yang dipilih.',
            ]);
        }

        $timKerjaId = $data['tim_kerja_id'] ?? null;

        // Validasi tim kerja sesuai divisi
        if ($timKerjaId !== null) {
            $timKerja = TimKerja::findOrFail($timKerjaId);

            if ((int) $timKerja->divisi_id !== (int) $divisi->id) {
                throw ValidationException::withMessages([
                    'tim_kerja_id' =>
                        'Tim Kerja tidak sesuai dengan divisi yang dipilih.',
                ]);
            }
        }

        $pengajuan->update([
            'divisi_id' => $divisi->id,
            'tim_kerja_id' => $timKerjaId,
            'pembimbing_id' => $pembimbing->id,
            'jobdesk' => $data['jobdesk'],

            // Kompatibilitas tampilan mahasiswa
            'bidang' => $divisi->nama,
            'pembimbing' => $pembimbing->nama,
        ]);

        return redirect()
            ->route('admin.penempatan.index')
            ->with(
                'success',
                'Penempatan mahasiswa berhasil disimpan.'
            );
    }

    // Konfirmasi mulai magang
    public function mulai(Request $request, Pengajuan $pengajuan)
    {
        if (
            $pengajuan->status !== 'diterima' ||
            !$pengajuan->divisi_id ||
            !$pengajuan->pembimbing_id
        ) {
            return back()->with(
                'error',
                'Mahasiswa harus diterima dan ditempatkan terlebih dahulu.'
            );
        }

        if ($pengajuan->status_pelaksanaan !== 'belum_mulai') {
            return back()->with(
                'error',
                'Magang sudah dimulai atau telah selesai.'
            );
        }

        $data = $request->validate([
            'tanggal_mulai_aktual' => [
                'required',
                'date',
                'before_or_equal:today',
            ],
        ]);

        $pengajuan->update([
            'status_pelaksanaan' => 'berlangsung',
            'tanggal_mulai_aktual' => $data['tanggal_mulai_aktual'],
            'tanggal_selesai_aktual' => null,
        ]);

        return back()->with(
            'success',
            'Status pelaksanaan berhasil diubah menjadi Sedang Magang.'
        );
    }

    // Konfirmasi selesai magang
    public function selesai(Request $request, Pengajuan $pengajuan)
    {
        if ($pengajuan->status_pelaksanaan !== 'berlangsung') {
            return back()->with(
                'error',
                'Hanya mahasiswa yang sedang magang yang dapat diselesaikan.'
            );
        }

        if (!$pengajuan->tanggal_mulai_aktual) {
            return back()->with(
                'error',
                'Tanggal mulai aktual belum tersedia.'
            );
        }

        $data = $request->validate([
            'tanggal_selesai_aktual' => [
                'required',
                'date',
                'after_or_equal:' .
                    $pengajuan->tanggal_mulai_aktual->format('Y-m-d'),
                'before_or_equal:today',
            ],
        ]);

        $pengajuan->update([
            'status_pelaksanaan' => 'selesai',
            'tanggal_selesai_aktual' => $data['tanggal_selesai_aktual'],
        ]);

        return back()->with(
            'success',
            'Status pelaksanaan berhasil diubah menjadi Selesai Magang.'
        );
    }
}
