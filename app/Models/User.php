<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'role'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    protected $attributes = ['is_active' => true];
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'presensi_migrated_at' => 'datetime',
        ];
    }

    public function isMahasiswa(): bool
    {
        return $this->role === 'mahasiswa';
    }

    public function requiresEmailVerification(): bool
    {
        // Existing presensi accounts keep their previous login access. New
        // SIMASTA registrations must still finish email verification.
        return $this->isMahasiswa() && !$this->email_verified_at && !$this->presensi_migrated_at;
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isPembimbing(): bool
    {
        return $this->role === 'pembimbing';
    }

    public function pengajuan(): HasOne
    {
        return $this->hasOne(Pengajuan::class)->latestOfMany();
    }

    public function mahasiswa(): HasOne
    {
        return $this->hasOne(Mahasiswa::class);
    }

    public function otps(): HasMany
    {
        return $this->hasMany(EmailOtp::class);
    }

    /**
     * Daftar kolom wajib profil, disamakan dengan indikator
     * kelengkapan pada mahasiswa/profil.blade.php.
     */
    public static function kolomProfilWajib(): array
    {
        return [
            'jenis_kelamin',
            'no_telepon',
            'perguruan_tinggi',
            'program_studi',
            'alamat_kampus',
            'nim',
            'semester',
            'dosen_pembimbing',
            'foto',
        ];
    }

    public function profilLengkap(): bool
    {
        if (!$this->isMahasiswa()) {
            return true;
        }

        $mahasiswa = $this->mahasiswa;

        if (!$mahasiswa) {
            return false;
        }

        foreach (self::kolomProfilWajib() as $kolom) {
            if (blank($mahasiswa->{$kolom})) {
                return false;
            }
        }

        return true;
    }

    public function statusMagang(): string
    {
        if (app()->environment('local') && session()->has('preview_status')) {
            return session('preview_status');
        }

        return $this->pengajuan->status ?? 'belum_diajukan';
    }

    public function dashboardUrl(): string
    {
        return match ($this->role) {
            'admin' => route('admin.dashboard'),
            'pembimbing' => route('pembimbing.dashboard'),
            default => route('mahasiswa.dashboard'),
        };
    }
}
