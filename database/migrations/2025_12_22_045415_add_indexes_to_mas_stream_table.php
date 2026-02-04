<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('mas_stream', function (Blueprint $table) {

            // Search & dropdowns
            $table->index('mas_stream_name', 'idx_stream_name');

            // Status filtering
            $table->index('mas_stream_status_id', 'idx_stream_status');

            // Academic production filter (Status + name)
            $table->index(
                ['mas_stream_status_id', 'mas_stream_name'],
                'idx_stream_status_name'
            );
        });
    }

    public function down(): void
    {
        Schema::table('mas_stream', function (Blueprint $table) {
            $table->dropIndex('idx_stream_name');
            $table->dropIndex('idx_stream_status');
            $table->dropIndex('idx_stream_status_name');
        });
    }
};
