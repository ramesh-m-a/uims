<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mas_batch_centre', function (Blueprint $table) {
            $table->id();

            $table->unsignedSmallInteger('mas_batch_id');
            $table->unsignedSmallInteger('mas_centre_id');

            // 0 = main centre, 1 = attached centre
            $table->boolean('is_attached')->default(0);

            // 1 = Active, 2 = Inactive
            $table->unsignedSmallInteger('status_id')->default(1);

            $table->timestamps();
            $table->softDeletes();

            $table->unique(
                ['mas_batch_id', 'mas_centre_id'],
                'uniq_batch_centre'
            );

            $table->index('mas_batch_id');
            $table->index('mas_centre_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mas_batch_centre');
    }
};
