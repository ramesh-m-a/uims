<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // =========================
        // USERS
        // =========================
        Schema::table('users', function (Blueprint $table) {

            $indexes = collect(DB::select("SHOW INDEX FROM users"))
                ->pluck('Key_name')
                ->unique()
                ->toArray();

            if (! in_array('idx_users_status', $indexes)) {
                $table->index('user_status_id', 'idx_users_status');
            }

            if (! in_array('idx_users_stream', $indexes)) {
                $table->index('user_stream_id', 'idx_users_stream');
            }

            if (! in_array('idx_users_designation', $indexes)) {
                $table->index('user_designation_id', 'idx_users_designation');
            }

            if (! in_array('idx_users_college', $indexes)) {
                $table->index('user_college_id', 'idx_users_college');
            }
        });

        // =========================
        // BASIC DETAILS
        // =========================
        Schema::table('basic_details', function (Blueprint $table) {

            $indexes = collect(DB::select("SHOW INDEX FROM basic_details"))
                ->pluck('Key_name')
                ->unique()
                ->toArray();

            if (! in_array('idx_basic_details_user', $indexes)) {
                $table->index('basic_details_user_id', 'idx_basic_details_user');
            }

            if (! in_array('idx_basic_details_department', $indexes)) {
                $table->index('basic_details_department_id', 'idx_basic_details_department');
            }
        });

        // =========================
        // USER PROFILE DRAFTS
        // =========================
        Schema::table('user_profile_drafts', function (Blueprint $table) {

            $indexes = collect(DB::select("SHOW INDEX FROM user_profile_drafts"))
                ->pluck('Key_name')
                ->unique()
                ->toArray();
/*
            if (! in_array('idx_drafts_principal', $indexes)) {
                $table->index('principal_id', 'idx_drafts_principal');
            }*/

            if (! in_array('idx_drafts_status', $indexes)) {
                $table->index('status_id', 'idx_drafts_status');
            }
        });
    }

    public function down(): void
    {
        // =========================
        // USERS
        // =========================
        Schema::table('users', function (Blueprint $table) {

            $indexes = collect(DB::select("SHOW INDEX FROM users"))
                ->pluck('Key_name')
                ->unique()
                ->toArray();

            if (in_array('idx_users_status', $indexes)) {
                $table->dropIndex('idx_users_status');
            }

            if (in_array('idx_users_stream', $indexes)) {
                $table->dropIndex('idx_users_stream');
            }

            if (in_array('idx_users_designation', $indexes)) {
                $table->dropIndex('idx_users_designation');
            }

            if (in_array('idx_users_college', $indexes)) {
                $table->dropIndex('idx_users_college');
            }
        });

        // =========================
        // BASIC DETAILS
        // =========================
        Schema::table('basic_details', function (Blueprint $table) {

            $indexes = collect(DB::select("SHOW INDEX FROM basic_details"))
                ->pluck('Key_name')
                ->unique()
                ->toArray();

            if (in_array('idx_basic_details_user', $indexes)) {
                $table->dropIndex('idx_basic_details_user');
            }

            if (in_array('idx_basic_details_department', $indexes)) {
                $table->dropIndex('idx_basic_details_department');
            }
        });

        // =========================
        // USER PROFILE DRAFTS
        // =========================
        Schema::table('user_profile_drafts', function (Blueprint $table) {

            $indexes = collect(DB::select("SHOW INDEX FROM user_profile_drafts"))
                ->pluck('Key_name')
                ->unique()
                ->toArray();

          /*  if (in_array('idx_drafts_principal', $indexes)) {
                $table->dropIndex('idx_drafts_principal');
            }*/

            if (in_array('idx_drafts_status', $indexes)) {
                $table->dropIndex('idx_drafts_status');
            }
        });
    }
};
