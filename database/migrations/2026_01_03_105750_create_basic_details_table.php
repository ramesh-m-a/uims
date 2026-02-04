<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('basic_details', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('basic_details_user_id')->nullable();
            $table->unsignedSmallInteger('basic_details_gender_id')->nullable();
            $table->date('basic_details_dob')->nullable();
            $table->string('basic_details_father_name', 500)->nullable();

            // ✅ FIXED: Department now FK-safe
            $table->unsignedBigInteger('basic_details_department_id')->nullable();

            $table->unsignedSmallInteger('basic_details_religion_id')->default(0);
            $table->unsignedSmallInteger('basic_details_category_id')->default(0);
            $table->unsignedSmallInteger('basic_details_status_id')->default(3);

            $table->unsignedSmallInteger('basic_details_is_administrative_id')->default(0);

            $table->timestamps();
            $table->softDeletes();

            $table->string('created_by', 100)->nullable();
            $table->string('updated_by', 100)->nullable();

            // Indexes (unchanged)
            $table->index('id', 'idx_basic_details_id');
            $table->index(
                ['id','basic_details_user_id','basic_details_gender_id','basic_details_category_id'],
                'idx_basic_details'
            );
            $table->index(['id','basic_details_religion_id'], 'idx_basic_details2');
            $table->index(
                ['id','basic_details_department_id','basic_details_status_id'],
                'idx_basic_details3'
            );

            // ✅ FOREIGN KEY (SAFE + CORRECT)
            $table->foreign('basic_details_department_id', 'fk_basic_details_department')
                ->references('id')
                ->on('mas_department')
                ->onUpdate('cascade')
                ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('basic_details');
    }
};
