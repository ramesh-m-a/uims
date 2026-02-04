<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('exam_eligible_student_details', function (Blueprint $table) {
            $table->id(); // smallint unsigned auto_increment equivalent not supported directly → uses bigint, acceptable

            $table->string('exam_eligible_student_details_faculty_name', 100);
            $table->string('exam_eligible_student_details_course_level', 100);
            $table->string('exam_eligible_student_details_subject_name', 100);
            $table->string('exam_eligible_student_details_scheme', 100);
            $table->string('exam_eligible_student_details_centre_code', 100);
            $table->string('exam_eligible_student_details_exam_year', 100);
            $table->string('exam_eligible_student_details_exam_month', 100);
            $table->string('exam_eligible_student_details_stud_count', 100);
            $table->string('exam_eligible_student_details_attached_college', 100);
            $table->date('exam_eligible_student_details_exam_start_date');

            $table->timestamps();

            $table->string('created_by', 100)->nullable();
            $table->string('updated_by', 100)->nullable();

            $table->unsignedBigInteger('exam_eligible_student_details_upload_session_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exam_eligible_student_details');
    }
};
