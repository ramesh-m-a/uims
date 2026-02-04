<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('mas_stream', function (Blueprint $table) {
            $table->id();

            $table->string('mas_stream_name', 50);
            $table->string('mas_stream_short_code', 5);
            $table->string('mas_stream_short_name', 10);

            $table->unsignedTinyInteger('mas_stream_status_id')
                ->default(1)
                ->comment('1=Active, 2=In Active');

            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index('mas_stream_name');
            $table->index('mas_stream_status_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mas_stream');
    }
};
