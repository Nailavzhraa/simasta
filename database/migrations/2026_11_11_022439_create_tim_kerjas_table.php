
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tim_kerjas', function (Blueprint $table) {
            $table->id();

            $table->foreignId('divisi_id')
                ->constrained('divisis')
                ->restrictOnDelete();

            $table->string('nama', 150);
            $table->text('deskripsi')->nullable();

            $table->timestamps();

            $table->unique(['divisi_id', 'nama']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tim_kerjas');
    }
};
