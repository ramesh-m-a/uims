<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('examiner_assigned_details', function (Blueprint $table) {
            $table->smallIncrements('id');

            $table->unsignedInteger('examiner_assigned_details_examiner_id');

            $table->unsignedSmallInteger('examiner_assigned_details_year_id');
            $table->unsignedSmallInteger('examiner_assigned_details_month_id');

            $table->unsignedInteger('examiner_assigned_details_batch_id');
            $table->unsignedInteger('examiner_assigned_details_batch_range_id');

            $table->unsignedSmallInteger('examiner_assigned_details_status_id');

            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->nullable()->useCurrent()->useCurrentOnUpdate();

            $table->string('created_by', 100)->nullable();
            $table->string('updated_by', 100)->nullable();

            $table->unsignedInteger('examiner_assigned_details_basic_details_id')->nullable();

            $table->string('examiner_assigned_details_type', 50)->default('UNKNOWN');

            $table->unsignedSmallInteger('examiner_assigned_details_centre_id');
            $table->unsignedSmallInteger('examiner_assigned_details_attached_id');

            $table->unsignedSmallInteger('examiner_assigned_details_revised_scheme_id');
            $table->unsignedSmallInteger('examiner_assigned_details_subject_id');

            $table->date('examiner_assigned_details_from_date')->nullable();
            $table->date('examiner_assigned_details_to_date')->nullable();

            $table->unsignedSmallInteger('examiner_assigned_details_degree_id');
            $table->unsignedSmallInteger('examiner_assigned_details_stream_id');

            $table->boolean('examiner_assigned_details_is_additional')->default(false);

            /**
             * -------------------------------------------------
             * INDEXES — this is where performance comes from
             * -------------------------------------------------
             */

            // Primary lookups used everywhere
 /*           $table->index([
                'examiner_assigned_details_year_id',
                'examiner_assigned_details_month_id',
                'examiner_assigned_details_revised_scheme_id',
            ], 'ead_scope_index');

            // Allocation fetch by batch/range
            $table->index([
                'examiner_assigned_details_batch_id',
                'examiner_assigned_details_batch_range_id',
            ], 'ead_batch_range_index');

            // Examiner reuse / collision detection
            $table->index([
                'examiner_assigned_details_examiner_id',
                'examiner_assigned_details_from_date',
            ], 'ead_examiner_date_index');

            // Centre + Subject filtering
            $table->index([
                'examiner_assigned_details_centre_id',
                'examiner_assigned_details_subject_id',
            ], 'ead_centre_subject_index');

            // Date range queries (reporting, conflicts)
            $table->index('examiner_assigned_details_from_date');
            $table->index('examiner_assigned_details_to_date');*/
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('examiner_assigned_details');
    }
};
