<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('temp_examiner_assigned_details', function (Blueprint $table) {

            $table->id();

            $table->unsignedBigInteger('user_id');
            $table->unsignedInteger('year_id');
            $table->unsignedInteger('month_id');
            $table->unsignedInteger('scheme_id');
            $table->unsignedInteger('degree_id');
            $table->unsignedInteger('batch_id');
            $table->unsignedInteger('batch_range_id');

            $table->integer('examiner_id');
            $table->string('examiner_name')->nullable();
            $table->string('examiner_type', 50)->nullable();
            $table->unsignedTinyInteger('examiner_type_id')->nullable();
            $table->string('mobile', 20)->nullable();

            $table->unsignedInteger('centre_id');
            $table->string('centre_name')->nullable();

            $table->unsignedInteger('attached_id')->default(0);
            $table->unsignedInteger('user_college_id')->default(0);

            $table->unsignedInteger('subject_id');
            $table->string('subject_name')->nullable();

            $table->unsignedInteger('user_stream_id')->nullable();

            $table->unsignedSmallInteger('status');
            $table->string('status_label', 50);

            $table->string('batch_name')->nullable();

            $table->date('from_date')->nullable();
            $table->date('original_from_date')->nullable();

            $table->boolean('is_rescheduled')->default(false);
            $table->tinyInteger('is_additional')->default(0);

            $table->timestamp('rescheduled_at')->nullable();
            $table->unsignedBigInteger('rescheduled_by')->nullable();

            $table->date('to_date')->nullable();

            $table->string('label_colour', 20)->nullable();

            $table->timestamps();

            /*
            |--------------------------------------------------------------------------
            | INDEXES
            |--------------------------------------------------------------------------
            */

            $table->index(
                ['user_id', 'year_id', 'month_id', 'scheme_id', 'degree_id'],
                'temp_scope_idx'
            );

            $table->index('batch_range_id');
            $table->index('centre_id');
            $table->index('examiner_id');
            $table->index('is_additional');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('temp_examiner_assigned_details');
    }
};
