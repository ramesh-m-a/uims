<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('examiner_details', function (Blueprint $table) {
            $table->increments('id'); // int(10) unsigned auto_increment

            $table->unsignedBigInteger('examiner_details_basic_details_id');
            $table->unsignedInteger('examiner_details_qualification_details_id')->nullable();

            $table->unsignedSmallInteger('examiner_details_type')->default(2);

            $table->unsignedInteger('examiner_details_year_id')->nullable();
            $table->unsignedInteger('examiner_details_month_id')->nullable();

            $table->date('examiner_details_recognition_date')->nullable();

            $table->unsignedTinyInteger('examiner_details_rank')->nullable();

            $table->unsignedSmallInteger('examiner_details_status_id')->default(1);

            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->nullable()->useCurrent()->useCurrentOnUpdate();

            $table->string('created_by', 100)->nullable();
            $table->string('updated_by', 100)->nullable();

            /* -----------------------------
             | Indexes (DB Optimization)
             |------------------------------
             | These are important because:
             | Your picker queries filter heavily on:
             | - status_id
             | - type
             | - basic_details_id
             | - rank
             */
            $table->index('examiner_details_basic_details_id', 'idx_examiner_basic');
            $table->index('examiner_details_status_id', 'idx_examiner_status');
            $table->index('examiner_details_type', 'idx_examiner_type');
            $table->index('examiner_details_rank', 'idx_examiner_rank');
            $table->index(['examiner_details_type', 'examiner_details_status_id'], 'idx_type_status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('examiner_details');
    }
};
