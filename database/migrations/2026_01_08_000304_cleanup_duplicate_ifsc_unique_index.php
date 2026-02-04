<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $indexes = collect(
            DB::select("SHOW INDEX FROM mas_ifsc")
        )->pluck('Key_name')->unique();

        if ($indexes->contains('mas_ifsc_code_mas_ifsc_number_unique')) {
            DB::statement(
                'ALTER TABLE mas_ifsc
                 DROP INDEX mas_ifsc_mas_ifsc_number_unique'
            );
        }
    }

    public function down(): void
    {
        // Optional: re-create only if you really want it back
        DB::statement(
            'ALTER TABLE mas_ifsc
             ADD UNIQUE KEY mas_ifsc_mas_ifsc_number_unique (mas_ifsc_number)'
        );
    }
};
