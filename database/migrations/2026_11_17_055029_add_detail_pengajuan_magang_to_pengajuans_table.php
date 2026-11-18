
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pengajuans', function (Blueprint $table) {

            // Durasi magang berdasarkan jumlah bulan
            $table->unsignedTinyInteger('durasi_bulan')
                ->nullable();

            // Link dokumen Google Drive
            $table->text('link_cv')->nullable();
            $table->text('link_surat')->nullable();
            $table->text('link_proposal')->nullable();

            // Membedakan pendaftaran baru dan susulan
            $table->string('jenis_pendaftaran', 20)
                ->default('reguler');
        });
    }

    public function down(): void
    {
        Schema::table('pengajuans', function (Blueprint $table) {
            $table->dropColumn([
                'durasi_bulan',
                'link_cv',
                'link_surat',
                'link_proposal',
                'jenis_pendaftaran',
            ]);
        });
    }
};
