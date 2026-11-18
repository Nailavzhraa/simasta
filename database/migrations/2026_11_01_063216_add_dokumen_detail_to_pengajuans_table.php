<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tambahkan data dokumen dan tujuan magang.
     */
    public function up(): void
    {
        if (!Schema::hasColumn('pengajuans', 'file_proposal')) {
            Schema::table('pengajuans', function (Blueprint $table) {
                $table->string('file_proposal')->nullable();
            });
        }

        if (!Schema::hasColumn('pengajuans', 'tujuan_magang')) {
            Schema::table('pengajuans', function (Blueprint $table) {
                $table->text('tujuan_magang')->nullable();
            });
        }

        if (!Schema::hasColumn('pengajuans', 'keterangan_tambahan')) {
            Schema::table('pengajuans', function (Blueprint $table) {
                $table->text('keterangan_tambahan')->nullable();
            });
        }
    }

    /**
     * Kembalikan perubahan migration ini.
     */
    public function down(): void
    {
        foreach ([
            'file_proposal',
            'tujuan_magang',
            'keterangan_tambahan',
        ] as $column) {
            if (Schema::hasColumn('pengajuans', $column)) {
                Schema::table('pengajuans', function (Blueprint $table) use ($column) {
                    $table->dropColumn($column);
                });
            }
        }
    }
};
