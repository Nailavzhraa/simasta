<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\OtpService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;

class OtpController extends Controller
{
    private function pendingUser(): ?User
    {
        $id = session('otp_user_id');

        if (!$id) {
            return null;
        }

        return User::query()
            ->whereKey($id)
            ->where('role', 'mahasiswa')
            ->first();
    }

    private function resendKey(User $user): string
    {
        return 'otp-resend:' . $user->id;
    }

    private function resendSeconds(User $user): int
    {
        $otp = $user->otps()->latest('id')->first();

        // Jeda 60 detik sejak OTP terakhir dibuat.
        $sinceLastSend = $otp
            ? max(0, $otp->created_at->timestamp + 60 - now()->timestamp)
            : 0;

        // Jeda tambahan dari Laravel RateLimiter.
        $rateLimit = RateLimiter::availableIn(
            $this->resendKey($user)
        );

        return max($sinceLastSend, $rateLimit);
    }

    public function show()
    {
        $user = $this->pendingUser();

        if (!$user) {
            return redirect()->route('login')
                ->withErrors([
                    'email' => 'Sesi verifikasi tidak ditemukan. Silakan login kembali.',
                ]);
        }

        if ($user->email_verified_at) {
            session()->forget('otp_user_id');

            return redirect()->route('login')
                ->with('success', 'Email sudah diverifikasi. Silakan login.');
        }

        $otp = $user->otps()->latest('id')->first();

        return view('auth.otp', [
            'email' => $user->email,
            'expiresAt' => $otp?->expires_at?->timestamp,
            'serverNow' => now()->timestamp,
            'resendSeconds' => $this->resendSeconds($user),
        ]);
    }

    public function verify(Request $request)
    {
        $data = $request->validate([
            'code' => ['required', 'digits:6'],
        ], [
            'code.required' => 'Kode OTP wajib diisi.',
            'code.digits' => 'Kode OTP harus terdiri dari 6 angka.',
        ]);

        $user = $this->pendingUser();

        if (!$user) {
            return redirect()->route('login')
                ->withErrors([
                    'email' => 'Sesi verifikasi berakhir. Silakan login kembali.',
                ]);
        }

        if ($user->email_verified_at) {
            session()->forget('otp_user_id');

            return redirect()->route('login')
                ->with('success', 'Email sudah terverifikasi.');
        }

        if (!OtpService::verify($user, $data['code'])) {
            return back()->withErrors([
                'code' => 'Kode salah, kedaluwarsa, atau batas percobaan tercapai.',
            ]);
        }

        $request->session()->forget('otp_user_id');

        RateLimiter::clear($this->resendKey($user));

        Auth::login($user);

        $request->session()->regenerate();

        return redirect($user->dashboardUrl())
            ->with(
                'success',
                'Email berhasil diverifikasi. Selamat datang di SIMASTA!'
            );
    }

    public function resend(Request $request)
    {
        $user = $this->pendingUser();

        if (!$user) {
            return redirect()->route('login')
                ->withErrors([
                    'email' => 'Sesi OTP berakhir. Silakan login kembali.',
                ]);
        }

        if ($user->email_verified_at) {
            $request->session()->forget('otp_user_id');

            return redirect()->route('login')
                ->with('success', 'Email sudah terverifikasi.');
        }

        $seconds = $this->resendSeconds($user);

        if ($seconds > 0) {
            return back()->withErrors([
                'code' => "Tunggu {$seconds} detik sebelum meminta kode baru.",
            ]);
        }

        $key = $this->resendKey($user);

        // Kunci sementara sebelum proses pengiriman.
        RateLimiter::hit($key, 60);

        try {
            OtpService::send($user);
        } catch (\Throwable $e) {
            report($e);

            return back()->withErrors([
                'code' => 'Kode belum berhasil dikirim. Silakan coba lagi nanti.',
            ]);
        }

        return redirect()->route('otp.show')
            ->with(
                'success',
                'Kode OTP baru berhasil dibuat. Gunakan kode terbaru.'
            );
    }
}
