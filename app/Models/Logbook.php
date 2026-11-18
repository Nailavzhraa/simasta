<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Logbook extends Model
{
    protected $table = 'logbooks';

    protected $fillable = [
        'user_id',
        'pengajuan_id',
        'tanggal',
        'kegiatan',
        'capaian',
        'link_bukti',
        'pembelajaran',
        'kendala',
        'status',
        'catatan_admin',
        'ditinjau_at',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'ditinjau_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function pengajuan(): BelongsTo
    {
        return $this->belongsTo(Pengajuan::class);
    }
}
