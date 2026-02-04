<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('mas_year', function (Blueprint $table) {
            $table->smallIncrements('id');

            $table->string('mas_year_year', 4)->unique();
            $table->unsignedSmallInteger('mas_year_status_id')->default(1);

            $table->timestamps();
            $table->softDeletes();

            $table->string('created_by', 100)->nullable();
            $table->string('updated_by', 100)->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mas_year');
    }
};
