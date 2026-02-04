<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('mas_batch_range', function (Blueprint $table) {
            $table->smallIncrements('id');

            $table->integer('mas_batch_range_batch_id');
            $table->unsignedSmallInteger('mas_batch_range_batch_split_id')->default(0);

            $table->unsignedSmallInteger('mas_batch_range_status_id')->default(50);

            $table->timestamps();
            $table->softDeletes();

            $table->string('created_by', 100)->nullable();
            $table->string('updated_by', 100)->nullable();

            $table->date('mas_batch_range_from_date');
            $table->date('mas_batch_range_to_date');

            $table->unsignedSmallInteger('mas_batch_range_students')->default(0);

            $table->string('mas_batch_range_batch_name', 100);
            $table->unsignedSmallInteger('mas_batch_range_group_number')->default(0);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mas_batch_range');
    }
};
