<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('qualification_details', function (Blueprint $table) {
            $table->bigincrements('id');

            $table->unsignedSmallInteger('qualification_details_basic_details_id')->nullable();
            $table->unsignedSmallInteger('qualification_details_stream_id')->nullable();
            $table->unsignedSmallInteger('qualification_details_degree_id')->nullable();
            $table->unsignedSmallInteger('qualification_details_specialisation_id')->nullable();

            $table->string('qualification_details_university_name', 100)->nullable();
            $table->year('qualification_details_year_of_award')->nullable();
            $table->year('qualification_details_year_of_exam')->nullable();
            $table->string('qualification_details_state_registration_number', 100)->nullable();
            $table->date('qualification_details_registration_date')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->string('created_by', 100)->nullable();
            $table->string('updated_by', 100)->nullable();

            $table->index(
                'qualification_details_basic_details_id',
                'idx_qualification_basic_details_id'
            );
            $table->index(
                ['id','qualification_details_degree_id','qualification_details_specialisation_id'],
                'idx_qualification_details'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('qualification_details');
    }
};
