<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('audit_logs', function (Blueprint $table) {

            /* =========================
             | PRIMARY KEY
             ========================= */
            $table->id();

            /* =========================
             | WHO
             ========================= */
            $table->unsignedBigInteger('user_id')->nullable()
                ->comment('User who performed the action');

            /* =========================
             | WHAT / WHERE
             ========================= */
            $table->string('table_name', 100);
            $table->unsignedBigInteger('record_id')->nullable();
            $table->string('action', 20)
                ->comment('create | update | delete | restore');

            /* =========================
             | DATA SNAPSHOTS
             ========================= */
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();

            /* =========================
             | TIMESTAMPS
             ========================= */
            $table->timestamps();

            /* =========================
             | INDEXES (CRITICAL)
             ========================= */
            $table->index('user_id');
            $table->index('table_name');
            $table->index('record_id');
            $table->index('action');
            $table->index('created_at');

            /* =========================
             | OPTIONAL FK (SAFE)
             ========================= */
            // $table->foreign('user_id')
            //     ->references('id')
            //     ->on('user')
            //     ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
