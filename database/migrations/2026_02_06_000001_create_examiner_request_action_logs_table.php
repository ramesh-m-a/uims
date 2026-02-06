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
        Schema::create('examiner_request_action_logs', function (Blueprint $table) {

            $table->id();

            // Link to college_examiner_request_details.id
            $table->unsignedBigInteger('request_id');

            // CREATED / APPROVED / REJECTED / UPDATED
            $table->string('action', 50);

            // User who performed action
            $table->unsignedBigInteger('action_by');

            // Timestamp of action
            $table->timestamp('action_at')->useCurrent();

            // Optional remarks
            $table->string('remarks', 1000)->nullable();

            // IP address (IPv4 / IPv6 safe)
            $table->string('ip_address', 45)->nullable();

            // Indexes (Important for reporting later)
            $table->index('request_id', 'idx_eral_request_id');
            $table->index('action', 'idx_eral_action');
            $table->index('action_at', 'idx_eral_action_at');

            // Optional FK (Enable only if your prod uses FK safely)
            /*
            $table->foreign('request_id')
                ->references('id')
                ->on('college_examiner_request_details')
                ->cascadeOnDelete();
            */
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('examiner_request_action_logs');
    }
};
