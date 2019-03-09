<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePengajuanPenjualanItemWpsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pengajuan_penjualan_item_wps', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('kategori_barang_id', false, true);
            $table->string('material_id');
            $table->string('divisi', 30);
            $table->string('unit', 10);
            $table->decimal('qty_reject');
            $table->integer('price_per_unit', false, true);
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
        Schema::dropIfExists('pengajuan_penjualan_item_wps');
    }
}
