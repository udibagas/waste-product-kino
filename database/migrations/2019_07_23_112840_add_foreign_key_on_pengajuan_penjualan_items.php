<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddForeignKeyOnPengajuanPenjualanItems extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pengajuan_penjualan_item_wps', function (Blueprint $table) {
            $table->foreign('pengajuan_penjualan_id')
                ->references('id')->on('pengajuan_penjualans')
                ->onDelete('cascade');
        });

        Schema::table('pengajuan_penjualan_item_bbs', function (Blueprint $table) {
            $table->foreign('pengajuan_penjualan_id')
                ->references('id')->on('pengajuan_penjualans')
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
        Schema::table('pengajuan_penjualan_item_wps', function (Blueprint $table) {
            $table->dropForeign('pengajuan_penjualan_item_wps_pengajuan_penjualan_id_foreign');
        });

        Schema::table('pengajuan_penjualan_item_bbs', function (Blueprint $table) {
            $table->dropForeign('pengajuan_penjualan_item_bbs_pengajuan_penjualan_id_foreign');
        });
    }
}
