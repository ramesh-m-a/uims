<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('mas_religion', function (Blueprint $table) {
            $table->id();

            $table->string('mas_religion_name', 100);

            $table->unsignedTinyInteger('mas_religion_status_id')
                ->default(1)
                ->comment('1=Active, 2=In Active');

            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->unique('mas_religion_name');
            $table->index('mas_religion_status_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mas_religion');
    }
};
