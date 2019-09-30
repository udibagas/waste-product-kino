<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldsOnPenjualanWpItems extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('penjualan_item_wps', function (Blueprint $table) {
            $table->decimal('terjual', 38, 4)->nullable();
            $table->decimal('berat_dijual', 38, 4)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('penjualan_item_wps', function (Blueprint $table) {
            $table->dropColumn(['berat_dijual', 'terjual']);
        });
    }
}
