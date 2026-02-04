<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('mas_degree_level', function (Blueprint $table) {
            $table->id();

            $table->string('mas_degree_level_code', 10)->unique(); // UG, PG, SS
            $table->string('mas_degree_level_name', 100);
            $table->unsignedTinyInteger('mas_degree_level_sort_order');

            $table->unsignedTinyInteger('mas_degree_level_status_id')->default(1); // Active / Inactive

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mas_degree_level');
    }
};
