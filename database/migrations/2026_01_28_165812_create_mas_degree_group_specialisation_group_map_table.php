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
       /* Schema::create('mas_degree_group_specialisation_group_map', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedSmallInteger('degree_group_id');
            $table->unsignedSmallInteger('specialisation_group_id');

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('degree_group_id', 'fk_dgsgm_dg')
                ->references('id')->on('mas_degree_group')
                ->cascadeOnDelete();

            $table->foreign('specialisation_group_id', 'fk_dgsgm_sg')
                ->references('id')->on('mas_specialisation_group')
                ->cascadeOnDelete();

            $table->unique(
                ['degree_group_id', 'specialisation_group_id', 'deleted_at'],
                'uniq_dgsgm_active'
            );
        });*/



    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        /*Schema::dropIfExists('mas_degree_group_specialisation_group_map');*/
    }
};
