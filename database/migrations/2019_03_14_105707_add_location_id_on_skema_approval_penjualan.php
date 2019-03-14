<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddLocationIdOnSkemaApprovalPenjualan extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('skema_approval_penjualans', function (Blueprint $table) {
            $table->dropColumn(['lokasi', 'plant']);
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
        Schema::table('skema_approval_penjualans', function (Blueprint $table) {
            $table->dropColumn(['location_id']);
            $table->string('lokasi', 20)->nullable();
            $table->string('plant', 20)->nullable();
        });
    }
}
