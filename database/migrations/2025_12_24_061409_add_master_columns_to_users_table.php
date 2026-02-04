<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {

            $table->foreignId('user_stream_id')
                ->nullable()
                ->constrained('mas_stream')
                ->nullOnDelete();

            $table->foreignId('user_college_id')
                ->nullable()
                ->constrained('mas_college')
                ->nullOnDelete();

            $table->foreignId('user_designation_id')
                ->nullable()
                ->constrained('mas_designation')
                ->nullOnDelete();

            $table->foreignId('user_role_id')
                ->nullable()
                ->constrained('roles')
                ->nullOnDelete();

            $table->foreignId('user_status_id')
                ->nullable()
                ->constrained('mas_status')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['user_stream_id']);
            $table->dropForeign(['user_college_id']);
            $table->dropForeign(['user_designation_id']);
            $table->dropForeign(['user_role_id']);
            $table->dropForeign(['user_status_id']);

            $table->dropColumn([
                'user_stream_id',
                'user_college_id',
                'user_designation_id',
                'user_role_id',
                'user_status_id',
            ]);
        });
    }
};
