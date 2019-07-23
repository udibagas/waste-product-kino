<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddForeignKeyOnSkemaApprovalPenjualan extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('skema_approval_penjualans', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('location_id')->references('id')->on('locations');
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
            $table->dropForeign('skema_approval_penjualans_user_id_foreign');
            $table->dropForeign('skema_approval_penjualans_location_id_foreign');
        });
    }
}
