<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mas_designation_constituency', function (Blueprint $table) {
            $table->id();

            // mas_designation.partials → BIGINT UNSIGNED
            $table->unsignedBigInteger('designation_id');

            // mas_constituency.partials → SMALLINT UNSIGNED (NO FK!)
            $table->unsignedSmallInteger('constituency_id');

            $table->timestamps();
            $table->softDeletes();

            // one designation → one constituency
            $table->unique('designation_id');

            // FK ONLY on designation (controlled table)
            $table->foreign('designation_id')
                ->references('id')
                ->on('mas_designation')
                ->onDelete('cascade');

            // lookup only
            $table->index('constituency_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mas_designation_constituency');
    }
};
