<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePenerimaanItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('penerimaan_items', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('penerimaan_id', false, true);
            $table->integer('kategori_barang_id', false, true);
            $table->integer('qty_kirim', false, true);
            $table->integer('qty_terima', false, true);
            $table->string('eun', 10);
            $table->decimal('timbangan_manual_kirim', 38, 4);
            $table->decimal('timbangan_manual_terima', 18, 4);
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
        Schema::dropIfExists('penerimaan_items');
    }
}
