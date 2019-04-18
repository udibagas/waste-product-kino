<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInOutStockBbsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('in_out_stock_bbs', function (Blueprint $table) {
            $table->increments('id');
            $table->date('tanggal');
            $table->string('lokasi_asal', 50);
            $table->string('lokasi_terima', 50);
            $table->integer('kategori_barang_id', false, true);
            $table->string('eun', 10);
            $table->decimal('stock_in', 38, 4);
            $table->decimal('stock_out', 38, 4);
            $table->string('no_sj', 100);
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
        Schema::dropIfExists('in_out_stock_bbs');
    }
}
