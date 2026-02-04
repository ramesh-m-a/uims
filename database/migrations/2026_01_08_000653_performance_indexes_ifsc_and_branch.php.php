<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // IFSC: status + pagination
        DB::statement(
            'CREATE INDEX IF NOT EXISTS mas_ifsc_status_id_id_index
             ON mas_ifsc (mas_ifsc_status_id, id)'
        );

        // Bank Branch: bank → branch dropdown
        DB::statement(
            'CREATE INDEX IF NOT EXISTS mas_bank_branch_bank_id_branch_name_index
             ON mas_bank_branch (mas_bank_branch_bank_id, mas_bank_branch_branch_name)'
        );
    }

    public function down(): void
    {
        DB::statement(
            'DROP INDEX IF EXISTS mas_ifsc_status_id_id_index
             ON mas_ifsc'
        );

        DB::statement(
            'DROP INDEX IF EXISTS mas_bank_branch_bank_id_branch_name_index
             ON mas_bank_branch'
        );
    }
};
