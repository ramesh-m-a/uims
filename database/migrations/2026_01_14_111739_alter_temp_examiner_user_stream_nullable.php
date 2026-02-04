<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('temp_examiner_assigned_details', function (Blueprint $table) {
            $table->unsignedInteger('user_stream_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('temp_examiner_assigned_details', function (Blueprint $table) {
            $table->unsignedInteger('user_stream_id')->nullable(false)->change();
        });
    }
};
