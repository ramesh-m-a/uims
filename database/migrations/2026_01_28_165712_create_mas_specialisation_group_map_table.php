<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
       /* Schema::create('mas_specialisation_group_map', function (Blueprint $table) {
            $table->bigIncrements('id');

            // Adjust types if mas_specialisation.partials is not BIGINT
            $table->unsignedBigInteger('specialisation_id');
            $table->unsignedBigInteger('specialisation_group_id');

            $table->softDeletes();

            $table->foreign('specialisation_id', 'fk_sgm_spec')
                ->references('id')->on('mas_specialisation')
                ->cascadeOnDelete();

            $table->foreign('specialisation_group_id', 'fk_sgm_group')
                ->references('id')->on('mas_specialisation_group')
                ->cascadeOnDelete();

            $table->unique(['specialisation_id', 'specialisation_group_id', 'deleted_at'], 'uniq_sgm_active');
        });*/


    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        /*Schema::dropIfExists('mas_specialisation_group_map');*/
    }
};
