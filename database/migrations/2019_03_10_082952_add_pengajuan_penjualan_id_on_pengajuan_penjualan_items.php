<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPengajuanPenjualanIdOnPengajuanPenjualanItems extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pengajuan_penjualan_item_bbs', function (Blueprint $table) {
            $table->integer('pengajuan_penjualan_id', false, true);
        });

        Schema::table('pengajuan_penjualan_item_wps', function (Blueprint $table) {
            $table->integer('pengajuan_penjualan_id', false, true);
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
            $table->dropColumn('pengajuan_penjualan_id');
        });

        Schema::table('pengajuan_penjualan_item_wps', function (Blueprint $table) {
            $table->dropColumn('pengajuan_penjualan_id');
        });
    }
}
