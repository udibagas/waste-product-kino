<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInOutStockWpsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('in_out_stock_wps', function (Blueprint $table) {
            $table->increments('id');
            $table->date('tanggal');
            $table->string('posting_date')->nullable();
            $table->string('plant');
            $table->string('mat_doc')->nullable();
            $table->string('mat')->nullable();
            $table->string('batch')->nullable();
            $table->string('doc_date')->nullable();
            $table->string('entry_date')->nullable();
            $table->string('time')->nullable();
            $table->string('bun')->nullable();
            $table->string('location_id', false, true)->nullable();
            $table->string('sloc')->nullable();
            $table->string('mvt')->nullable();
            $table->integer('qty_in')->default(0);
            $table->integer('qty_out')->default(0);
            $table->decimal('stock_in', 38, 4)->default(0);
            $table->decimal('stock_out', 38, 4)->default(0);
            $table->string('material');
            $table->string('material_description');
            $table->string('no_aju')->nullable();
            $table->string('no_sj')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('in_out_stock_wps');
    }
}
