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
        /*Schema::create('mas_degree_group_map', function (Blueprint $table) {
            $table->bigIncrements('id');

            // Adjust these types if mas_degree.partials is not BIGINT
            $table->unsignedBigInteger('degree_id');
            $table->unsignedBigInteger('degree_group_id');

            $table->softDeletes();

            $table->foreign('degree_id', 'fk_dgm_degree')
                ->references('id')->on('mas_degree')
                ->cascadeOnDelete();

            $table->foreign('degree_group_id', 'fk_dgm_group')
                ->references('id')->on('mas_degree_group')
                ->cascadeOnDelete();

            $table->unique(['degree_id', 'degree_group_id', 'deleted_at'], 'uniq_dgm_active');
        });*/

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        /*Schema::dropIfExists('mas_degree_group_map');*/
    }
};
