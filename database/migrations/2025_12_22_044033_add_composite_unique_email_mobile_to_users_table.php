<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('user', 'mobile')) {
                $table->string('mobile', 15)
                    ->nullable()
                    ->after('email');
            }
        });

        // Drop old uniques safely
        DB::statement("ALTER TABLE users DROP INDEX IF EXISTS users_email_unique");
        DB::statement("ALTER TABLE users DROP INDEX IF EXISTS users_mobile_unique");

        // Add composite unique
        DB::statement(
            "ALTER TABLE users
             ADD CONSTRAINT uq_users_email_mobile UNIQUE (email, mobile)"
        );
    }

    public function down(): void
    {
        // Drop composite unique
        DB::statement(
            "ALTER TABLE users
             DROP INDEX IF EXISTS uq_users_email_mobile"
        );

        // Restore email unique ONLY if missing
        $exists = DB::selectOne("
            SELECT COUNT(*) AS count
            FROM information_schema.statistics
            WHERE table_schema = DATABASE()
              AND table_name = 'users'
              AND index_name = 'users_email_unique'
        ");

        if ($exists->count == 0) {
            DB::statement(
                "ALTER TABLE users ADD UNIQUE users_email_unique (email)"
            );
        }
    }
};
