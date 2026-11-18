<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Divisi extends Model
{
    protected $fillable = [
        'nama',
        'jobdesk',
    ];

    public function pembimbings(): HasMany
    {
        return $this->hasMany(Pembimbing::class);
    }

    public function timKerjas(): HasMany
    {
        return $this->hasMany(TimKerja::class);
    }

    public function pengajuans(): HasMany
    {
        return $this->hasMany(Pengajuan::class);
    }
}
