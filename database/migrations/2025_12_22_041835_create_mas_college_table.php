<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('mas_college', function (Blueprint $table) {
            $table->id();

            /* =========================
             | STREAM (FK)
             ========================= */
            $table->unsignedBigInteger('mas_college_stream_id');

            /* =========================
             | CORE FIELDS
             ========================= */
            $table->string('mas_college_code', 50);
            $table->string('mas_college_name', 500);
            $table->unsignedBigInteger('mas_college_canonical_college_id')->nullable();

            /* =========================
             | ATTRIBUTES
             ========================= */
            $table->boolean('mas_college_exam_centre')
                ->default(1)
                ->comment('1 = Exam Centre');

            $table->char('mas_college_type', 1)
                ->default('G')
                ->comment('G=Government, P=Private');

            $table->unsignedSmallInteger('mas_college_is_internal')
                ->default(1)
                ->comment('1 = Internal');

            /* =========================
             | STATUS
             ========================= */
            $table->unsignedSmallInteger('mas_college_status_id')
                ->default(1)
                ->comment('1 = Active, 2 = In Active');

            /* =========================
             | AUDIT
             ========================= */
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();

            $table->timestamps();
            $table->softDeletes();

            /* =========================
             | INDEXES & CONSTRAINTS
             ========================= */
            $table->unique(
                ['mas_college_stream_id', 'mas_college_code'],
                'uq_mas_college_stream_code'
            );

            $table->index('mas_college_name');
            $table->index('mas_college_status_id');
            $table->index('mas_college_stream_id');

            /* =========================
             | FOREIGN KEYS
             ========================= */
            $table->foreign('mas_college_stream_id')
                ->references('id')
                ->on('mas_stream')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mas_college');
    }
};
