<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pembimbing extends Model
{
    protected $fillable = ['nama', 'nip', 'divisi_id'];

    public function divisi(): BelongsTo
    {
        return $this->belongsTo(Divisi::class);
    }
}