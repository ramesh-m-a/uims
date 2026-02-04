<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('work_details', function (Blueprint $table) {
            $table->bigincrements('id');

            $table->unsignedSmallInteger('work_details_basic_details_id')->nullable();
            $table->unsignedSmallInteger('work_details_work_designation_id')->nullable();
            $table->string('work_details_last_institution_name', 100)->nullable();

            $table->date('work_details_date_of_appointment')->nullable();
            $table->date('work_details_from_date')->nullable();
            $table->date('work_details_to_date')->nullable();
            $table->date('work_details_date_of_joining')->nullable();

            $table->unsignedSmallInteger('work_details_status')->nullable();
            $table->string('work_details_work_department_id', 100)->nullable();
            $table->boolean('work_details_till_date')->default(false);

            $table->timestamps();
            $table->softDeletes();

            $table->string('created_by', 100)->nullable();
            $table->string('updated_by', 100)->nullable();

            $table->index(
                'work_details_basic_details_id',
                'idx_work_basic_details_id'
            );
            $table->index(
                ['id','work_details_work_designation_id'],
                'idx_work_details'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('work_details');
    }
};
