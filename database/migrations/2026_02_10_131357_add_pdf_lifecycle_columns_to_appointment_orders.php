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
        Schema::table('appointment_orders', function (Blueprint $table) {

            $table->string('pdf_status', 20)->nullable()->after('pdf_disk');

            $table->timestamp('pdf_generated_at')->nullable();

            $table->text('pdf_last_error')->nullable();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('appointment_orders', function (Blueprint $table) {
            //
        });
    }
};
