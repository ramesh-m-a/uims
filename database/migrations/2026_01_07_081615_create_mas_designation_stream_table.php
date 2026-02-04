<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mas_designation_stream', function (Blueprint $table) {
            $table->id();

            // MUST MATCH mas_designation.partials (BIGINT UNSIGNED)
            $table->unsignedBigInteger('designation_id');

            // MUST MATCH mas_stream.partials (BIGINT UNSIGNED)
            $table->unsignedBigInteger('stream_id');

            $table->tinyInteger('status_id')->default(1);

            $table->timestamps();
            $table->softDeletes();

            $table->unique(['designation_id', 'stream_id']);

            $table->foreign('designation_id')
                ->references('id')
                ->on('mas_designation')
                ->onDelete('cascade');

            $table->foreign('stream_id')
                ->references('id')
                ->on('mas_stream')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mas_designation_stream');
    }
};
