<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mas_ifsc', function (Blueprint $table) {

            $table->bigIncrements('id');

            $table->string('mas_ifsc_number', 20);
            $table->unsignedBigInteger('mas_ifsc_bank_id');
            $table->unsignedBigInteger('mas_ifsc_branch_id');

            $table->string('mas_ifsc_micr', 50)->nullable();

            $table->tinyInteger('mas_ifsc_status_id')->default(1)
                ->comment('1=Active, 2=In active');

            $table->timestamps();
            $table->softDeletes();
            /* ==========================
             | UNIQUE
             ========================== */
            $table->unique('mas_ifsc_number');

            /* ==========================
             | INDEXES
             ========================== */
            $table->index('mas_ifsc_bank_id');
            $table->index('mas_ifsc_branch_id');

            /* ==========================
             | FKs
             ========================== */
            $table->foreign('mas_ifsc_bank_id')
                ->references('id')
                ->on('mas_bank')
                ->onDelete('restrict');

            $table->foreign('mas_ifsc_branch_id')
                ->references('id')
                ->on('mas_bank_branch')
                ->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mas_ifsc');
    }
};
