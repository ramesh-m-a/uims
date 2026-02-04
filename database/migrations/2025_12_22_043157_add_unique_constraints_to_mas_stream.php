<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('mas_stream', function (Blueprint $table) {

            /* =========================
             | UNIQUE CONSTRAINTS
             | (Soft Delete safe)
             ========================= */

            // Unique Stream Name (only active)
            $table->unique(
                ['mas_stream_name', 'deleted_at'],
                'uq_mas_stream_name_active'
            );

            // Unique Stream Code (only active)
            $table->unique(
                ['mas_stream_short_code', 'deleted_at'],
                'uq_mas_stream_short_code_active'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mas_stream', function (Blueprint $table) {

            $table->dropUnique('uq_mas_stream_name_active');
            $table->dropUnique('uq_mas_stream_short_code_active');
        });
    }
};
