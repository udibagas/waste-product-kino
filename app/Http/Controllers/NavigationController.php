<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Menu;

class NavigationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        return Menu::where('parent_id', 0)->get();
    }

    public function store()
    {
        Menu::truncate();

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
                'url' => '/pengajuanPenjualanBb',
                'parent_id' => 2,
                'order' => 3
            ],
            [
                'icon' => 'money-bill',
                'label' => 'Penjualan',
                'url' => '/penjualanBb',
                'parent_id' => 2,
                'order' => 4
            ],
            [
                'icon' => 'exchange-alt',
                'label' => 'In Out Stock',
                'url' => '/inOutStockBb',
                'parent_id' => 2,
                'order' => 5
            ],
            [
                'icon' => 'chart-bar',
                'label' => 'Laporan',
                'url' => '/reportBb',
                'parent_id' => 2,
                'order' => 6
            ],
            // Sub Menu Waste Product (ID : 3)
            [
                'icon' => 'warehouse',
                'label' => 'Stock',
                'url' => '/stockWp',
                'parent_id' => 3,
                'order' => 0
            ],
            [
                'icon' => 'file-contract',
                'label' => 'Pengajuan Penjualan',
                'url' => '/pengajuanPenjualanWp',
                'parent_id' => 3,
                'order' => 1
            ],
            [
                'icon' => 'money-bill',
                'label' => 'Penjualan',
                'url' => '/penjualanWp',
                'parent_id' => 3,
                'order' => 2
            ],
            [
                'icon' => 'chart-bar',
                'label' => 'Laporan',
                'url' => '/reportWp',
                'parent_id' => 3,
                'order' => 3
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
}
