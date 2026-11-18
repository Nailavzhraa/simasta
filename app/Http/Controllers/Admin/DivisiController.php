<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Divisi;
use App\Models\TimKerja;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class DivisiController extends Controller
{
    public function index()
    {
        return view('admin.divisi.index', [
            'divisis' => Divisi::withCount([
                'pembimbings',
                'timKerjas',
            ])->orderBy('nama')->get(),

            'timKerjas' => TimKerja::with('divisi')
                ->orderBy('nama')
                ->get(),
        ]);
    }

    public function simpanDivisi(Request $request)
    {
        $data = $request->validate([
            'nama' => [
                'required', 'string', 'max:150',
                Rule::unique('divisis', 'nama'),
            ],
            'jobdesk' => 'nullable|string|max:2000',
        ], [
            'nama.required' => 'Nama divisi wajib diisi.',
            'nama.unique' => 'Nama divisi sudah ada.',
        ]);

        Divisi::create($data);

        return redirect()->route('admin.divisi.index', [
            'tab' => 'divisi',
        ])->with('success', 'Divisi berhasil ditambahkan.');
    }

    public function updateDivisi(Request $request, Divisi $divisi)
    {
        $data = $request->validate([
            'nama' => [
                'required',
                'string',
                'max:150',
                Rule::unique('divisis', 'nama')->ignore($divisi->id),
            ],
            'jobdesk' => 'nullable|string|max:2000',
        ], [
            'nama.required' => 'Nama divisi wajib diisi.',
            'nama.unique' => 'Nama divisi sudah ada.',
        ]);

        $divisi->update($data);

        return redirect()->route('admin.divisi.index', [
            'tab' => 'divisi',
        ])->with('success', 'Divisi berhasil diperbarui.');
    }

    public function hapusDivisi(Divisi $divisi)
    {
        if ($divisi->pembimbings()->exists()) {
            return back()->with(
                'error',
                'Divisi tidak dapat dihapus karena masih memiliki pembimbing.'
            );
        }

        if ($divisi->timKerjas()->exists()) {
            return back()->with(
                'error',
                'Divisi tidak dapat dihapus karena masih memiliki tim kerja.'
            );
        }

        if ($divisi->pengajuans()->exists()) {
            return back()->with(
                'error',
                'Divisi tidak dapat dihapus karena masih digunakan dalam pengajuan magang.'
            );
        }

        $divisi->delete();

        return redirect()->route('admin.divisi.index', [
            'tab' => 'divisi',
        ])->with('success', 'Divisi berhasil dihapus.');
    }

    public function simpanTimKerja(Request $request)
    {
        $data = $request->validate([
            'divisi_id' => 'required|exists:divisis,id',
            'nama' => [
                'required',
                'string',
                'max:150',
                Rule::unique('tim_kerjas', 'nama')
                    ->where('divisi_id', $request->input('divisi_id')),
            ],
            'deskripsi' => 'nullable|string|max:2000',
        ], [
            'divisi_id.required' => 'Pilih divisi terlebih dahulu.',
            'divisi_id.exists' => 'Divisi tidak ditemukan.',
            'nama.required' => 'Nama tim kerja wajib diisi.',
            'nama.unique' => 'Tim kerja sudah ada pada divisi tersebut.',
        ]);

        TimKerja::create($data);

        return redirect()->route('admin.divisi.index', [
            'tab' => 'tim',
        ])->with('success', 'Tim kerja berhasil ditambahkan.');
    }

    public function updateTimKerja(Request $request, TimKerja $timKerja)
    {
        $data = $request->validate([
            'divisi_id' => 'required|exists:divisis,id',
            'nama' => [
                'required',
                'string',
                'max:150',
                Rule::unique('tim_kerjas', 'nama')
                    ->where('divisi_id', $request->input('divisi_id'))
                    ->ignore($timKerja->id),
            ],
            'deskripsi' => 'nullable|string|max:2000',
        ], [
            'divisi_id.required' => 'Pilih divisi terlebih dahulu.',
            'divisi_id.exists' => 'Divisi tidak ditemukan.',
            'nama.required' => 'Nama tim kerja wajib diisi.',
            'nama.unique' => 'Tim kerja sudah ada pada divisi tersebut.',
        ]);

        $timKerja->update($data);

        return redirect()->route('admin.divisi.index', [
            'tab' => 'tim',
        ])->with('success', 'Tim kerja berhasil diperbarui.');
    }

    public function hapusTimKerja(TimKerja $timKerja)
    {
        $timKerja->delete();

        return redirect()->route('admin.divisi.index', [
            'tab' => 'tim',
        ])->with('success', 'Tim kerja berhasil dihapus.');
    }
}
