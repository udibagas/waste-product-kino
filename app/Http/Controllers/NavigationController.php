<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NavigationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        return [
            [
                'path' => '/home',
                'label' => 'Home',
                'icon' => 'home'
            ],
            [
                'path' => '/barangBekas',
                'label' => 'Barang Bekas',
                'icon' => 'boxes',
                'children' => [
                    [
                        'path' => '/user',
                        'label' => 'Stock',
                        'icon' => 'user-lock'
                    ],
                    [
                        'path' => '/pengeluaran',
                        'label' => 'Pengeluaran',
                        'icon' => 'user-lock'
                    ],
                    [
                        'path' => '/penerimaan',
                        'label' => 'Penerimaan',
                        'icon' => 'user-lock'
                    ],
                    [
                        'path' => '/pengajuanPenjualan',
                        'label' => 'Pengajuan Penjualan',
                        'icon' => 'user-lock'
                    ],
                    [
                        'path' => '/penjualan',
                        'label' => 'Penjualan',
                        'icon' => 'user-lock'
                    ],
                    [
                        'path' => '/pembayaran',
                        'label' => 'Pembayaran',
                        'icon' => 'user-lock'
                    ],
                    [
                        'path' => '/user',
                        'label' => 'Laporan',
                        'icon' => 'user-lock'
                    ],
                ]
            ],
            [
                'path' => '/wasteProduct',
                'label' => 'Waste Product',
                'icon' => 'boxes',
                'children' => [
                    [
                        'path' => '/user',
                        'label' => 'Stock',
                        'icon' => 'user-lock'
                    ],
                    [
                        'path' => '/user',
                        'label' => 'Pengajuan Penjualan',
                        'icon' => 'user-lock'
                    ],
                    [
                        'path' => '/user',
                        'label' => 'Penjualan',
                        'icon' => 'user-lock'
                    ],
                    [
                        'path' => '/user',
                        'label' => 'Pembayaran',
                        'icon' => 'user-lock'
                    ],
                    [
                        'path' => '/user',
                        'label' => 'Laporan',
                        'icon' => 'user-lock'
                    ],
                ]
            ],
            [
                'path' => '/master',
                'label' => 'Master Data',
                'icon' => 'database',
                'children' => [
                    [
                        'path' => '/kategoriBarang',
                        'label' => 'Kategori Barang',
                        'icon' => 'tags'
                    ],
                    [
                        'path' => '/pembeli',
                        'label' => 'Pembeli',
                        'icon' => 'users'
                    ],
                    [
                        'path' => '/user',
                        'label' => 'User',
                        'icon' => 'user-lock'
                    ],
                ]
            ],
        ];
    }
}
