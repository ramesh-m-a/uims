<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('permissions', function (Blueprint $table) {

            // Check if unique index already exists
            $indexes = DB::select("SHOW INDEX FROM permissions WHERE Key_name = 'permissions_name_unique'");

            if (empty($indexes)) {
                $table->unique('name', 'permissions_name_unique');
            }
        });
    }

    public function down(): void
    {
        Schema::table('permissions', function (Blueprint $table) {

            $indexes = DB::select("SHOW INDEX FROM permissions WHERE Key_name = 'permissions_name_unique'");

            if (!empty($indexes)) {
                $table->dropUnique('permissions_name_unique');
            }
        });
    }
};
