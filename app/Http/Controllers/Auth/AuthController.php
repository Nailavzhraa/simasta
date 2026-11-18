<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\OtpService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | REGISTER MAHASISWA
    |--------------------------------------------------------------------------
    | Registrasi cukup data akun.
    | Data kampus dilengkapi melalui Profil Mahasiswa.
    |--------------------------------------------------------------------------
    */

    public function register(Request $r)
    {
        $data = $r->validate([
            'nama_lengkap' => [
                'required',
                'string',
                'max:150',
            ],
            'no_telp' => [
                'required',
                'string',
                'max:20',
            ],
            'no_wa' => [
                'nullable',
                'string',
                'max:20',
            ],
            'email' => [
                'required',
                'email',
                'max:255',
                'unique:users,email',
                'unique:mahasiswas,email',
            ],
            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
            ],
        ], [
            'nama_lengkap.required' =>
                'Nama lengkap wajib diisi.',

            'no_telp.required' =>
                'Nomor telepon wajib diisi.',

            'email.required' =>
                'Email wajib diisi.',

            'email.unique' =>
                'Email sudah terdaftar.',

            'password.required' =>
                'Password wajib diisi.',

            'password.min' =>
                'Password minimal 8 karakter.',

            'password.confirmed' =>
                'Konfirmasi password tidak sesuai.',
        ]);

        /*
        |--------------------------------------------------------------------------
        | SIMPAN AKUN DAN PROFIL MAHASISWA
        |--------------------------------------------------------------------------
        */

        $user = DB::transaction(function () use ($data) {

            $user = User::create([
                'name' => $data['nama_lengkap'],
                'email' => $data['email'],
                'password' => $data['password'],
                'role' => 'mahasiswa',
            ]);

            $user->mahasiswa()->create([
                'nama' => $data['nama_lengkap'],
                'email' => $data['email'],
                'no_telepon' => $data['no_telp'],
                'no_telp' => $data['no_telp'],
                'no_wa' => $data['no_wa'] ?? null,

                // Data kampus sengaja belum diisi.
                'nim' => null,
                'perguruan_tinggi' => null,
                'program_studi' => null,
                'alamat_kampus' => null,
                'kelurahan_kampus' => null,
                'kecamatan_kampus' => null,
                'dosen_pembimbing' => null,
                'no_telp_dosen' => null,
                'no_wa_dosen' => null,
            ]);

            return $user;
        });

        /*
        |--------------------------------------------------------------------------
        | KIRIM OTP
        |--------------------------------------------------------------------------
        */

        session(['otp_user_id' => $user->id]);

        OtpService::send($user);

        return redirect()
            ->route('otp.show')
            ->with(
                'success',
                'Kode OTP verifikasi telah dibuat. '
                . 'Silakan periksa email atau log pengujian.'
            );
    }

    /*
    |--------------------------------------------------------------------------
    | LOGIN
    |--------------------------------------------------------------------------
    */

    public function login(Request $r)
    {
        $cred = $r->validate([
            'email' => [
                'required',
                'email',
            ],
            'password' => [
                'required',
            ],
        ]);

        if (!Auth::attempt($cred)) {
            return back()
                ->withErrors([
                    'email' =>
                        'Email atau password salah.',
                ])
                ->onlyInput('email');
        }

        $user = Auth::user();

        /*
        |--------------------------------------------------------------------------
        | WAJIB VERIFIKASI OTP
        |--------------------------------------------------------------------------
        */

        if (
            $user->role === 'mahasiswa' &&
            !$user->email_verified_at
        ) {
            Auth::logout();

            session(['otp_user_id' => $user->id]);

            OtpService::send($user);

            return redirect()
                ->route('otp.show')
                ->with(
                    'success',
                    'Email belum diverifikasi. '
                    . 'Kode OTP baru telah dibuat.'
                );
        }


        $r->session()->regenerate();

        // Mahasiswa wajib melengkapi profil
        if ($user->isMahasiswa()) {

            if (!$user->profilLengkap()) {
                return redirect()
                    ->route('mahasiswa.profil')
                    ->with(
                        'warning',
                        'Selamat datang di SIMASTA! Silakan lengkapi profil terlebih dahulu sebelum mengajukan magang.'
                    );
            }

            return redirect()->route('mahasiswa.dashboard');
        }

        // Admin dan role lainnya
        return redirect()->intended($user->dashboardUrl());
    }


    /*
    |--------------------------------------------------------------------------
    | LOGOUT
    |--------------------------------------------------------------------------
    */

    public function logout(Request $r)
    {
        Auth::logout();

        $r->session()->invalidate();
        $r->session()->regenerateToken();

        return redirect('/login')
            ->with(
                'success',
                'Anda berhasil logout.'
            );
    }
}
