<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddLokasiAsalIdOnInOutStocBbs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('in_out_stock_bbs', function (Blueprint $table) {
            $table->integer('lokasi_asal_id', false, true)->nullable();
            $table->integer('lokasi_terima_id', false, true)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('in_out_stock_bbs', function (Blueprint $table) {
            $table->dropColumn(['lokasi_asal_id', 'lokasi_terima_id']);
        });
    }
}
