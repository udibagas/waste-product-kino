<?php

use Illuminate\Support\Facades\Route;
use App\PengajuanPenjualan;
use App\User;

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

// TRANSACTION
Route::resource('penerimaan', 'PenerimaanController')->only(['index', 'store', 'update', 'destroy']);
Route::put( 'pengajuanPenjualan/{pengajuanPenjualan}/approve', 'PengajuanPenjualanController@approve');
Route::resource('pengajuanPenjualan', 'PengajuanPenjualanController')->only(['index', 'edit', 'show', 'store', 'update', 'destroy']);
Route::resource('pengeluaran', 'PengeluaranController')->only(['index', 'store', 'update', 'destroy']);
Route::resource('pengeluaranItem', 'PengeluaranItemController')->only(['destroy']);
Route::resource('penjualan','PenjualanController')->only(['index', 'store', 'update', 'destroy']);

// REPORT
Route::get('inOutStockBb', 'InOutStockBbController@index');
Route::get('report/bb', 'ReportController@bb');
Route::get('stockBb/getStock', 'StockBbController@getStock');
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

// untuk check auth sebelum route vue
Route::get('checkAuth', 'AppController@checkAuth');

// ROOT & SPA
Route::get('/', 'AppController@index');
Route::get('/app/{any}', 'AppController@index')->where('any', '.*');