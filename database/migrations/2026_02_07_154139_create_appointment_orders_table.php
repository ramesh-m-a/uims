<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('appointment_orders', function (Blueprint $table) {

            $table->id();

            // Relations
            $table->unsignedBigInteger('allocation_id');
            $table->unsignedBigInteger('examiner_id');
            $table->unsignedBigInteger('batch_id');

            // Identity
            $table->string('order_number', 50);
            $table->integer('order_version')->default(1);
            $table->boolean('is_latest')->default(true);

            // File
            $table->text('pdf_path')->nullable();
            $table->string('pdf_disk', 50)->default('local');

            // Integrity
            $table->string('document_hash', 128)->nullable();
            $table->json('qr_payload')->nullable();

            // Audit
            $table->unsignedBigInteger('generated_by');
            $table->string('generated_role', 30)->nullable();
            $table->timestamp('generated_at')->nullable();

            // Regeneration
            $table->unsignedBigInteger('regenerated_from_id')->nullable();
            $table->text('remarks')->nullable();

            // Soft status
            $table->string('status', 30)->default('GENERATED');

            $table->timestamps();

            // Indexes
            $table->index(['allocation_id', 'is_latest']);
            $table->index('examiner_id');
            $table->index('batch_id');
            $table->unique(['order_number', 'order_version']);

        });

    }

    public function down(): void
    {
        Schema::dropIfExists('appointment_orders');
    }
};
