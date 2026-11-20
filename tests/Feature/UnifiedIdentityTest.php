<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class UnifiedIdentityTest extends TestCase
{
    use RefreshDatabase;

    public function test_migrated_presensi_password_logs_in_without_changing_its_legacy_hash(): void
    {
        Mail::fake();
        $user = User::factory()->unverified()->create(['role' => 'mahasiswa']);
        DB::table('users')->where('id', $user->id)->update([
            'password' => 'scrypt$32768$8$1$0123456789abcdef0123456789abcdef$9b8e9751d13bd52e2f28b49e02debbbfdd9ae3bef519c05b34d522a6782800e1597125863763950c19055440c4b462ff2d226b6c98731b9faefc2340e7a0b005',
            'presensi_migrated_at' => now(),
        ]);
        $this->post('/login', ['email' => strtoupper($user->email), 'password' => 'wrong'])->assertSessionHasErrors('email');
        $this->assertGuest();
        $this->post('/login', ['email' => ' '.$user->email.' ', 'password' => 'Legacy-password-2026'])->assertRedirect('/mahasiswa/profil');
        $this->assertAuthenticatedAs($user);
        $this->assertStringStartsWith('scrypt$', $user->fresh()->password);
        $this->assertTrue(Hash::check('Legacy-password-2026', $user->fresh()->password));
        $this->assertNull($user->fresh()->email_verified_at);
        Mail::assertNothingSent();
    }

    public function test_new_accounts_still_require_email_verification(): void
    {
        Mail::fake();
        $user = User::factory()->unverified()->create(['role' => 'mahasiswa', 'password' => 'New-password-2026']);
        $this->post('/login', ['email' => $user->email, 'password' => 'New-password-2026'])->assertRedirect('/verifikasi-otp');
        $this->assertGuest();
        Mail::assertSent(\App\Mail\OtpMail::class);
    }

    public function test_inactive_accounts_cannot_log_in_or_keep_an_authenticated_session(): void
    {
        $user = User::factory()->create(['role' => 'admin', 'password' => 'Admin-password-2026']);
        $user->forceFill(['is_active' => false])->save();
        $this->post('/login', ['email' => $user->email, 'password' => 'Admin-password-2026'])->assertSessionHasErrors('email');
        $this->assertGuest();
        $this->actingAs($user)->get('/admin/dashboard')->assertRedirect('/login');
        $this->assertGuest();
    }

    public function test_google_only_legacy_accounts_do_not_accept_a_password(): void
    {
        $this->assertFalse(Hash::check('anything', '!google-only'));
    }

    public function test_new_passwords_cannot_silently_exceed_bcrypt_byte_limit(): void
    {
        $password = str_repeat('a', 73);
        $this->post('/register', [
            'nama_lengkap' => 'Password limit', 'no_telp' => '081234567890',
            'email' => 'password-limit@example.com', 'password' => $password,
            'password_confirmation' => $password,
        ])->assertSessionHasErrors('password');
        $this->assertDatabaseMissing('users', ['email' => 'password-limit@example.com']);
    }

    public function test_long_legacy_passwords_keep_their_full_length_verification(): void
    {
        $hash = 'scrypt$32768$8$1$0123456789abcdef0123456789abcdef$fc304832d6d44ab0c3a8b107837c6702e071125be6e8e9c29e81b7e4330ee9d9a7ee502fcba29946135136fa69fa87952e7e48192eb1e220ae9b5d4bf0e9d374';
        $this->assertTrue(Hash::check(str_repeat('a', 72).'tail', $hash));
        $this->assertFalse(Hash::check(str_repeat('a', 72).'wrong-tail', $hash));
        $this->assertFalse(Hash::needsRehash($hash));
    }
}
