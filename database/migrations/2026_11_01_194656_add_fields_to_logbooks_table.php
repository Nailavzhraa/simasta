
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Menambahkan kolom yang dibutuhkan
     * sistem Monitoring Logbook SIMASTA.
     */
    public function up(): void
    {
        Schema::table('logbooks', function (Blueprint $table) {

            // Akun mahasiswa pemilik logbook.
            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            // Pengajuan magang yang terkait.
            $table->foreignId('pengajuan_id')
                ->nullable()
                ->constrained('pengajuans')
                ->nullOnDelete();

            // Tanggal kegiatan magang.
            $table->date('tanggal')->nullable();

            // Isi logbook mahasiswa.
            $table->text('kegiatan')->nullable();
            $table->text('pembelajaran')->nullable();
            $table->text('kendala')->nullable();

            // Status peninjauan oleh admin.
            $table->enum('status', [
                'menunggu',
                'ditinjau',
            ])->default('menunggu');

            // Hasil peninjauan admin.
            $table->text('catatan_admin')->nullable();
            $table->timestamp('ditinjau_at')->nullable();

            // Index untuk mempercepat pencarian.
            $table->index(['pengajuan_id', 'tanggal']);
            $table->index(['user_id', 'status']);
        });
    }

    /**
     * Membatalkan perubahan struktur jika diperlukan.
     */
    public function down(): void
    {
        Schema::table('logbooks', function (Blueprint $table) {

            $table->dropForeign(['user_id']);
            $table->dropForeign(['pengajuan_id']);

            $table->dropIndex(['pengajuan_id', 'tanggal']);
            $table->dropIndex(['user_id', 'status']);

            $table->dropColumn([
                'user_id',
                'pengajuan_id',
                'tanggal',
                'kegiatan',
                'pembelajaran',
                'kendala',
                'status',
                'catatan_admin',
                'ditinjau_at',
            ]);
        });
    }
};
