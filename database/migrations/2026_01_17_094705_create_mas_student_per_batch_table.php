<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mas_student_per_batch', function (Blueprint $table) {

            /*
            |--------------------------------------------------------------------------
            | PRIMARY KEY
            |--------------------------------------------------------------------------
            */
            $table->smallIncrements('id');

            /*
            |--------------------------------------------------------------------------
            | FOREIGN KEYS (MATCH MASTER TYPES EXACTLY)
            |--------------------------------------------------------------------------
            */

            // mas_subject.id = SMALLINT UNSIGNED
            $table->unsignedSmallInteger(
                'mas_student_per_batch_subject_id'
            );

            // mas_degree.id = BIGINT UNSIGNED
            $table->unsignedBigInteger(
                'mas_student_per_batch_degree_id'
            );

            /*
            |--------------------------------------------------------------------------
            | CONFIG DATA
            |--------------------------------------------------------------------------
            */

            $table->unsignedSmallInteger(
                'mas_student_per_batch_total_number'
            );

            $table->unsignedSmallInteger(
                'mas_student_per_batch_per_day'
            );

            $table->unsignedSmallInteger(
                'mas_student_per_batch_status_id'
            )->default(1);

            /*
            |--------------------------------------------------------------------------
            | AUDIT
            |--------------------------------------------------------------------------
            */

            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();

            $table->timestamps();
            $table->softDeletes();

            /*
            |--------------------------------------------------------------------------
            | UNIQUE (SOFT DELETE SAFE)
            |--------------------------------------------------------------------------
            */

            $table->unique(
                [
                    'mas_student_per_batch_subject_id',
                    'mas_student_per_batch_degree_id',
                    'deleted_at'
                ],
                'uq_student_per_batch_active'
            );

            /*
            |--------------------------------------------------------------------------
            | INDEXES
            |--------------------------------------------------------------------------
            */

            $table->index(
                'mas_student_per_batch_subject_id',
                'idx_spb_subject'
            );

            $table->index(
                'mas_student_per_batch_degree_id',
                'idx_spb_degree'
            );

            /*
            |--------------------------------------------------------------------------
            | FOREIGN CONSTRAINTS
            |--------------------------------------------------------------------------
            */

            $table->foreign(
                'mas_student_per_batch_subject_id',
                'fk_spb_subject'
            )
                ->references('id')
                ->on('mas_subject')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->foreign(
                'mas_student_per_batch_degree_id',
                'fk_spb_degree'
            )
                ->references('id')
                ->on('mas_degree')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->foreign('created_by', 'fk_spb_created_by')
                ->references('id')
                ->on('users')
                ->nullOnDelete();

            $table->foreign('updated_by', 'fk_spb_updated_by')
                ->references('id')
                ->on('users')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('mas_student_per_batch', function (Blueprint $table) {
            $table->dropForeign('fk_spb_subject');
            $table->dropForeign('fk_spb_degree');
            $table->dropForeign('fk_spb_created_by');
            $table->dropForeign('fk_spb_updated_by');
        });

        Schema::dropIfExists('mas_student_per_batch');
    }
};
