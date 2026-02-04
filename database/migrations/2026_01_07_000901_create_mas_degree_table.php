<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('mas_degree', function (Blueprint $table) {
            $table->id();

            $table->unsignedTinyInteger('mas_degree_stream_id');

            $table->string('mas_degree_code', 20)->unique(); // MBBS, BSC, MD
            $table->string('mas_degree_name', 150);

            $table->foreignId('mas_degree_level_id')
                ->constrained('mas_degree_level');

            /**
             * 0 = NONE        (MBBS, BAMS, BHMS)
             * 1 = OPTIONAL    (BSc)
             * 2 = REQUIRED    (MD, DM, MCh)
             */
            $table->unsignedTinyInteger('mas_degree_specialisation_mode')->default(0);

            $table->unsignedTinyInteger('mas_degree_status_id')->default(1);

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mas_degree');
    }
};
