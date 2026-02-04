<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        /* =========================================
         | 1. UPDATE mas_document
         ========================================= */
        Schema::table('mas_document', function (Blueprint $table) {
            $table->boolean('mas_document_is_required_global')
                ->default(false)
                ->after('mas_document_type')
                ->comment('1 = Required for all designations');
        });

        /* =========================================
         | 2. CREATE mas_document_designation
         ========================================= */
        Schema::create('mas_document_designation', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('mas_document_id');
            $table->unsignedBigInteger('mas_designation_id');

            $table->boolean('mas_document_is_required')
                ->default(true)
                ->comment('1 = Required, 0 = Optional');

            $table->timestamps();

            /* ========= CONSTRAINTS ========= */

            $table->unique(
                ['mas_document_id', 'mas_designation_id'],
                'uq_document_designation'
            );

            $table->foreign('mas_document_id')
                ->references('id')
                ->on('mas_document')
                ->cascadeOnDelete();

            $table->foreign('mas_designation_id')
                ->references('id')
                ->on('mas_designation')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mas_document_designation');

        Schema::table('mas_document', function (Blueprint $table) {
            $table->dropColumn('mas_document_is_required_global');
        });
    }
};
