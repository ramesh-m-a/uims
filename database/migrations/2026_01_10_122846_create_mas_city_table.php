<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('mas_city', function (Blueprint $table) {

            // Matches: smallint unsigned auto_increment
            $table->smallIncrements('id');

            $table->unsignedSmallInteger('mas_city_taluk_id')->nullable();
            $table->unsignedSmallInteger('mas_city_district_id')->nullable();

            $table->string('mas_city_name', 100);

            $table->timestamps();
            $table->softDeletes();

            // tinyint default 50
            $table->unsignedTinyInteger('mas_city_status_id')
                ->default(50);

            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();

            // composite unique
            $table->unique(
                ['mas_city_taluk_id', 'mas_city_district_id', 'mas_city_name'],
                'mas_city_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mas_city');
    }
};
