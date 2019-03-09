<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePengajuanPenjualanItemBbsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pengajuan_penjualan_item_bbs', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('kategori_barang_id', false, true);
            $table->decimal('jumlah');
            $table->decimal('jumlah_terima');
            $table->string('eun', 10);
            $table->decimal('timbangan_manual');
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
        Schema::dropIfExists('pengajuan_penjualan_item_bbs');
    }
}
