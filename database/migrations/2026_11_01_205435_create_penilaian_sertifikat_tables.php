
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        /*
        |--------------------------------------------------------------------------
        | 1. PENILAIAN BERKAS
        |--------------------------------------------------------------------------
        | Template dari universitas -> hasil dari BPS
        */

        Schema::create('penilaian_berkas', function (Blueprint $table) {
            $table->id();

            $table->foreignId('pengajuan_id')
                ->constrained('pengajuans')
                ->cascadeOnDelete();

            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            // Template yang dikirim mahasiswa.
            $table->string('template_nama')->nullable();
            $table->string('template_path')->nullable();
            $table->text('template_url')->nullable();
            $table->timestamp('template_dikirim_at')->nullable();

            // Hasil penilaian dari admin BPS.
            $table->string('hasil_nama')->nullable();
            $table->string('hasil_path')->nullable();
            $table->text('hasil_url')->nullable();
            $table->timestamp('hasil_dikirim_at')->nullable();

            // Catatan komunikasi.
            $table->text('catatan_mahasiswa')->nullable();
            $table->text('catatan_admin')->nullable();

            $table->enum('status', [
                'menunggu_template',
                'menunggu_penilaian',
                'selesai',
            ])->default('menunggu_template');

            $table->timestamps();

            // Satu pengajuan punya satu proses penilaian aktif.
            $table->unique('pengajuan_id');
            $table->index(['user_id', 'status']);
        });

        /*
        |--------------------------------------------------------------------------
        | 2. TEMPLATE SERTIFIKAT
        |--------------------------------------------------------------------------
        | Dokumen mentahan yang diunggah admin.
        */

        Schema::create('sertifikat_templates', function (Blueprint $table) {
            $table->id();

            $table->string('nama');
            $table->string('file_path');
            $table->string('file_asli')->nullable();
            $table->string('tipe_file')->nullable();

            // Posisi elemen sertifikat untuk generator.
            $table->json('konfigurasi')->nullable();

            $table->boolean('is_active')->default(false);

            $table->timestamps();
        });

        /*
        |--------------------------------------------------------------------------
        | 3. SERTIFIKAT MAHASISWA
        |--------------------------------------------------------------------------
        | Satu sertifikat per pengajuan magang.
        */

        Schema::create('sertifikats', function (Blueprint $table) {
            $table->id();

            $table->foreignId('pengajuan_id')
                ->constrained('pengajuans')
                ->cascadeOnDelete();

            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->foreignId('template_id')
                ->nullable()
                ->constrained('sertifikat_templates')
                ->nullOnDelete();

            $table->string('nomor_sertifikat')
                ->nullable()
                ->unique();

            $table->string('nama_penerima');
            $table->string('nama_instansi')
                ->default('BPS Kota Batu');

            $table->date('tanggal_mulai')->nullable();
            $table->date('tanggal_selesai')->nullable();

            $table->string('jabatan_penandatangan')
                ->nullable();

            $table->string('nama_penandatangan')
                ->nullable();

            $table->string('nip_penandatangan')
                ->nullable();

            // File PDF akhir.
            $table->string('file_path')->nullable();

            $table->enum('status', [
                'draft',
                'terbit',
            ])->default('draft');

            $table->date('tanggal_terbit')->nullable();
            $table->timestamp('diterbitkan_at')->nullable();

            $table->timestamps();

            $table->unique('pengajuan_id');
            $table->index(['user_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sertifikats');
        Schema::dropIfExists('sertifikat_templates');
        Schema::dropIfExists('penilaian_berkas');
    }
};
