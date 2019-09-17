<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropQtyOnPenjualanItemBb extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('penjualan_item_bbs', function (Blueprint $table) {
            $table->dropColumn([ 'qty', 'keterangan', 'stock_qty' ]);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('penjualan_item_bbs', function (Blueprint $table) {
            $table->integer('qty')->nullable();
            $table->integer('stock_qty')->nullable();
            $table->string('keterangan')->nullable();
        });
    }
}
