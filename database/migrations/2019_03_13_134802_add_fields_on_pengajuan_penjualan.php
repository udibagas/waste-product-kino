<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldsOnPengajuanPenjualan extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pengajuan_penjualans', function (Blueprint $table) {
            $table->tinyInteger('approval1_status')->default(0);
            $table->dateTime('approval1_time')->nullable();
            $table->integer('approval1_by', false, true)->nullable();
            $table->tinyInteger('approval2_status')->default(0);
            $table->dateTime('approval2_time')->nullable();
            $table->integer('approval2_by', false, true)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pengajuan_penjualans', function (Blueprint $table) {
            $table->dropColumn([
                'approval1_status', 'approval1_time', 'approval1_by',
                'approval2_status', 'approval2_time', 'approval2_by'
            ]);
        });
    }
}
