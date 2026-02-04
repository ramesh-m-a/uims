<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mas_subject', function (Blueprint $table) {
            $table->smallIncrements('id');

            $table->unsignedSmallInteger('mas_subject_stream_id');
            $table->unsignedSmallInteger('mas_subject_degree_id');

            $table->string('mas_subject_code', 50)->nullable();
            $table->string('mas_subject_name', 200);

            $table->unsignedSmallInteger('mas_subject_status_id')->default(1);

            $table->unsignedSmallInteger('mas_subject_department_id')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->string('created_by', 100)->nullable();
            $table->string('updated_by', 100)->nullable();

            // Composite unique exactly like DDL
            $table->unique(
                [
                    'mas_subject_stream_id',
                    'mas_subject_degree_id',
                    'mas_subject_name',
                    'mas_subject_department_id'
                ],
                'mas_subject_stream_id'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mas_subject');
    }
};
