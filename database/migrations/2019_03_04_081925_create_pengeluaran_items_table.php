<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePengeluaranItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pengeluaran_items', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('pengeluaran_id', false, true);
            $table->integer('kategori_barang_id', false, true);
            $table->integer('qty', false, true);
            $table->string('eun', 10);
            $table->integer('timbangan_manual', false, true);
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
        Schema::dropIfExists('pengeluaran_items');
    }
}
