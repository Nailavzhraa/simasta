<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Pengajuan extends Model
{
    /**
     * Kolom yang boleh diisi melalui Eloquent.
     */
    protected $fillable = [

        // =====================================
        // DATA PENGAJUAN MAGANG
        // =====================================
        'user_id',
        'instansi',
        'bidang_diminati',
        'periode_mulai',
        'periode_selesai',
        'durasi_bulan',
        'jenis_pendaftaran',
        'motivasi',

        // =====================================
        // DOKUMEN MAGANG LAMA (FILE LOKAL)
        // =====================================
        'file_surat',
        'file_cv',
        'file_proposal',

        // =====================================
        // DOKUMEN MAGANG BARU (GOOGLE DRIVE)
        // =====================================
        'link_surat',
        'link_cv',
        'link_proposal',

        // =====================================
        // TUJUAN DAN KETERANGAN MAHASISWA
        // =====================================
        'tujuan_magang',
        'keterangan_tambahan',

        // =====================================
        // DATA PENEMPATAN MAGANG
        // =====================================
        'bidang',
        'pembimbing',
        'divisi_id',
        'tim_kerja_id',
        'pembimbing_id',
        'jobdesk',

        // =====================================
        // VERIFIKASI ADMIN
        // =====================================
        'status',
        'catatan_admin',

        // =====================================
        // PELAKSANAAN MAGANG
        // =====================================
        'status_pelaksanaan',
        'tanggal_mulai_aktual',
        'tanggal_selesai_aktual',
    ];

    /**
     * Konversi tipe data otomatis.
     */
    protected $casts = [
        'periode_mulai' => 'date',
        'periode_selesai' => 'date',
        'durasi_bulan' => 'integer',
        'tanggal_mulai_aktual' => 'date',
        'tanggal_selesai_aktual' => 'date',
    ];

    // =========================================
    // RELASI AKUN MAHASISWA
    // =========================================

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // =========================================
    // RELASI PENEMPATAN MAGANG
    // =========================================

    public function divisi(): BelongsTo
    {
        return $this->belongsTo(Divisi::class);
    }

    public function timKerja(): BelongsTo
    {
        return $this->belongsTo(
            TimKerja::class,
            'tim_kerja_id'
        );
    }

    public function pembimbingRef(): BelongsTo
    {
        return $this->belongsTo(
            Pembimbing::class,
            'pembimbing_id'
        );
    }

    // =========================================
    // RELASI KEGIATAN MAGANG
    // =========================================

    public function logbooks(): HasMany
    {
        return $this->hasMany(
            Logbook::class,
            'pengajuan_id'
        );
    }

    // =========================================
    // RELASI PENILAIAN DAN SERTIFIKAT
    // =========================================

    public function penilaianBerkas(): HasOne
    {
        return $this->hasOne(
            PenilaianBerkas::class,
            'pengajuan_id'
        );
    }

    public function sertifikat(): HasOne
    {
        return $this->hasOne(
            Sertifikat::class,
            'pengajuan_id'
        );
    }
}
