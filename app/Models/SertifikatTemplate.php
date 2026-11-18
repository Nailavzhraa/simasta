<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SertifikatTemplate extends Model
{
    protected $table = 'sertifikat_templates';

    protected $fillable = [
        'nama',
        'file_path',
        'file_asli',
        'tipe_file',
        'konfigurasi',
        'is_active',
    ];

    protected $casts = [
        'konfigurasi' => 'array',
        'is_active' => 'boolean',
    ];

    public function sertifikats(): HasMany
    {
        return $this->hasMany(
            Sertifikat::class,
            'template_id'
        );
    }
}
