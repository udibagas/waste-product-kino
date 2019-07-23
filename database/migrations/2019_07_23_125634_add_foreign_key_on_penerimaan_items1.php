<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddForeignKeyOnPenerimaanItems1 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('penerimaan_items', function (Blueprint $table) {
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
        Schema::table('penerimaan_items', function (Blueprint $table) {
            $table->dropForeign('penerimaan_items_kategori_barang_id_foreign');
        });
    }
}
