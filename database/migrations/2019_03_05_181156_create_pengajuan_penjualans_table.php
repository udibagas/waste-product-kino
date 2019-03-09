<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePengajuanPenjualansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pengajuan_penjualans', function (Blueprint $table) {
            $table->increments('id');
            $table->date('tanggal');
            $table->string('no_aju', 100);
            $table->string('plant', 10);
            $table->date('period_from');
            $table->date('period_to');
            $table->string('jenis', 10);
            $table->string('mvt_type')->nullable();
            $table->string('sloc')->nullable();
            $table->integer('user_id', false, true);
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
        Schema::dropIfExists('pengajuan_penjualans');
    }
}
