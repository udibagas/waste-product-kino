<?php

use Illuminate\Support\Facades\Route;
use App\PengajuanPenjualan;
use App\User;
use App\Penerimaan;
use Illuminate\Support\Facades\Mail;
use App\Mail\TestEmail;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/migrate', function () {
    return Artisan::call('migrate');
});

Route::get('testEmail', function() {
    Mail::to('bagas@lamsolusi.com')
        ->cc('udibagas@gmail.com')
        ->send(new TestEmail());
    return 'mail sent';
});

// AUTH
Route::get('login', 'Auth\LoginController@showLoginForm')->name('login');
Route::post('login', 'Auth\LoginController@login');
Route::post('logout', 'Auth\LoginController@logout')->name('logout');

// UNTUK DROPDOWN
Route::get('kategoriBarang/getList', 'KategoriBarangController@getList');
Route::get('location/getList', 'LocationController@getList');
Route::get('navigation/refresh', 'NavigationController@store'); // untuk generate link
Route::get('navigation', 'NavigationController@index');
Route::get('pembeli/getList', 'PembeliController@getList');
Route::get('pengajuanPenjualan/getList', 'PengajuanPenjualanController@getList');
Route::get('pengeluaran/getList', 'PengeluaranController@getList');
Route::get('user/getList', 'UserController@getList');
Route::get('stockWp/getList', 'StockWpController@getList');
Route::get('getSlocList', 'StockWpController@getSlocList');
Route::get('getMvtList', 'StockWpController@getMvtList');
Route::get('getMatList', 'StockWpController@getMatList');

// TRANSACTION
Route::resource('penerimaan', 'PenerimaanController')->only(['index', 'store', 'update', 'destroy']);
Route::get('pengajuanPenjualan/{pengajuanPenjualan}/approvalForm', 'PengajuanPenjualanController@approvalForm');
Route::get('pengajuanPenjualan/{pengajuanPenjualan}/getApprovalHistory', 'PengajuanPenjualanController@getApprovalHistory');
Route::put('pengajuanPenjualan/{pengajuanPenjualan}/approve', 'PengajuanPenjualanController@approve');
Route::delete('pengajuanPenjualanItemBb/{pengajuanPenjualanItemBb}', 'PengajuanPenjualanItemBbController@destroy');
Route::delete('pengajuanPenjualanItemWp/{pengajuanPenjualanItemWp}', 'PengajuanPenjualanItemWpController@destroy');
Route::get('pengajuanPenjualan/getLastRecord', 'PengajuanPenjualanController@getLastRecord');
Route::resource('pengajuanPenjualan', 'PengajuanPenjualanController')->only(['index', 'edit', 'show', 'store', 'update', 'destroy']);
Route::get('pengeluaran/getLastRecord', 'PengeluaranController@getLastRecord');
Route::resource('pengeluaran', 'PengeluaranController')->only(['index', 'store', 'update', 'destroy']);
Route::delete('pengeluaranItem/{pengeluaranItem}', 'PengeluaranItemController@destroy');
Route::get('penjualan/{penjualan}/printSlip', 'PenjualanController@printSlip');
Route::get('penjualan/getLastRecord','PenjualanController@getLastRecord');
Route::resource('penjualan','PenjualanController')->only(['index', 'store', 'update', 'destroy']);
Route::resource('pembayaran','PembayaranController')->only(['store']);

// REPORT
Route::get('inOutStockBb', 'InOutStockBbController@index');
Route::get('inOutStockWp', 'InOutStockWpController@index');
Route::get('report/bb', 'ReportController@bb');
Route::get('report/wp', 'ReportController@wp');
Route::get('stockBb/getStock', 'StockBbController@getStock');
Route::get('stockBb/getStockList', 'StockBbController@getStockList');
Route::get('stockBb', 'StockBbController@index');

// MASTER DATA
Route::resource('kategoriBarang', 'KategoriBarangController')->only(['index', 'store', 'update', 'destroy']);
Route::resource('konversiBerat', 'KonversiBeratController')->only(['index', 'store']);
Route::resource('location', 'LocationController')->only(['index', 'store', 'update', 'destroy']);
Route::resource('pembeli', 'PembeliController')->only(['index', 'store', 'update', 'destroy']);
Route::resource('skemaApprovalPenjualan', 'SkemaApprovalPenjualanController')->only(['index', 'store', 'update', 'destroy']);
Route::resource('stockWp', 'StockWpController')->only(['index', 'store', 'update', 'destroy']);
Route::resource('user', 'UserController')->only(['index', 'show', 'store', 'update', 'destroy']);

// TESTING ONLY
Route::get('jajal', function() {
    return auth()->user()->rights;
});

Route::get('/emailApproval', function() {
    return new App\Mail\ApprovalRequest(PengajuanPenjualan::first(), 1, User::find(1));
});

Route::get('/emailPenerimaan', function() {
    $penerimaan = Penerimaan::find(3015);
    return new App\Mail\ReceiptNotification($penerimaan);
});

// untuk check auth sebelum route vue
Route::get('checkAuth', 'AppController@checkAuth');

// ROOT & SPA
Route::get('/', 'AppController@index');
Route::get('/app/{any}', 'AppController@index')->where('any', '.*');
