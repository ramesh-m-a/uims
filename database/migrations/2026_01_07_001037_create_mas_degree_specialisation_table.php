<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up(): void
    {
        Schema::create('mas_degree_specialisation', function (Blueprint $table) {
            $table->id();

            // 🔑 FK → mas_degree
            $table->unsignedBigInteger('degree_id');

            // 🔑 FK → mas_specialisation
            $table->unsignedBigInteger('specialisation_id');

            $table->unsignedTinyInteger('mas_degree_specialisation_status_id')
                ->default(1);

            $table->timestamps();
            $table->softDeletes();

            // ✅ UNIQUE PAIR
            $table->unique(
                ['degree_id', 'specialisation_id'],
                'mas_degree_specialisation_unique'
            );

            // ✅ EXPLICIT FOREIGN KEYS (NO LARAVEL MAGIC)
            $table->foreign('degree_id')
                ->references('id')
                ->on('mas_degree')
                ->onDelete('cascade');

            $table->foreign('specialisation_id')
                ->references('id')
                ->on('mas_specialisation')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mas_degree_specialisation');
    }
};
