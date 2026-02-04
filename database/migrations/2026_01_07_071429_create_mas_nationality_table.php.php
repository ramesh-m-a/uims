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
        Schema::create('mas_nationality', function (Blueprint $table) {
            $table->id();

            $table->string('mas_nationality_name', 50);

            $table->unsignedTinyInteger('mas_nationality_status_id')
                ->default(1)
                ->comment('1=Active, 2=In Active');

            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->unique('mas_nationality_name');
            $table->index('mas_nationality_status_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
