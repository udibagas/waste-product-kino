<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePengajuanPenjualanApprovalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pengajuan_penjualan_approvals', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('pengajuan_penjualan_id', false, true);
            $table->integer('user_id', false, true);
            $table->tinyInteger('status');
            $table->tinyInteger('level');
            $table->string('note')->nullable();
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
        Schema::dropIfExists('pengajuan_penjualan_approvals');
    }
}
