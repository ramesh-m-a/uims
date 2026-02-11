<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chart_documents', function (Blueprint $table) {

            $table->id();

            /*
            |--------------------------------------------------------------------------
            | RELATIONS (Operational Source Link)
            |--------------------------------------------------------------------------
            */
            $table->unsignedBigInteger('allocation_id');
            $table->unsignedBigInteger('examiner_id')->nullable();
            $table->unsignedBigInteger('batch_id')->nullable();

            /*
            |--------------------------------------------------------------------------
            | VERSION CONTROL (Future Safe)
            |--------------------------------------------------------------------------
            */
            $table->unsignedInteger('document_version')->default(1);
            $table->boolean('is_latest')->default(true);

            /*
            |--------------------------------------------------------------------------
            | FILE STORAGE
            |--------------------------------------------------------------------------
            */
            $table->string('pdf_path')->nullable();
            $table->string('pdf_disk')->default('public');

            $table->string('pdf_status')->nullable(); // pending | generating | ready | failed
            $table->timestamp('pdf_generated_at')->nullable();
            $table->text('pdf_last_error')->nullable();

            /*
            |--------------------------------------------------------------------------
            | DOCUMENT INTEGRITY
            |--------------------------------------------------------------------------
            */
            $table->string('document_hash', 128)->nullable();

            /*
            |--------------------------------------------------------------------------
            | AUDIT
            |--------------------------------------------------------------------------
            */
            $table->unsignedBigInteger('generated_by')->nullable();
            $table->string('generated_role')->nullable();
            $table->timestamp('generated_at')->nullable();

            /*
            |--------------------------------------------------------------------------
            | REGENERATION CHAIN
            |--------------------------------------------------------------------------
            */
            $table->unsignedBigInteger('regenerated_from_id')->nullable();

            /*
            |--------------------------------------------------------------------------
            | STATUS
            |--------------------------------------------------------------------------
            */
            $table->string('status')->nullable();

            $table->timestamps();

            /*
            |--------------------------------------------------------------------------
            | INDEXES
            |--------------------------------------------------------------------------
            */
            $table->index('allocation_id');
            $table->index(['allocation_id', 'is_latest']);
            $table->index('pdf_status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chart_documents');
    }
};
