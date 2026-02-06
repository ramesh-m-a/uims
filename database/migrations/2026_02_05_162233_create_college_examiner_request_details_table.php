<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('college_examiner_request_details', function (Blueprint $table) {

            $table->id();

            $table->unsignedSmallInteger('college_examiner_request_details_year_id');
            $table->unsignedSmallInteger('college_examiner_request_details_month_id');

            $table->unsignedSmallInteger('college_examiner_request_details_batch_id');
            $table->unsignedSmallInteger('college_examiner_request_details_batch_range_id');

            $table->unsignedSmallInteger('college_examiner_request_details_revised_scheme_id');

            $table->unsignedSmallInteger('college_examiner_request_details_college_id');
            $table->unsignedSmallInteger('college_examiner_request_details_stream_id');

            $table->unsignedBigInteger('college_examiner_request_details_examiner_id');
            $table->unsignedBigInteger('college_examiner_request_details_new_examiner_id')->nullable();

            $table->string('created_by', 100)->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();

            $table->string('college_examiner_request_details_comments', 1000)->nullable();

            $table->unsignedSmallInteger('college_examiner_request_details_status_id')->nullable();

            $table->timestamps();

            // ⭐ INDEXES (Important for performance)
            $table->index([
                'college_examiner_request_details_batch_range_id',
                'college_examiner_request_details_examiner_id',
                'college_examiner_request_details_college_id'
            ], 'cerd_lookup_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('college_examiner_request_details');
    }
};
