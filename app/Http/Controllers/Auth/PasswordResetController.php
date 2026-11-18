<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Auth\Events\PasswordReset;

class PasswordResetController extends Controller
{
    /**
     * Menampilkan halaman lupa kata sandi.
     */
    public function showLinkRequestForm()
    {
        return view('auth.passwords.email');
    }

    /**
     * Mengirim tautan reset ke email mahasiswa.
     */
    public function sendResetLinkEmail(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
        ], [
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
        ]);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        if ($status === Password::RESET_LINK_SENT) {
            return back()->with(
                'status',
                'Tautan reset kata sandi berhasil dikirim. Silakan periksa email Anda.'
            );
        }

        return back()
            ->withInput($request->only('email'))
            ->withErrors([
                'email' => $status === Password::RESET_THROTTLED
                    ? 'Silakan tunggu sebelum meminta tautan reset kembali.'
                    : 'Email tidak ditemukan dalam sistem SIMASTA.',
            ]);
    }

    /**
     * Menampilkan halaman untuk membuat kata sandi baru.
     */
    public function showResetForm(Request $request, string $token)
    {
        return view('auth.passwords.reset', [
            'token' => $token,
            'email' => $request->query('email'),
        ]);
    }

    /**
     * Memproses perubahan kata sandi.
     */
    public function reset(Request $request)
    {
        $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
            ],
        ], [
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'password.required' => 'Kata sandi baru wajib diisi.',
            'password.min' => 'Kata sandi minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
        ]);

        $status = Password::reset(
            $request->only(
                'email',
                'password',
                'password_confirmation',
                'token'
            ),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return redirect()
                ->route('login')
                ->with(
                    'status',
                    'Kata sandi berhasil diperbarui. Silakan masuk menggunakan kata sandi baru.'
                );
        }

        return back()->withErrors([
            'email' => $status === Password::INVALID_TOKEN
                ? 'Tautan reset tidak valid atau sudah kedaluwarsa.'
                : 'Email tidak ditemukan.',
        ]);
    }
}
