<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('mas_specialisation', function (Blueprint $table) {
            $table->id();

            $table->string('mas_specialisation_name', 150);
            $table->string('mas_specialisation_code',100);
            $table->unsignedTinyInteger('mas_specialisation_status_id')->default(1);

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mas_specialisation');
    }
};
