<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateKonversiBeratsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('konversi_berats', function (Blueprint $table) {
            $table->increments('id');
            $table->string('kategori_jual')->nullable();
            $table->string('finished_good')->nullable();
            $table->string('material_id');
            $table->string('material_description');
            $table->decimal('berat', 38, 4);
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
        Schema::dropIfExists('konversi_berats');
    }
}
