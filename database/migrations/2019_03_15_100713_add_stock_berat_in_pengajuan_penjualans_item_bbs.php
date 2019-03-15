<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddStockBeratInPengajuanPenjualansItemBbs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pengajuan_penjualan_item_bbs', function (Blueprint $table) {
            $table->integer('stock_qty')->nullable();
            $table->integer('stock_berat')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pengajuan_penjualan_item_bbs', function (Blueprint $table) {
            $table->dropColumn(['stock_berat', 'stock_qty']);
        });
    }
}
