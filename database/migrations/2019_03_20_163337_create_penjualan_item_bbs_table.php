<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePenjualanItemBbsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('penjualan_item_bbs', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('penjualan_id', false, true);
            $table->integer('kategori_barang_id', false, true);
            $table->integer('qty', false, true);
            $table->decimal('berat', 8, 4);
            $table->integer('price_per_kg');
            $table->integer('value', false, true);
            $table->string('keterangan')->nullable();
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
        Schema::dropIfExists('penjualan_item_bbs');
    }
}
