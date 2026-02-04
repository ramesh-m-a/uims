<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('bank_details', function (Blueprint $table) {
            $table->bigincrements('id')->autoIncrement();

            $table->unsignedSmallInteger('bank_details_basic_details_id')->nullable();
            $table->string('bank_details_pan_number', 100)->nullable();
            $table->string('bank_details_pan_name', 100)->nullable();
            $table->string('bank_details_epf_number', 100)->nullable();

            $table->unsignedSmallInteger('bank_details_account_type_id')->nullable();
            $table->string('bank_details_account_number', 100)->nullable();
            $table->string('bank_details_account_name', 100)->nullable();
            $table->unsignedSmallInteger('bank_details_bank_id')->nullable();
            $table->string('bank_details_branch_id', 100)->nullable();
            $table->string('bank_details_ifs_code', 100)->nullable();

            $table->string('bank_details_basic_pay', 100)->nullable();
            $table->string('bank_details_gross_pay', 100)->nullable();
            $table->unsignedSmallInteger('bank_details_salary_mode_id')->nullable();

            $table->string('bank_details_aadhar_number', 100)->nullable();
            $table->string('bank_details_pay_scale', 100)->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->string('created_by', 100)->nullable();
            $table->string('updated_by', 100)->nullable();

            $table->index(
                'bank_details_basic_details_id',
                'idx_bank_basic_details_id'
            );
            $table->index(
                ['id','bank_details_account_type_id','bank_details_bank_id','bank_details_salary_mode_id'],
                'idx_bank_details'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bank_details');
    }
};
