<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddForeignKeyOnPenjualanItemsBbs111 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('penjualan_item_bbs', function (Blueprint $table) {
            $table->foreign('penjualan_id')
                ->references('id')->on('penjualans')
                ->onDelete('cascade');
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
            $table->dropForeign('penjualan_item_bbs_penjualan_id_foreign');
        });
    }
}
