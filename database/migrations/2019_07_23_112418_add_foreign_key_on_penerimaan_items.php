<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddForeignKeyOnPenerimaanItems extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('penerimaan_items', function (Blueprint $table) {
            $table->foreign('penerimaan_id')
                ->references('id')->on('penerimaans')
                ->onDelete('cascade');
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
            $table->dropForeign('penerimaan_items_penerimaan_id_foreign');
        });
    }
}
