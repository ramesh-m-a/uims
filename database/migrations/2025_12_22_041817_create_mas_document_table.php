<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('mas_document', function (Blueprint $table) {
            $table->id();

            $table->string('mas_document_name', 200);
            $table->string('mas_document_description', 500);

            // define in correct order instead of using ->after()
            $table->unsignedTinyInteger('mas_document_status_id')
                ->default(1)
                ->comment('1=Active, 2=In Active');

            $table->unsignedSmallInteger('mas_document_sort_order')
                ->default(0);

            $table->unsignedTinyInteger('mas_document_type')
                ->default(0)
                ->comment('0=General, 1=Identity, 2=Academic, 3=Experience');

            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->unique('mas_document_name');
            $table->index('mas_document_status_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mas_document');
    }
};
