<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('role_permission', function (Blueprint $table) {

            $table->unsignedBigInteger('role_id');
            $table->unsignedBigInteger('permission_id');

            // Composite PK (NO duplicates)
            $table->primary(['role_id', 'permission_id']);

            // Indexes
            $table->index('role_id');
            $table->index('permission_id');

            // FKs
            $table->foreign('role_id')
                ->references('id')->on('roles')
                ->cascadeOnDelete();

            $table->foreign('permission_id')
                ->references('id')->on('permissions')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('role_permission');
    }
};
