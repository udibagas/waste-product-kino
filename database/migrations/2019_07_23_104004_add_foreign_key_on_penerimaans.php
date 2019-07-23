<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddForeignKeyOnPenerimaans extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('penerimaans', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('lokasi_asal_id')->references('id')->on('locations');
            $table->foreign('lokasi_terima_id')->references('id')->on('locations');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('penerimaans', function (Blueprint $table) {
            $table->dropForeign('penerimaans_user_id_foreign');
            $table->dropForeign('penerimaans_lokasi_asal_id_foreign');
            $table->dropForeign('penerimaans_lokasi_terima_id_foreign');
        });
    }
}
