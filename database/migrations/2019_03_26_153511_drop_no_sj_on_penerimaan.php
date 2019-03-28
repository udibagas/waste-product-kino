<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropNoSjOnPenerimaan extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('penerimaans', function (Blueprint $table) {
            $table->dropColumn('no_sj');
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
            $table->string('no_sj', 50);
        });
    }
}
