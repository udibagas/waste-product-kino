<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldOnStockWp extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('stock_wps', function (Blueprint $table) {
            $table->string('mat', 10)->nullable();
            $table->string('text')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('stock_wps', function (Blueprint $table) {
            $table->dropColumn(['mat', 'text']);
        });
    }
}
