<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up(): void
    {
        Schema::create('subject_details', function (Blueprint $table) {
            $table->bigIncrements('id'); // matches BIGINT PK pattern

            // basic_details.partials = BIGINT UNSIGNED → OK
            $table->unsignedBigInteger('basic_details_id');

            // mas_subject.partials = SMALLINT UNSIGNED → must match
            $table->unsignedSmallInteger('mas_subject_id');

            // mas_year.partials = SMALLINT UNSIGNED → must match
            $table->unsignedSmallInteger('mas_year_id');

            // Foreign keys (explicit, safe)
            $table->foreign('basic_details_id')
                ->references('id')->on('basic_details')
                ->cascadeOnDelete();

            $table->foreign('mas_subject_id')
                ->references('id')->on('mas_subject')
                ->cascadeOnDelete();

            $table->foreign('mas_year_id')
                ->references('id')->on('mas_year')
                ->cascadeOnDelete();

            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->unique(
                ['basic_details_id', 'mas_subject_id', 'mas_year_id'],
                'uniq_subject_per_year_per_user'
            );
        });

    }

    public function down(): void
    {
        Schema::dropIfExists('subject_details');
    }
};
