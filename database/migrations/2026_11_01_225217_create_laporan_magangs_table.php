<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('laporan_magangs', function (Blueprint $table) {
            $table->id();

            // Hubungan dengan pengajuan magang.
            $table->foreignId('pengajuan_id')
                ->constrained('pengajuans')
                ->cascadeOnDelete();

            // Pemilik laporan mahasiswa.
            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            // Informasi laporan.
            $table->string('judul', 255);
            $table->text('deskripsi')->nullable();

            // Dokumen laporan yang diunggah.
            $table->string('file_nama')->nullable();
            $table->string('file_path')->nullable();

            // Alternatif Google Drive.
            $table->text('file_url')->nullable();

            // Status pemeriksaan.
            $table->enum('status', [
                'menunggu',
                'ditinjau',
                'revisi',
                'disetujui',
            ])->default('menunggu');

            // Catatan atau masukan dari BPS.
            $table->text('catatan_admin')->nullable();

            // Admin yang terakhir memeriksa.
            $table->foreignId('reviewed_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            // Riwayat waktu.
            $table->timestamp('dikirim_at')->nullable();
            $table->timestamp('ditinjau_at')->nullable();
            $table->timestamp('disetujui_at')->nullable();

            $table->timestamps();

            // Satu laporan akhir aktif per pengajuan.
            $table->unique('pengajuan_id');

            $table->index(['user_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('laporan_magangs');
    }
};
