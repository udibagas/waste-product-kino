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
                'icon' => 'recycle',
                'children' => [
                    [
                        'path' => '/stockBb',
                        'label' => 'Stock',
                        'icon' => 'warehouse'
                    ],
                    [
                        'path' => '/pengeluaran',
                        'label' => 'Pengeluaran',
                        'icon' => 'share'
                    ],
                    [
                        'path' => '/penerimaan',
                        'label' => 'Penerimaan',
                        'icon' => 'reply'
                    ],
                    [
                        'path' => '/pengajuanPenjualan',
                        'label' => 'Pengajuan Penjualan',
                        'icon' => 'file-contract'
                    ],
                    [
                        'path' => '/penjualan',
                        'label' => 'Penjualan',
                        'icon' => 'money-bill'
                    ],
                    [
                        'path' => '/pembayaran',
                        'label' => 'Pembayaran',
                        'icon' => 'file-invoice-dollar'
                    ],
                    [
                        'path' => '/inOutStockBb',
                        'label' => 'In Out Stock',
                        'icon' => 'exchange-alt'
                    ],
                    [
                        'path' => '/reportBb',
                        'label' => 'Laporan',
                        'icon' => 'chart-bar'
                    ],
                ]
            ],
            [
                'path' => '/wasteProduct',
                'label' => 'Waste Product',
                'icon' => 'boxes',
                'children' => [
                    [
                        'path' => '/stockWp',
                        'label' => 'Stock',
                        'icon' => 'warehouse'
                    ],
                    [
                        'path' => '/pengajuanPenjualan',
                        'label' => 'Pengajuan Penjualan',
                        'icon' => 'file-contract'
                    ],
                    [
                        'path' => '/penjualan',
                        'label' => 'Penjualan',
                        'icon' => 'money-bill'
                    ],
                    [
                        'path' => '/pembayaran',
                        'label' => 'Pembayaran',
                        'icon' => 'file-invoice-dollar'
                    ],
                    [
                        'path' => '/reportWp',
                        'label' => 'Laporan',
                        'icon' => 'chart-bar'
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
                        'path' => '/location',
                        'label' => 'Location',
                        'icon' => 'city'
                    ],
                    [
                        'path' => '/pembeli',
                        'label' => 'Pembeli',
                        'icon' => 'users'
                    ],
                    [
                        'path' => '/skemaApprovalPenjualan',
                        'label' => 'Skema Approval Penjualan',
                        'icon' => 'project-diagram'
                    ],
                    [
                        'path' => '/konversiBerat',
                        'label' => 'Konversi Berat',
                        'icon' => 'balance-scale'
                    ],
                    [
                        'path' => '/user',
                        'label' => 'User',
                        'icon' => 'user-lock'
                    ],
                ]
            ],
            // [
            //     'path' => '/setting',
            //     'label' => 'Setting',
            //     'icon' => 'sliders-h',
            //     'children' => [
                    
            //     ]
            // ],
        ];
    }
}
