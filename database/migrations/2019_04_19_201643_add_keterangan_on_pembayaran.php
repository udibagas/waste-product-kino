<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddKeteranganOnPembayaran extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pembayarans', function (Blueprint $table) {
            $table->string('keterangan')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pembayarans', function (Blueprint $table) {
            $table->dropColumn('keterangan');
        });
    }
}
