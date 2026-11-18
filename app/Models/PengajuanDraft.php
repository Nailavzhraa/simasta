<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PengajuanDraft extends Model
{
    protected $table = 'pengajuan_drafts';

    protected $fillable = [
        'user_id',
        'form_data',
        'current_step',
    ];

    protected $casts = [
        'form_data' => 'array',
        'current_step' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
