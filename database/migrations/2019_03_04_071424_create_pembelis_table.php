<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePembelisTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pembelis', function (Blueprint $table) {
            $table->increments('id');
            $table->string('nama', 30);
            $table->string('kontak', 60)->nullable();
            $table->string('alamat')->nullable();
            $table->string('bank', 30)->nullable();
            $table->string('nomor_rekening', 50)->nullable();
            $table->string('pemegang_rekening', 50)->nullable();
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
        Schema::dropIfExists('pembelis');
    }
}
