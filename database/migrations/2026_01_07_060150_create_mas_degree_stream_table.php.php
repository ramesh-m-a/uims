<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('mas_degree_stream', function (Blueprint $table) {
        $table->id();

        $table->unsignedTinyInteger('mas_degree_stream_status_id')->default(1);

        $table->timestamps();
        $table->softDeletes();

        $table->foreignId('mas_degree_id')
            ->constrained('mas_degree')
            ->cascadeOnDelete();

        $table->foreignId('mas_stream_id')
            ->constrained('mas_stream')
            ->cascadeOnDelete();

        $table->unique(['mas_degree_id', 'mas_stream_id']);
    });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
