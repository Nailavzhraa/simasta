<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Divisi;
use App\Models\Pembimbing;
use Illuminate\Http\Request;

class PembimbingController extends Controller
{
    public function index(Request $request)
    {
        $query = Pembimbing::with('divisi')->orderBy('nama');

        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($w) use ($q) {
                $w->where('nama', 'like', "%{$q}%")
                  ->orWhere('nip', 'like', "%{$q}%");
            });
        }

        return view('admin.pembimbing.index', [
            'pembimbings' => $query->paginate(10)->withQueryString(),
        ]);
    }

    public function create()
    {
        return view('admin.pembimbing.create', [
            'divisis' => Divisi::orderBy('nama')->get(),
        ]);
    }

    public function store(Request $request)
    {
        Pembimbing::create($this->validasi($request));

        return redirect()->route('admin.pembimbing.index')
            ->with('success', 'Pembimbing berhasil ditambahkan.');
    }

    // Form edit memakai view yang sama dengan create
    public function edit(Pembimbing $pembimbing)
    {
        return view('admin.pembimbing.create', [
            'pembimbing' => $pembimbing,
            'divisis'    => Divisi::orderBy('nama')->get(),
        ]);
    }

    public function update(Request $request, Pembimbing $pembimbing)
    {
        $pembimbing->update($this->validasi($request));

        return redirect()->route('admin.pembimbing.index')
            ->with('success', 'Data pembimbing berhasil diperbarui.');
    }

    public function destroy(Pembimbing $pembimbing)
    {
        $pembimbing->delete();

        return redirect()->route('admin.pembimbing.index')
            ->with('success', 'Pembimbing berhasil dihapus.');
    }

    private function validasi(Request $request): array
    {
        return $request->validate([
            'nama'      => 'required|string|max:150',
            'nip'       => 'nullable|string|max:50',
            'divisi_id' => 'nullable|exists:divisis,id',
        ], [
            'nama.required' => 'Nama pembimbing wajib diisi.',
        ]);
    }
}