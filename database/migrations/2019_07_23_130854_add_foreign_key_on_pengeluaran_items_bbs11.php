<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddForeignKeyOnPengeluaranItemsBbs11 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Schema::table('pengeluaran_items', function (Blueprint $table) {
        //     $table->foreign('pengeluaran_id')
        //         ->references('id')->on('pengeluarans')
        //         ->onDelete('cascade');
        // });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Schema::table('pengeluaran_items', function (Blueprint $table) {
        //     $table->dropForeign('pengeluaran_items_pengeluaran_id_foreign');
        // });
    }
}
