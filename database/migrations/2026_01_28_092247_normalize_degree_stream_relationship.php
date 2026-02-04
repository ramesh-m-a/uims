<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('mas_degree_stream', function (Blueprint $table) {
            // Ensure clean FK structure
            $table->unsignedBigInteger('mas_degree_id')->change();
            $table->unsignedBigInteger('mas_stream_id')->change();

            // Add composite index (if missing)
            $table->index(['mas_degree_id', 'mas_stream_id'], 'idx_degree_stream_lookup');
        });

        Schema::table('mas_degree_specialisation', function (Blueprint $table) {
            $table->unsignedBigInteger('degree_id')->change();
            $table->unsignedBigInteger('specialisation_id')->change();

            $table->index(['degree_id', 'specialisation_id'], 'idx_degree_specialisation_lookup');
        });
    }

    public function down(): void
    {
        Schema::table('mas_degree_stream', function (Blueprint $table) {
            $table->dropIndex('idx_degree_stream_lookup');
        });

        Schema::table('mas_degree_specialisation', function (Blueprint $table) {
            $table->dropIndex('idx_degree_specialisation_lookup');
        });
    }
};
