<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateKategoriBarangsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('kategori_barangs', function (Blueprint $table) {
            $table->increments('id');
            $table->string('jenis', 2);
            $table->string('kode', 10);
            $table->string('nama', 30);
            $table->string('unit', 10);
            $table->integer('harga', false, true)->default(0);
            $table->boolean('status')->default(0);
            $table->string('created_by', 30)->nullable();
            $table->string('updated_by', 30)->nullable();
            $table->string('approved_by', 30)->nullable();
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
        Schema::dropIfExists('kategori_barangs');
    }
}
