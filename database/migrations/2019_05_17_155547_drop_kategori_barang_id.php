<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropKategoriBarangId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pengajuan_penjualan_item_wps', function (Blueprint $table) {
            $table->dropColumn('kategori_barang_id');
            $table->string('material_description');
            $table->decimal('stock', 38, 4);
            $table->decimal('berat', 38, 4);
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
            $table->integer('kategori_barang_id', false, true)->nullable();
            $table->dropColumn(['material_description', 'stock', 'berat']);
        });
    }
}
