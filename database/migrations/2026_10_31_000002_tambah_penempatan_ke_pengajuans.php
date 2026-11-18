<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('divisis')) {
            Schema::create('divisis', function (Blueprint $t) {
                $t->id();
                $t->string('nama');
                $t->text('jobdesk')->nullable();
                $t->timestamps();
            });
        }

        if (! Schema::hasTable('pembimbings')) {
            Schema::create('pembimbings', function (Blueprint $t) {
                $t->id();
                $t->string('nama');
                $t->string('nip')->nullable();
                $t->foreignId('divisi_id')->nullable()->constrained('divisis')->nullOnDelete();
                $t->timestamps();
            });
        }

        // Tabel pengajuans milikmu hanya DITAMBAH kolom, datanya tidak berubah.
        Schema::table('pengajuans', function (Blueprint $t) {
            if (! Schema::hasColumn('pengajuans', 'divisi_id')) {
                $t->foreignId('divisi_id')->nullable()->constrained('divisis')->nullOnDelete();
            }
            if (! Schema::hasColumn('pengajuans', 'pembimbing_id')) {
                $t->foreignId('pembimbing_id')->nullable()->constrained('pembimbings')->nullOnDelete();
            }
            if (! Schema::hasColumn('pengajuans', 'jobdesk')) {
                $t->text('jobdesk')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('pengajuans', function (Blueprint $t) {
            foreach (['divisi_id', 'pembimbing_id'] as $kolom) {
                if (Schema::hasColumn('pengajuans', $kolom)) {
                    $t->dropConstrainedForeignId($kolom);
                }
            }
            if (Schema::hasColumn('pengajuans', 'jobdesk')) {
                $t->dropColumn('jobdesk');
            }
        });
    }
};