<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Menu;

class CreateMenusesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('menus', function (Blueprint $table) {
            $table->increments('id');
            $table->string('icon', 20)->nullable();
            $table->string('label', 30);
            $table->string('url', 100);
            $table->integer('parent_id', false, true)->default(0);
            $table->integer('order', false, true)->nullable(0);
            $table->timestamps();
        });

        Menu::insert([
            // ID : 1
            [
                'icon' => 'home',
                'label' => 'Home',
                'url' => '/home',
                'parent_id' => 0,
                'order' => 0
            ],
            // ID : 2
            [
                'icon' => 'recycle',
                'label' => 'Barang Bekas',
                'url' => '#',
                'parent_id' => 0,
                'order' => 1
            ],
            // ID : 3
            [
                'icon' => 'boxes',
                'label' => 'Waste Product',
                'url' => '#',
                'parent_id' => 0,
                'order' => 2
            ],
            // ID : 4
            [
                'icon' => 'database',
                'label' => 'Master Data',
                'url' => '#',
                'parent_id' => 0,
                'order' => 3
            ],
            // Sub Menu Barang Bekas (ID : 2)
            [
                'icon' => 'warehouse',
                'label' => 'Stock',
                'url' => '/stockBb',
                'parent_id' => 2,
                'order' => 0
            ],
            [
                'icon' => 'share',
                'label' => 'Pengeluaran',
                'url' => '/pengeluaran',
                'parent_id' => 2,
                'order' => 1
            ],
            [
                'icon' => 'reply',
                'label' => 'Penerimaan',
                'url' => '/penerimaan',
                'parent_id' => 2,
                'order' => 2
            ],
            [
                'icon' => 'file-contract',
                'label' => 'Pengajuan Penjualan',
                'url' => '/pengajuanPenjualan',
                'parent_id' => 2,
                'order' => 3
            ],
            [
                'icon' => 'money-bill',
                'label' => 'Penjualan',
                'url' => '/penjualan',
                'parent_id' => 2,
                'order' => 4
            ],
            [
                'icon' => 'file-invoice-dollar',
                'label' => 'Pembayaran',
                'url' => '/pembayaran',
                'parent_id' => 2,
                'order' => 5
            ],
            [
                'icon' => 'exchange-alt',
                'label' => 'In Out Stock',
                'url' => '/inOutStockBb',
                'parent_id' => 2,
                'order' => 6
            ],
            [
                'icon' => 'chart-bar',
                'label' => 'Laporan',
                'url' => '/reportBb',
                'parent_id' => 2,
                'order' => 7
            ],
            // Sub Menu Waste Product (ID : 3)
            [
                'icon' => 'warehouse',
                'label' => 'Stock',
                'url' => '/stockBb',
                'parent_id' => 3,
                'order' => 0
            ],
            [
                'icon' => 'file-contract',
                'label' => 'Pengajuan Penjualan',
                'url' => '/pengajuanPenjualan',
                'parent_id' => 3,
                'order' => 1
            ],
            [
                'icon' => 'money-bill',
                'label' => 'Penjualan',
                'url' => '/penjualan',
                'parent_id' => 3,
                'order' => 2
            ],
            [
                'icon' => 'file-invoice-dollar',
                'label' => 'Pembayaran',
                'url' => '/pembayaran',
                'parent_id' => 3,
                'order' => 3
            ],
            [
                'icon' => 'chart-bar',
                'label' => 'Laporan',
                'url' => '/reportWp',
                'parent_id' => 3,
                'order' => 4
            ],
            // Sub Menu Master Data (ID : 4)
            [
                'url' => '/kategoriBarang',
                'label' => 'Kategori Barang',
                'icon' => 'tags',
                'parent_id' => 4,
                'order' => 0
            ],
            [
                'url' => '/location',
                'label' => 'Location',
                'icon' => 'city',
                'parent_id' => 4,
                'order' => 1
            ],
            [
                'url' => '/pembeli',
                'label' => 'Pembeli',
                'icon' => 'users',
                'parent_id' => 4,
                'order' => 2
            ],
            [
                'url' => '/skemaApprovalPenjualan',
                'label' => 'Skema Approval Penjualan',
                'icon' => 'project-diagram',
                'parent_id' => 4,
                'order' => 3
            ],
            [
                'url' => '/konversiBerat',
                'label' => 'Konversi Berat',
                'icon' => 'balance-scale',
                'parent_id' => 4,
                'order' => 4
            ],
            [
                'url' => '/user',
                'label' => 'User',
                'icon' => 'user-lock',
                'parent_id' => 4,
                'order' => 5
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('menus');
    }
}
