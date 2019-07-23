<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddForeignKeyOnPenjualans extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('penjualans', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('location_id')->references('id')->on('locations');
            $table->foreign('pembeli_id')->references('id')->on('pembelis');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('penjualans', function (Blueprint $table) {
            $table->dropForeign('penjualans_user_id_foreign');
            $table->dropForeign('penjualans_location_id_foreign');
            $table->dropForeign('penjualans_pembeli_id_foreign');
        });
    }
}
