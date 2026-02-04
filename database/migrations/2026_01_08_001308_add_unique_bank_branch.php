<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Add composite unique index: bank_id + branch_name
        DB::statement(
            'CREATE UNIQUE INDEX IF NOT EXISTS
             mas_bank_branch_bank_id_branch_name_unique
             ON mas_bank_branch (mas_bank_branch_bank_id, mas_bank_branch_branch_name)'
        );
    }

    public function down(): void
    {
        DB::statement(
            'DROP INDEX IF EXISTS
             mas_bank_branch_bank_id_branch_name_unique
             ON mas_bank_branch'
        );
    }
};
