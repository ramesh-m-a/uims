<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mas_admin_role', function (Blueprint $table) {
            $table->id();

            $table->string('mas_admin_role_name', 150);
            $table->tinyInteger('mas_admin_role_status_id')->default(1)
                ->comment('1=Active, 2=Inactive');

            $table->timestamps();
            $table->softDeletes();

            $table->unique('mas_admin_role_name');
            $table->index('mas_admin_role_status_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mas_admin_role');
    }
};
