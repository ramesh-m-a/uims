<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mas_examiner_scheme_distribution', function (Blueprint $table) {

            // PK
            $table->unsignedSmallInteger('id', true);

            // Columns
            $table->unsignedSmallInteger('mas_examiner_scheme_distribution_scheme_id');
            $table->unsignedSmallInteger('mas_examiner_scheme_distribution_examiner_type_id');
            $table->unsignedTinyInteger('mas_examiner_scheme_distribution_examiner_type_count');

            $table->unsignedSmallInteger('mas_examiner_scheme_distribution_status_id')
                ->default(50);

            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();

            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->softDeletes();

            // Unique (Soft Delete Aware)
            $table->unique([
                'mas_examiner_scheme_distribution_scheme_id',
                'mas_examiner_scheme_distribution_examiner_type_id',
                'deleted_at'
            ], 'uq_examiner_scheme_distribution_active');

            // Indexes
            $table->index(
                'mas_examiner_scheme_distribution_scheme_id',
                'idx_esd_scheme'
            );

            $table->index(
                'mas_examiner_scheme_distribution_examiner_type_id',
                'idx_esd_examiner_type'
            );

            $table->index(
                'mas_examiner_scheme_distribution_status_id',
                'idx_esd_status'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mas_examiner_scheme_distribution');
    }
};
