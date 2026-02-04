<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('mas_account_type', function (Blueprint $table) {

            // Matches: smallint unsigned auto_increment
            $table->smallIncrements('id');

            $table->string('mas_account_type_name', 20);

            $table->timestamps();
            $table->softDeletes();

            // tinyint default 50
            $table->unsignedTinyInteger('mas_account_type_status_id')
                ->default(1);

            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mas_account_type');
    }
};
