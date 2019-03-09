<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePenjualanItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('penjualan_items', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('penjualan_id', false, true);
            $table->integer('kategori_produk_id', false, true);
            $table->integer('timbangan_manual', false, true);
            $table->integer('harga_per_kg', false, true);
            $table->integer('value', false, true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('penjualan_items');
    }
}
