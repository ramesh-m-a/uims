
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mas_revised_scheme_stream', function (Blueprint $table) {

            $table->bigIncrements('id');

            $table->unsignedSmallInteger('mas_revised_scheme_id'); // matches partials
            $table->unsignedBigInteger('mas_stream_id'); // matches stream.id

            $table->unsignedTinyInteger(
                'mas_revised_scheme_stream_status_id'
            )->default(1);

            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->unique(
                [
                    'mas_revised_scheme_id',
                    'mas_stream_id',
                    'deleted_at'
                ],
                'uq_scheme_stream_active'
            );

            $table->index('mas_revised_scheme_id', 'idx_rss_scheme');
            $table->index('mas_stream_id', 'idx_rss_stream');

            /*
            |--------------------------------------------------------------------------
            | FK — IMPORTANT FIX HERE
            |--------------------------------------------------------------------------
            */

            $table->foreign('mas_revised_scheme_id', 'fk_rss_scheme')
                ->references('id') // 🔥 FIXED
                ->on('mas_revised_scheme')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->foreign('mas_stream_id', 'fk_rss_stream')
                ->references('id')
                ->on('mas_stream')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
        });


    }

    public function down(): void
    {
        Schema::table('mas_revised_scheme_stream', function (Blueprint $table) {
            $table->dropForeign('fk_rss_scheme');
            $table->dropForeign('fk_rss_stream');
            $table->dropForeign('fk_rss_created_by');
            $table->dropForeign('fk_rss_updated_by');
        });

        Schema::dropIfExists('mas_revised_scheme_stream');
    }
};
