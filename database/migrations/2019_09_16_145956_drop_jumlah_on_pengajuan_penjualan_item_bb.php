<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropJumlahOnPengajuanPenjualanItemBb extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pengajuan_penjualan_item_bbs', function (Blueprint $table) {
            $table->dropColumn(['jumlah', 'stock_qty']);
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
            $table->integer('jumlah')->default(0);
            $table->integer('stock_qty')->default(0);
        });
    }
}
