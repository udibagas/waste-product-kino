<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddToleransiPenjualanOnKategori extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('kategori_barangs', function (Blueprint $table) {
            $table->tinyInteger('toleransi_penjualan')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('kategori_barangs', function (Blueprint $table) {
            $table->dropColumn('toleransi_penjualan');
        });
    }
}
