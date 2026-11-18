
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengajuan_drafts', function (Blueprint $table) {
            $table->id();

            // Setiap mahasiswa memiliki satu draft aktif
            $table->foreignId('user_id')
                ->unique()
                ->constrained('users')
                ->cascadeOnDelete();

            // Menyimpan data formulir sementara
            $table->json('form_data')->nullable();

            // Tahapan terakhir yang sedang dikerjakan
            $table->unsignedTinyInteger('current_step')
                ->default(1);

            // Waktu draft dibuat dan diperbarui
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengajuan_drafts');
    }
};
