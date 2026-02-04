<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('mas_month', function (Blueprint $table) {
            $table->smallIncrements('id');

            $table->string('mas_month_name', 100)->unique();
            $table->unsignedTinyInteger('mas_month_status_id')->default(1);

            $table->timestamps();
            $table->softDeletes();

            $table->string('created_by', 100)->nullable();
            $table->string('updated_by', 100)->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mas_month');
    }
};
