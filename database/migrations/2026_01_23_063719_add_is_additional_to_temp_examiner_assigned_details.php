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
        Schema::table('temp_examiner_assigned_details', function (Blueprint $table) {
            $table->tinyInteger('is_additional')
                ->default(0)
                ->after('is_rescheduled')
                ->index();
        });
    }

    public function down(): void
    {
        Schema::table('temp_examiner_assigned_details', function (Blueprint $table) {
            $table->dropColumn('is_additional');
        });
    }

};
