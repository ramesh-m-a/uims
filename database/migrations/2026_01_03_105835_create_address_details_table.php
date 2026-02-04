<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('address_details', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedSmallInteger('address_details_basic_details_id')->nullable();
            $table->boolean('address_details_same_address')->default(true);

            $table->string('address_details_p_address_1', 500)->nullable();
            $table->string('address_details_p_address_2', 500)->nullable();
            $table->string('address_details_p_address_3', 500)->nullable();
            $table->string('address_details_p_district', 500)->nullable();
            $table->string('address_details_p_state_id', 500)->nullable();
            $table->unsignedInteger('address_details_p_pincode')->nullable();

            $table->string('address_details_t_address_1', 500)->nullable();
            $table->string('address_details_t_address_2', 500)->nullable();
            $table->string('address_details_t_address_3', 500)->nullable();
            $table->string('address_details_t_district', 500)->nullable();
            $table->string('address_details_t_state_id', 500)->nullable();
            $table->unsignedInteger('address_details_t_pincode')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->string('created_by', 100)->nullable();
            $table->string('updated_by', 100)->nullable();

            $table->index(
                'address_details_basic_details_id',
                'idx_address_basic_details_id'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('address_details');
    }
};
