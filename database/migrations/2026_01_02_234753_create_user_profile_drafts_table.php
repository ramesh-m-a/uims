<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_profile_drafts', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('basic_details_id')->nullable();

            $table->string('current_tab', 50)->default('basic');

            $table->json('completed_tabs')->nullable();

            $table->enum('status_id', [
                'draft',
                'submitted',
                'approved',
                'rejected'
            ])->default('draft');

            $table->json('data');

            $table->unsignedBigInteger('locked_by')->nullable();
            $table->timestamp('locked_at')->nullable();

            $table->timestamps();

            // Constraints
            $table->unique('user_id');

            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');

            $table->index('basic_details_id');
            $table->index('status_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_profile_drafts');
    }
};
