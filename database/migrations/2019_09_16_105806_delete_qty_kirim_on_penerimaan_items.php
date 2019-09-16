<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DeleteQtyKirimOnPenerimaanItems extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('penerimaan_items', function (Blueprint $table) {
            $table->dropColumn(['qty_kirim', 'qty_terima']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('penerimaan_items', function (Blueprint $table) {
            $table->integer('qty_kirim')->default(0);
            $table->integer('qty_terima')->default(0);
        });
    }
}
