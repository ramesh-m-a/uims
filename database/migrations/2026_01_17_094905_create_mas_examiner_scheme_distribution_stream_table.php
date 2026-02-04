<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mas_examiner_scheme_distribution_stream', function (Blueprint $table) {
            $table->id();

            $table->unsignedSmallInteger('mas_examiner_scheme_distribution_id');
            $table->unsignedBigInteger('mas_stream_id');

            $table->unsignedTinyInteger('mas_examiner_scheme_distribution_stream_status_id')->default(1);

            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->unique([
                'mas_examiner_scheme_distribution_id',
                'mas_stream_id',
                'deleted_at'
            ], 'uq_esd_stream_active');

            $table->foreign('mas_examiner_scheme_distribution_id', 'fk_esds_distribution')
                ->references('id')
                ->on('mas_examiner_scheme_distribution')
                ->cascadeOnUpdate();

            $table->foreign('mas_stream_id', 'fk_esds_stream')
                ->references('id')
                ->on('mas_stream')
                ->cascadeOnUpdate();
        });

    }

    public function down(): void
    {
        Schema::dropIfExists('mas_examiner_scheme_distribution_stream');
    }
};
