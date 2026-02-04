<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {

            // Composite index
            $table->index(
                ['user_stream_id', 'user_college_id'],
                'idx_users_stream_college'
            );

            // Single column indexes
            $table->index('user_designation_id', 'idx_users_designation');
            $table->index('name', 'idx_users_name');
            $table->index('mobile', 'idx_users_mobile');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {

            $table->dropIndex('idx_users_stream_college');
            $table->dropIndex('idx_users_designation');
            $table->dropIndex('idx_users_name');
            $table->dropIndex('idx_users_mobile');
        });
    }
};
