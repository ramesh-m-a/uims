<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('mas_batch_split', function (Blueprint $table) {
            $table->smallIncrements('id');

            $table->unsignedInteger('mas_batch_split_batch_id')->nullable();
            $table->unsignedSmallInteger('mas_batch_split_status_id')->nullable();

            $table->unsignedSmallInteger('mas_batch_split_students')->nullable();

            $table->string('created_by', 100)->nullable();
            $table->string('updated_by', 100)->nullable();

            $table->timestamps();
            $table->softDeletes();
            // --------------------
            // Indexes (Optimization)
            // --------------------
            $table->index('mas_batch_split_batch_id', 'idx_batch_split_batch');
            $table->index('mas_batch_split_status_id', 'idx_batch_split_status');
            $table->index('mas_batch_split_students', 'idx_batch_split_students');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mas_batch_split');
    }
};
