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
        Schema::create('mas_status', function (Blueprint $table) {
            $table->id(); // BIGINT UNSIGNED

            $table->string('mas_status_name', 100);

            $table->string('mas_status_code', 100);

            $table->string('mas_status_module', 100)
                ->comment('finance, teacher_profile, approval, hr, exam')->nullable();

            $table->unsignedTinyInteger('is_active')
                ->default(1)
                ->comment('1=Active,0=Inactive');

            $table->unsignedSmallInteger('sort_order')
                ->default(0);

            $table->string('mas_status_label_colour', 50)->nullable();

            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();

            $table->timestamps();
            $table->softDeletes();   // ADD THIS LINE

            // Important: status unique per module
            $table->unique(['mas_status_name', 'mas_status_module']);
            $table->index('mas_status_module');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mas_status');
    }
};
