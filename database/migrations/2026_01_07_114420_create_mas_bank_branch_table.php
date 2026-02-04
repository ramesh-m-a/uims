<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('mas_bank_branch', function (Blueprint $table) {
            $table->id();

            // FK to mas_bank
            $table->unsignedBigInteger('mas_bank_branch_bank_id');

            $table->string('mas_bank_branch_branch_name', 150);

            $table->string('mas_bank_branch_address_1', 500)->nullable();
            $table->string('mas_bank_branch_address_2', 255)->nullable();

            $table->string('mas_bank_branch_city', 100)->nullable();
            $table->string('mas_bank_branch_state', 100)->nullable();

            $table->unsignedTinyInteger('mas_bank_branch_status_id')
                ->default(1)
                ->comment('1=Active, 2=Inactive');

            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('mas_bank_branch_bank_id', 'bank_branch_bank_idx');
            $table->index('mas_bank_branch_branch_name', 'bank_branch_name_idx');

            // Unique composite
            $table->unique(
                ['mas_bank_branch_bank_id', 'mas_bank_branch_branch_name'],
                'bank_branch_unique'
            );

            // FK
            $table->foreign('mas_bank_branch_bank_id')
                ->references('id')
                ->on('mas_bank')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mas_bank_branch');
    }
};
