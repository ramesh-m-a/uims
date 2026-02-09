<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('request_status_master', function (Blueprint $table) {
            $table->id();

            $table->string('code')->unique();
            $table->string('label');

            $table->boolean('is_pending')->default(false);
            $table->boolean('is_final')->default(false);

            $table->string('colour')->nullable();
            $table->unsignedSmallInteger('status_id')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('request_status_master');
    }
};
