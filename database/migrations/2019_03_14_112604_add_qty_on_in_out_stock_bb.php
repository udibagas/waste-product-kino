<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddQtyOnInOutStockBb extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('in_out_stock_bbs', function (Blueprint $table) {
            $table->integer('qty_in', false, true)->default(0);
            $table->integer('qty_out', false, true)->default(0);
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
            $table->dropColumn(['qty_in', 'qty_out']);
        });
    }
}
