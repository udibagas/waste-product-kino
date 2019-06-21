<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePenjualanItemWpsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('penjualan_item_wps', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('penjualan_id', false, true);
            $table->string('material_id');
            $table->string('material_description');
            $table->integer('price_per_unit', false, true);
            $table->integer('value', false, true);
            $table->decimal('berat', 38, 4);
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
        Schema::dropIfExists('penjualan_item_wps');
    }
}
