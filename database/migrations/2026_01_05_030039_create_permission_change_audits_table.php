<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('permission_change_audits', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('actor_id');
            $table->unsignedBigInteger('target_user_id');
            $table->unsignedBigInteger('source_user_id')->nullable();

            $table->json('before');
            $table->json('after');

            $table->timestamps();

            $table->index('actor_id');
            $table->index('target_user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('permission_change_audits');
    }
};
