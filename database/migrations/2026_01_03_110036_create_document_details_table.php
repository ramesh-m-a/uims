<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('document_details', function (Blueprint $table) {
            $table->bigincrements('id')->nullable();

            $table->unsignedInteger('document_details_basic_details_id')->nullable();
            $table->unsignedSmallInteger('document_details_document_id')->nullable();
            $table->string('document_details_file_path', 500)->nullable();

            $table->unsignedSmallInteger('document_details_status')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->string('created_by', 100)->nullable();
            $table->string('updated_by', 100)->nullable();

            $table->index(
                'document_details_basic_details_id',
                'idx_document_basic_details_id'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('document_details');
    }
};
