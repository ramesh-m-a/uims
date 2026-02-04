<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('mas_batch', function (Blueprint $table) {
            $table->smallIncrements('id');

            $table->unsignedSmallInteger('mas_batch_stream_id');
            $table->unsignedSmallInteger('mas_batch_year_id');
            $table->unsignedSmallInteger('mas_batch_subject_id');
            $table->unsignedSmallInteger('mas_batch_centre_id');

            $table->unsignedInteger('mas_batch_total_students')->default(0);
            $table->unsignedSmallInteger('mas_batch_status_id')->default(50);

            $table->timestamps();
            $table->softDeletes();

            $table->string('created_by', 100)->nullable();
            $table->string('updated_by', 100)->nullable();

            $table->unsignedSmallInteger('mas_batch_month_id');
            $table->unsignedInteger('mas_batch_total_batches')->default(0);

            $table->string('mas_batch_attached_centre_id', 100)->default('0');

            $table->date('mas_batch_start_date');

            $table->unsignedSmallInteger('mas_batch_degree_id')->default(0);
            $table->unsignedSmallInteger('mas_batch_revised_scheme_id');

            $table->unsignedTinyInteger('mas_batch_approval_status')->default(32);
            $table->unsignedTinyInteger('mas_batch_is_updated')->default(0)->comment('No');

            $table->unsignedSmallInteger('mas_batch_old_centre_id')->default(0);
            $table->string('mas_batch_old_attached_centre_id', 100)->default('0');

            $table->unique([
                'mas_batch_stream_id',
                'mas_batch_year_id',
                'mas_batch_subject_id',
                'mas_batch_centre_id',
                'mas_batch_degree_id',
                'mas_batch_revised_scheme_id',
                'mas_batch_month_id',
                'mas_batch_start_date',
            ], 'mas_batch_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mas_batch');
    }
};
