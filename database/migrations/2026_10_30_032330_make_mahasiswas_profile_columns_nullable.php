<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // PostgreSQL requires an explicit cast for the original string column.
        // Invalid legacy values fail the migration instead of being discarded.
        if (DB::getDriverName() === 'pgsql') {
            DB::statement("ALTER TABLE mahasiswas ALTER COLUMN semester TYPE SMALLINT USING NULLIF(BTRIM(semester::text), '')::smallint");
        }

        Schema::table('mahasiswas', function (Blueprint $table) {
        $table->string('nim')->nullable()->change();
        $table->string('perguruan_tinggi')->nullable()->change();
        $table->string('program_studi')->nullable()->change();
        $table->string('jenis_kelamin')->nullable()->change();
        $table->string('no_telepon')->nullable()->change();
        $table->text('alamat_kampus')->nullable()->change();
        $table->string('kecamatan_kampus')->nullable()->change();
        $table->string('kelurahan_kampus')->nullable()->change();
        $table->unsignedTinyInteger('semester')->nullable()->change();
        $table->string('dosen_pembimbing')->nullable()->change();
        $table->string('foto')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mahasiswas', function (Blueprint $table) {
            //
        });
    }
};
