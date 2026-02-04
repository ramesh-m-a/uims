<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mas_revised_scheme', function (Blueprint $table) {
            $table->smallIncrements('id');

            $table->string('mas_revised_scheme_name', 100);
            $table->string('mas_revised_scheme_short_name', 20);

            $table->unsignedSmallInteger('mas_revised_scheme_status_id')->default(1);

            $table->timestamps();
            $table->softDeletes();

            $table->string('created_by', 100)->nullable();
            $table->string('updated_by', 100)->nullable();

            // Composite unique exactly like DDL
            $table->unique(
                [
                    'mas_revised_scheme_name',
                    'mas_revised_scheme_short_name',
                ],
                'mas_revised_scheme_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mas_revised_scheme');
    }
};
