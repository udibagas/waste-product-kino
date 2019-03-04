<?php

use Illuminate\Support\Facades\Route;

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

Route::get('login', 'Auth\LoginController@showLoginForm')->name('login');
Route::post('login', 'Auth\LoginController@login');
Route::post('logout', 'Auth\LoginController@logout')->name('logout');

Route::resource('user', 'UserController')->only(['index', 'store', 'update', 'destroy']);
Route::resource('pembeli', 'PembeliController')->only(['index', 'store', 'update', 'destroy']);
Route::resource('kategoriBarang', 'KategoriBarangController')->only(['index', 'store', 'update', 'destroy']);
Route::resource('pengeluaran', 'PengeluaranController')->only(['index', 'show', 'store', 'update', 'destroy']);
Route::resource('penerimaan', 'PenerimaanController')->only(['index', 'show', 'store', 'update', 'destroy']);
Route::get('navigation', 'NavigationController@index');

Route::get('/', 'AppController@index');
Route::get('/app/{any}', 'AppController@index')->where('any', '.*');

