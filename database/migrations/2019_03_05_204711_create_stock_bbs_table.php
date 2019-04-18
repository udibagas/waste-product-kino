<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStockBbsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stock_bbs', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('kategori_barang_id', false, true);
            $table->string('lokasi', 20);
            $table->string('plant', 10);
            $table->decimal('stock', 38, 4);
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
        Schema::dropIfExists('stock_bbs');
    }
}
