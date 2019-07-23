<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddForeignKeyOnPengajuanPenualans extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pengajuan_penjualans', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('location_id')->references('id')->on('locations');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pengajuan_penjualans', function (Blueprint $table) {
            $table->dropForeign('pengajuan_penjualans_user_id_foreign');
            $table->dropForeign('pengajuan_penjualans_location_id_foreign');
        });
    }
}
