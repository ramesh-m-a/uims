<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('mas_gender', function (Blueprint $table) {

            if (!Schema::hasColumn('mas_gender', 'created_by')) return;

            $table->foreign('created_by', 'fk_mas_gender_created_by')
                ->references('id')->on('users')
                ->nullOnDelete();

            $table->foreign('updated_by', 'fk_mas_gender_updated_by')
                ->references('id')->on('users')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('mas_gender', function (Blueprint $table) {
            $table->dropForeign('fk_mas_gender_created_by');
            $table->dropForeign('fk_mas_gender_updated_by');
        });
    }
};
