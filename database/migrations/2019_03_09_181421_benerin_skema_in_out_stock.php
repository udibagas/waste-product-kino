<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class BenerinSkemaInOutStock extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('in_out_stock_bbs', function (Blueprint $table) {
            $table->dropColumn(['lokasi_terima', 'lokasi_terima_id']);
            $table->integer('location_id', false, true)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('in_out_stock_bbs', function (Blueprint $table) {
            $table->dropColumn('location_id');
            $table->string('lokasi_terima', 20)->nullable();
            $table->integer('lokasi_terima_id', false, true)->nullable();
        });
    }
}
