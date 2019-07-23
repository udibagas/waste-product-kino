<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddForeignKeyOnPengajuanPenjualanItemBbs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pengajuan_penjualan_item_bbs', function (Blueprint $table) {
            $table->foreign('kategori_barang_id')->references('id')->on('kategori_barangs');
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
            $table->dropForeign('pengajuan_penjualan_item_bbs_kategori_barang_id_foreign');
        });
    }
}
