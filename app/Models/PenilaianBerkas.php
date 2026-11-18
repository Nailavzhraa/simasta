<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PenilaianBerkas extends Model
{
    protected $table = 'penilaian_berkas';

    protected $fillable = [
        'pengajuan_id',
        'user_id',
        'template_nama',
        'template_path',
        'template_url',
        'template_dikirim_at',
        'hasil_nama',
        'hasil_path',
        'hasil_url',
        'hasil_dikirim_at',
        'catatan_mahasiswa',
        'catatan_admin',
        'status',
    ];

    protected $casts = [
        'template_dikirim_at' => 'datetime',
        'hasil_dikirim_at' => 'datetime',
    ];

    public function pengajuan(): BelongsTo
    {
        return $this->belongsTo(Pengajuan::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
