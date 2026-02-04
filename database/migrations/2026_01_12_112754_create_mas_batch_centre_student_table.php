<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mas_batch_centre_student', function (Blueprint $table) {
            $table->id();

            $table->unsignedSmallInteger('mas_batch_id');
            $table->unsignedSmallInteger('mas_centre_id');

            $table->unsignedInteger('student_count');

            $table->string('created_by', 100)->nullable();
            $table->string('updated_by', 100)->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Constraints
            $table->unique(['mas_batch_id', 'mas_centre_id'], 'uniq_batch_centre');
            $table->index('mas_batch_id', 'idx_batch');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mas_batch_centre_student');
    }
};
