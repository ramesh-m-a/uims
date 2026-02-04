<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Check if index already exists
        $indexExists = collect(
            DB::select("SHOW INDEX FROM mas_ifsc WHERE Key_name = 'mas_ifsc_number_unique'")
        )->isNotEmpty();

        if (! $indexExists) {
            DB::statement(
                'ALTER TABLE mas_ifsc
                 ADD UNIQUE KEY mas_ifsc_number_unique (mas_ifsc_number)'
            );
        }
    }

    public function down(): void
    {
        // Drop index only if it exists
        $indexExists = collect(
            DB::select("SHOW INDEX FROM mas_ifsc WHERE Key_name = 'mas_ifsc_number_unique'")
        )->isNotEmpty();

        if ($indexExists) {
            DB::statement(
                'ALTER TABLE mas_ifsc
                 DROP INDEX mas_ifsc_number_unique'
            );
        }
    }
};
