<?php

use App\Http\Controllers\Backend\BarangController;
use App\Http\Controllers\Backend\KategoriController;
use App\Http\Controllers\Backend\LelangController;
use App\Http\Controllers\Backend\PemenangController;
use App\Http\Controllers\Backend\ReviewBidController;
use App\Http\Controllers\Backend\StrukController as BackendStrukController;
use App\Http\Controllers\StrukController;
use App\Http\Controllers\BackendController;
use App\Http\Controllers\FrontController;
use App\Http\Controllers\KodeController;
use App\Http\Controllers\MidtransController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\SingleController;
use App\Http\Controllers\VerifikasiController;
use App\Http\Middleware\IsAdmin;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
Route::get('/', [FrontController::class, 'index'])->name('awal');
Route::get('/backend/struk/bayar/{kode}', [StrukController::class, 'bayar'])->name('backend.struk.bayar');
Route::get('/backend/struk/status-paid/{kode}', [StrukController::class, 'setPaid']);
Route::post('/struk/check-status/{kode}', [StrukController::class, 'checkStatus'])->name('check.status');
Route::get('/midtrans/redirect', [MidtransController::class, 'handleRedirect'])->name('midtrans.redirect');
Route::resource('verifikasi', VerifikasiController::class);
Route::resource('daftar', RegisterController::class);
Route::get('/search', [FrontController::class, 'search'])->name('search');


Route::get('struk/{kodestruk}', [StrukController::class, 'struk'])->name('struk.detail');
Route::resource('struk', SingleController::class);
Route::get('/', [FrontController::class, 'index'])->name('home');

Auth::routes();
Route::get('kategori/{slug}', [FrontController::class, 'show'])->name('kategori.show');
Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home.user');
Route::resource('lelang', SingleController::class);

Route::group(['prefix'=>'admin','as' => 'backend.','middleware'=>['auth', IsAdmin::class]], function(){
    Route::get('/', [BackendController::class, 'index'])->name('home');
    Route::resource('kategori', KategoriController::class);
    Route::resource('barang', BarangController::class);
    Route::resource('lelang', LelangController::class);
    Route::resource('bid', ReviewBidController::class);
    Route::get('pemenang', [PemenangController::class, 'index'])->name('pemenang');
    Route::resource('struk', BackendStrukController::class);
});