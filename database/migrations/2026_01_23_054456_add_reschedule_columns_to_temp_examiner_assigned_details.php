<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('temp_examiner_assigned_details', function (Blueprint $table) {
            $table->date('original_from_date')->nullable()->after('from_date');
            $table->boolean('is_rescheduled')->default(false)->after('original_from_date');
            $table->timestamp('rescheduled_at')->nullable()->after('is_rescheduled');
            $table->unsignedBigInteger('rescheduled_by')->nullable()->after('rescheduled_at');
        });
    }

    public function down(): void
    {
        Schema::table('temp_examiner_assigned_details', function (Blueprint $table) {
            $table->dropColumn([
                'original_from_date',
                'is_rescheduled',
                'rescheduled_at',
                'rescheduled_by',
            ]);
        });
    }
};
