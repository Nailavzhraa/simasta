<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Sertifikat extends Model
{
    protected $table = 'sertifikats';

    protected $fillable = [
        'pengajuan_id',
        'user_id',
        'template_id',
        'nomor_sertifikat',
        'nama_penerima',
        'nama_instansi',
        'tanggal_mulai',
        'tanggal_selesai',
        'jabatan_penandatangan',
        'nama_penandatangan',
        'nip_penandatangan',
        'file_path',
        'status',
        'tanggal_terbit',
        'diterbitkan_at',
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
        'tanggal_terbit' => 'date',
        'diterbitkan_at' => 'datetime',
    ];

    public function pengajuan(): BelongsTo
    {
        return $this->belongsTo(Pengajuan::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function template(): BelongsTo
    {
        return $this->belongsTo(
            SertifikatTemplate::class,
            'template_id'
        );
    }
}
