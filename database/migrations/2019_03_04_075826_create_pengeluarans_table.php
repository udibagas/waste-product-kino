<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePengeluaransTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pengeluarans', function (Blueprint $table) {
            $table->increments('id');
            $table->string('no_sj', 50)->nullable();
            $table->date('tanggal')->nullable();
            $table->string('penerima', 30)->nullable();
            $table->string('lokasi_asal', 30)->nullable();
            $table->string('lokasi_terima', 30)->nullable();
            $table->integer('user_id', false, true);
            $table->decimal('jembatan_timbang', 38, 4);
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
        Schema::dropIfExists('pengeluarans');
    }
}
