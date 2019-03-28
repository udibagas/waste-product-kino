<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStockWpsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stock_wps', function (Blueprint $table) {
            $table->increments('id');
            $table->string('plant', 10);
            $table->string('sloc', 10);
            $table->string('mvt', 10);
            $table->string('posting_date', 20);
            $table->string('mat_doc', 50);
            $table->string('material', 50);
            $table->string('material_description');
            $table->string('doc_date', 20);
            $table->string('entry_date', 20);
            $table->string('time', 20);
            $table->string('bun', 20);
            $table->decimal('quantity', 38, 4)->default(0);
            $table->decimal('stock', 38, 4)->default(0);
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
        Schema::dropIfExists('stock_wps');
    }
}
