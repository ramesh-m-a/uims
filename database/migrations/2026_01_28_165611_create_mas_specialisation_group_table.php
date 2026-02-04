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
       /* Schema::create('mas_specialisation_group', function (Blueprint $table) {
            $table->smallIncrements('id');
            $table->string('mas_specialisation_group_code')->unique();
            $table->string('mas_specialisation_group_name');
            $table->unsignedTinyInteger('status_id')->default(1);
            $table->timestamps();
            $table->softDeletes();
        });*/

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        /*Schema::dropIfExists('mas_specialisation_group');*/
    }
};
