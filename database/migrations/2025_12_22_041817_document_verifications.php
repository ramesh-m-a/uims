<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('document_verifications', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('document_details_id');

            $table->string('verification_level', 50)
                ->comment('principal, rguhs, finance, admin');

            $table->unsignedTinyInteger('status')
                ->comment('0=pending, 1=approved, 2=rejected');

            $table->text('comments')->nullable();

            $table->unsignedBigInteger('verified_by')->nullable();

            $table->timestamp('verified_at')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index('document_details_id');
            $table->index('verification_level');

            $table->foreign('document_details_id')
                ->references('id')
                ->on('mas_document')
                ->cascadeOnDelete();
        });

    }

    public function down(): void
    {
        Schema::dropIfExists('document_verifications');
    }
};
