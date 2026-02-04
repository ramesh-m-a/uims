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
        Schema::create('profile_approvals', function ($table) {
            $table->id();

            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('basic_details_id')->nullable();

            $table->string('level_id'); // college | principal | rguhs
            $table->string('status_id'); // pending | approved | rejected

            $table->text('remarks')->nullable();

            $table->unsignedBigInteger('acted_by')->nullable();
            $table->timestamp('acted_at')->nullable();

            $table->timestamps();

            $table->index(['user_id', 'level_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
