<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('mas_document_designation_rules', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('mas_document_id');
            $table->unsignedBigInteger('mas_designation_id');

            $table->unsignedTinyInteger('is_required')
                ->default(1)
                ->comment('1=Mandatory, 0=Optional');

            $table->unsignedTinyInteger('status_id')
                ->default(1)
                ->comment('1=Active,2=In Active');

            $table->timestamps();
            $table->softDeletes();

            // ✅ FIX: short explicit index name
            $table->unique(
                ['mas_document_id', 'mas_designation_id'],
                'doc_desig_unique'
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
        Schema::dropIfExists('mas_document_designation_rules');
    }
};
