<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldsOnPenjualanItemsBb extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('penjualan_item_bbs', function (Blueprint $table) {
            $table->decimal('stock_berat', 38, 4)->nullable();
            $table->decimal('timbangan_manual', 38, 4)->nullable();
            $table->integer('stock_qty', false, true)->nullable();
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
            $table->dropColumn(['stock_berat', 'stock_qty', 'timbangan_manual']);
        });
    }
}
