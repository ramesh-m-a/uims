<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('appointment_order_sequences', function (Blueprint $table) {

            $table->id();

            $table->integer('year');
            $table->integer('month');

            $table->integer('current_sequence')->default(0);

            $table->timestamps();

            $table->unique(['year', 'month']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('appointment_order_sequences');
    }
};
