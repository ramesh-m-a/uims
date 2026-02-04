<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mas_stream_designation', function (Blueprint $table) {
            $table->id();

            $table->foreignId('stream_id')
                ->constrained('mas_stream')
                ->cascadeOnDelete();

            $table->foreignId('designation_id')
                ->constrained('mas_designation')
                ->cascadeOnDelete();

            // Optional but useful for future enable/disable per mapping
            $table->unsignedTinyInteger('status_id')->default(1);

            $table->unique(['stream_id', 'designation_id'], 'mas_stream_designation_unique');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mas_stream_designation');
    }
};
