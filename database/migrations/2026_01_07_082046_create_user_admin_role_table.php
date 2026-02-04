<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_admin_role', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('admin_role_id');

            $table->date('from_date')->nullable();
            $table->date('to_date')->nullable();

            $table->boolean('is_primary')->default(false);

            $table->timestamps();
            $table->softDeletes();

            /* =========================
             | CONSTRAINTS
             ========================= */
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');

            $table->foreign('admin_role_id')
                ->references('id')
                ->on('mas_admin_role')
                ->onDelete('cascade');

            /* =========================
             | INDEXES
             ========================= */
            $table->index(['user_id', 'admin_role_id']);
            $table->index('is_primary');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_admin_role');
    }
};
