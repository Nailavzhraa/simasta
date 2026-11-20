<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'is_active')) {
                $table->boolean('is_active')->default(true);
            }
            if (!Schema::hasColumn('users', 'presensi_migrated_at')) {
                $table->timestamp('presensi_migrated_at')->nullable();
            }
        });
    }

    public function down(): void
    {
        // These fields also protect linked attendance identities. Keep their data.
    }
};
