<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mas_college_prod', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('mas_college_stream_id');
            $table->string('mas_college_code', 20)->nullable();
            $table->string('mas_college_name', 500);

            $table->boolean('mas_college_exam_centre')
                ->default(1)
                ->comment('1 = Exam Centre');

            $table->char('mas_college_type', 1)
                ->default('G')
                ->comment('G=Government, P=Private');

            $table->unsignedSmallInteger('mas_college_is_internal')
                ->default(1)
                ->comment('1 = Internal');

            $table->unsignedSmallInteger('mas_college_status_id')
                ->default(1)
                ->comment('1 = Active, 2 = In Active');

            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mas_college_prod');
    }
};
