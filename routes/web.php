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

// TRANSACTION
Route::resource('penerimaan', 'PenerimaanController')->only(['index', 'store', 'update', 'destroy']);
Route::post( 'pengajuanPenjualan/{pengajuanPenjualan}/approve', 'PengajuanPenjualanController@approve');
Route::resource('pengajuanPenjualan', 'PengajuanPenjualanController')->only(['index', 'edit', 'show', 'store', 'update', 'destroy']);
Route::resource('pengeluaran', 'PengeluaranController')->only(['index', 'store', 'update', 'destroy']);
Route::resource('pengeluaranItem', 'PengeluaranItemController')->only(['destroy']);

// REPORT
Route::get('inOutStockBb', 'InOutStockBbController@index');
Route::get('report/bb', 'ReportController@bb');
Route::get('stockBb', 'StockBbController@index');

// UNTUK DROPDOWN
Route::get('kategoriBarang/getList', 'KategoriBarangController@getList');
Route::get('location/getList', 'LocationController@getList');
Route::get('navigation/refresh', 'NavigationController@store');
Route::get('navigation', 'NavigationController@index');
Route::get('pengeluaran/getList', 'PengeluaranController@getList');
Route::get('user/getList', 'UserController@getList');

// MASTER DATA
Route::resource('kategoriBarang', 'KategoriBarangController')->only(['index', 'store', 'update', 'destroy']);
Route::resource('konversiBerat', 'KonversiBeratController')->only(['index', 'store']);
Route::resource('location', 'LocationController')->only(['index', 'store', 'update', 'destroy']);
Route::resource('pembeli', 'PembeliController')->only(['index', 'store', 'update', 'destroy']);
Route::resource('skemaApprovalPenjualan', 'SkemaApprovalPenjualanController')->only(['index', 'store', 'update', 'destroy']);
Route::resource('user', 'UserController')->only(['index', 'store', 'update', 'destroy']);

// TESTING ONLY
Route::get('/emailApproval', function() {
    return new App\Mail\ApprovalRequest(PengajuanPenjualan::first(), 1, User::find(1));
});

// ROOT & SPA
Route::get('/', 'AppController@index');
Route::get('/app/{any}', 'AppController@index')->where('any', '.*');

