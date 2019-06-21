<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeBeratToDecimal extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Schema::table('pengajuan_penjualan_item_wps', function (Blueprint $table) {
        //     $table->dropColumn('berat');
        // });

        Schema::table('pengajuan_penjualan_item_wps', function (Blueprint $table) {
            // $table->decimal('berat', 38, 4);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pengajuan_penjualan_item_wps', function (Blueprint $table) {
            // $table->decimal('berat', 38, 4);
        });
    }
}
