<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('permission_audits', function (Blueprint $table) {

            $table->engine = 'InnoDB';

            $table->id();

            /*
             | IMPORTANT:
             | No foreign key constraint
             | users.partials is NOT compatible
             */
            $table->unsignedBigInteger('user_id')->index();

            $table->string('module');
            $table->string('action');
            $table->string('permission');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('permission_audits');
    }
};
