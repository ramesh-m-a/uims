<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('mas_batch_split', function (Blueprint $table) {
            $table->unsignedSmallInteger('mas_batch_split_centre_id')
                ->nullable()
                ->after('mas_batch_split_batch_id')
                ->index('idx_batch_split_centre');
        });

        Schema::table('mas_batch_range', function (Blueprint $table) {
            $table->unsignedSmallInteger('mas_batch_range_centre_id')
                ->nullable()
                ->after('mas_batch_range_batch_id')
                ->index('idx_batch_range_centre');
        });
    }

    public function down(): void
    {
        Schema::table('mas_batch_split', function (Blueprint $table) {
            $table->dropIndex('idx_batch_split_centre');
            $table->dropColumn('mas_batch_split_centre_id');
        });

        Schema::table('mas_batch_range', function (Blueprint $table) {
            $table->dropIndex('idx_batch_range_centre');
            $table->dropColumn('mas_batch_range_centre_id');
        });
    }
};
