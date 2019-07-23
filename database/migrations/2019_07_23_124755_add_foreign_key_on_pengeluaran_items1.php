<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddForeignKeyOnPengeluaranItems1 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pengeluaran_items', function (Blueprint $table) {
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
        Schema::table('pengeluaran_items', function (Blueprint $table) {
            $table->dropForeign('pengeluaran_items_kategori_barang_id_foreign');
        });
    }
}
