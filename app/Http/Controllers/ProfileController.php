<?php

namespace App\Http\Controllers;

use App\Models\Mahasiswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    public function show()
    {
        $user = auth()->user();

        return view('mahasiswa.profil', [
            'user' => $user,
            'mhs' => $user->mahasiswa,
        ]);
    }

    public function update(Request $request)
    {
        $user = auth()->user();
        $existing = $user->mahasiswa;

        // Semua kolom profil boleh disimpan sebagian.
        // Kelengkapan tetap diperiksa melalui User::profilLengkap().
        $data = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
            ],
            'foto' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048',
            ],
            'jenis_kelamin' => [
                'nullable',
                Rule::in(['Laki-laki', 'Perempuan']),
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
                'nullable',
                'string',
                'max:150',
            ],
            'program_studi' => [
                'nullable',
                'string',
                'max:150',
            ],
            'alamat_kampus' => [
                'nullable',
                'string',
            ],
            'kecamatan_kampus' => [
                'nullable',
                'string',
                'max:100',
            ],
            'kelurahan_kampus' => [
                'nullable',
                'string',
                'max:100',
            ],
            'nim' => [
                'nullable',
                'string',
                'max:30',
                Rule::unique('mahasiswas', 'nim')
                    ->ignore($existing?->id),
            ],
            'semester' => [
                'nullable',
                'integer',
                'min:1',
                'max:14',
            ],
            'tahun_angkatan' => [
                'nullable',
                'string',
                'max:10',
            ],
            'dosen_pembimbing' => [
                'nullable',
                'string',
                'max:150',
            ],
            'no_telp_dosen' => [
                'nullable',
                'string',
                'max:20',
            ],
            'no_wa_dosen' => [
                'nullable',
                'string',
                'max:20',
            ],
            'nik' => [
                'nullable',
                'string',
                'max:30',
            ],
            'alamat' => [
                'nullable',
                'string',
            ],
            'alamat_ktp' => [
                'nullable',
                'string',
            ],
            'alamat_lengkap' => [
                'nullable',
                'string',
            ],
            'provinsi' => [
                'nullable',
                'string',
                'max:150',
            ],
            'kota' => [
                'nullable',
                'string',
                'max:150',
            ],
            'kecamatan' => [
                'nullable',
                'string',
                'max:150',
            ],
            'kelurahan' => [
                'nullable',
                'string',
                'max:150',
            ],
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'foto.image' => 'File harus berupa gambar.',
            'foto.mimes' => 'Foto harus berformat JPG, PNG, atau WEBP.',
            'foto.max' => 'Ukuran foto maksimal 2 MB.',
            'nim.unique' => 'NIM sudah digunakan mahasiswa lain.',
            'semester.min' => 'Semester minimal 1.',
            'semester.max' => 'Semester maksimal 14.',
        ]);

        $nama = trim($data['name']);
        unset($data['name']);

        $fotoLama = $existing?->foto;
        $fotoBaru = null;

        if ($request->hasFile('foto')) {
            $fotoBaru = $request->file('foto')
                ->store('foto-profil', 'public');

            $data['foto'] = $fotoBaru;
        } else {
            unset($data['foto']);
        }

        try {
            DB::transaction(function () use ($user, $nama, $data) {
                // Sinkronisasi nama akun
                $user->update([
                    'name' => $nama,
                ]);

                // Simpan data profil mahasiswa
                Mahasiswa::updateOrCreate(
                    ['user_id' => $user->id],
                    array_merge($data, [
                        'nama' => $nama,
                        'email' => $user->email,
                    ])
                );
            });
        } catch (\Throwable $e) {
            if ($fotoBaru) {
                Storage::disk('public')->delete($fotoBaru);
            }

            throw $e;
        }

        // Hapus foto lama setelah transaksi berhasil
        if ($fotoBaru && $fotoLama && $fotoLama !== $fotoBaru) {
            Storage::disk('public')->delete($fotoLama);
        }

        // Ambil data terbaru
        $user->refresh();

        if ($user->profilLengkap()) {
            return redirect()
                ->route('mahasiswa.profil')
                ->with(
                    'success',
                    'Profil berhasil disimpan dan seluruh data wajib sudah lengkap.'
                );
        }

        return redirect()
            ->route('mahasiswa.profil')
            ->with(
                'warning',
                'Profil berhasil disimpan, tetapi masih ada data wajib yang perlu dilengkapi.'
            );
    }
}
