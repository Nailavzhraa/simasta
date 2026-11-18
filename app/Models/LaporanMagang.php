<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LaporanMagang extends Model
{
    protected $table = 'laporan_magangs';

    protected $fillable = [
        'pengajuan_id',
        'user_id',
        'judul',
        'deskripsi',
        'file_nama',
        'file_path',
        'file_url',
        'status',
        'catatan_admin',
        'reviewed_by',
        'dikirim_at',
        'ditinjau_at',
        'disetujui_at',
    ];

    protected $casts = [
        'dikirim_at' => 'datetime',
        'ditinjau_at' => 'datetime',
        'disetujui_at' => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELASI KE PENGAJUAN MAGANG
    |--------------------------------------------------------------------------
    */

    public function pengajuan(): BelongsTo
    {
        return $this->belongsTo(
            Pengajuan::class,
            'pengajuan_id'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | RELASI KE MAHASISWA
    |--------------------------------------------------------------------------
    */

    public function user(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'user_id'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | RELASI KE ADMIN PEMERIKSA
    |--------------------------------------------------------------------------
    */

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'reviewed_by'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | STATUS LAPORAN
    |--------------------------------------------------------------------------
    */

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'menunggu' => 'Menunggu Pemeriksaan',
            'ditinjau' => 'Sedang Ditinjau',
            'revisi' => 'Perlu Revisi',
            'disetujui' => 'Disetujui',
            default => 'Belum Diketahui',
        };
    }
}
