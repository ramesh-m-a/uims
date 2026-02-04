<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->id();

            /* =========================
             | CORE
             ========================= */
            $table->string('name', 50)->unique(); // admin, principal, my-details
            $table->string('description')->nullable();

            /* =========================
             | STATUS (🔥 NAMING STANDARD)
             ========================= */
            $table->unsignedBigInteger('roles_status_id')
                ->default(1)
                ->comment('FK to mas_status.partials');

            /* =========================
             | AUDIT
             ========================= */
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();

            $table->timestamps();

            /* =========================
             | INDEXES
             ========================= */
            $table->index('roles_status_id', 'roles_status_id_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('roles');
    }
};
