<?php

use App\Http\Controllers\Authenticated\AdminController;
use App\Http\Controllers\Authenticated\DashboardController;
use App\Http\Controllers\Guest\AuthController;
use App\Http\Controllers\PjKabupaten\KabupatenLuasTanamController;
use App\Http\Controllers\PjKabupaten\KabupatenPompaDimanfaatkanController;
use App\Http\Controllers\PjKabupaten\KabupatenPompaDiterimaController;
use App\Http\Controllers\PjKabupaten\KabupatenPompaUsulanController;
use App\Http\Controllers\PjKecamatan\KecamatanLuasTanamController;
use App\Http\Controllers\PjKecamatan\KecamatanPompaDimanfaatkanController;
use App\Http\Controllers\PjKecamatan\KecamatanPompaDiterimaController;
use App\Http\Controllers\PjKecamatan\KecamatanPompaUsulanController;
use App\Http\Controllers\PjKecamatan\PoktanController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () { return redirect()->route('auth.login.view'); });

Route::prefix('/auth')->group(function () {
    Route::middleware('guest')->group(function () {
        Route::get('/login', [AuthController::class, 'loginView'])->name('auth.login.view');
        Route::post('/login', [AuthController::class, 'login'])->name('auth.login');
    });
    Route::get('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('auth.logout');
});

Route::middleware('auth')->group(function () {
    Route::middleware('nonactive')->group(function () {
        Route::get('/aktifasi', [DashboardController::class, 'activation'])->name('activation');
        Route::post('/aktifasi', [DashboardController::class, 'activate'])->name('activate');
    });
    
    Route::middleware('active')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        
        Route::middleware('access:admin')->group(function () {
            Route::get('/daftar-pj', [AdminController::class, 'userList'])->name('admin.daftar_pj');
            Route::post('/tambah-pj', [AdminController::class, 'createUser'])->name('admin.tambah_pj');
            Route::put('/edit-pj/{id}', [AdminController::class, 'editUser'])->name('admin.edit_pj');
            Route::delete('/hapus-pj/{id}', [AdminController::class, 'deleteUser'])->name('admin.hapus_pj');
        });
        
        Route::middleware('access:pj_kecamatan')->group(function () {
            Route::post('/input/poktan/store', [PoktanController::class, 'store'])->name('kecamatan.poktan.store');
            Route::get('/input/usulan', [KecamatanPompaUsulanController::class, 'index'])->name('kecamatan.usulan');
            Route::get('/input/usulan/create', [KecamatanPompaUsulanController::class, 'create'])->name('kecamatan.usulan.create');
            Route::post('/input/usulan/store', [KecamatanPompaUsulanController::class, 'store'])->name('kecamatan.usulan.store');
            Route::put('/input/usulan/{id}/update', [KecamatanPompaUsulanController::class, 'update'])->name('kecamatan.usulan.update');
            Route::delete('/input/usulan/{id}/destroy', [KecamatanPompaUsulanController::class, 'destroy'])->name('kecamatan.usulan.destroy');
            Route::get('/input/diterima', [KecamatanPompaDiterimaController::class, 'index'])->name('kecamatan.diterima');
            Route::get('/input/diterima/create', [KecamatanPompaDiterimaController::class, 'create'])->name('kecamatan.diterima.create');
            Route::post('/input/diterima/store', [KecamatanPompaDiterimaController::class, 'store'])->name('kecamatan.diterima.store');
            Route::put('/input/diterima/{id}/update', [KecamatanPompaDiterimaController::class, 'update'])->name('kecamatan.diterima.update');
            Route::delete('/input/diterima/{id}/destroy', [KecamatanPompaDiterimaController::class, 'destroy'])->name('kecamatan.diterima.destroy');
            Route::get('/input/dimanfaatkan', [KecamatanPompaDimanfaatkanController::class, 'index'])->name('kecamatan.dimanfaatkan');
            Route::get('/input/dimanfaatkan/create', [KecamatanPompaDimanfaatkanController::class, 'create'])->name('kecamatan.dimanfaatkan.create');
            Route::post('/input/dimanfaatkan/store', [KecamatanPompaDimanfaatkanController::class, 'store'])->name('kecamatan.dimanfaatkan.store');
            Route::put('/input/dimanfaatkan/{id}/update', [KecamatanPompaDimanfaatkanController::class, 'update'])->name('kecamatan.dimanfaatkan.update');
            Route::delete('/input/dimanfaatkan/{id}/destroy', [KecamatanPompaDimanfaatkanController::class, 'destroy'])->name('kecamatan.dimanfaatkan.destroy');
            Route::get('/input/luas-tanam', [KecamatanLuasTanamController::class, 'index'])->name('kecamatan.luas_tanam');
            Route::get('/input/luas-tanam/create', [KecamatanLuasTanamController::class, 'create'])->name('kecamatan.luas_tanam.create');
            Route::post('/input/luas-tanam/store', [KecamatanLuasTanamController::class, 'store'])->name('kecamatan.luas_tanam.store');
            Route::put('/input/luas-tanam/{id}/update', [KecamatanLuasTanamController::class, 'update'])->name('kecamatan.luas_tanam.update');
            Route::delete('/input/luas-tanam/{id}/destroy', [KecamatanLuasTanamController::class, 'destroy'])->name('kecamatan.luas_tanam.destroy');
        });
        
        Route::middleware('access:pj_kabupaten')->group(function () {
            Route::get('/verifikasi/usulan', [KabupatenPompaUsulanController::class, 'index'])->name('kabupaten.usulan');
            Route::put('/verifikasi/usulan/{id}/update', [KabupatenPompaUsulanController::class, 'update'])->name('kabupaten.usulan.update');
            Route::put('/verifikasi/usulan/{id}/approve', [KabupatenPompaUsulanController::class, 'approve'])->name('kabupaten.usulan.approve');
            Route::put('/verifikasi/usulan/{id}/deny', [KabupatenPompaUsulanController::class, 'deny'])->name('kabupaten.usulan.deny');
            Route::get('/verifikasi/diterima', [KabupatenPompaDiterimaController::class, 'index'])->name('kabupaten.diterima');
            Route::put('/verifikasi/diterima/{id}/update', [KabupatenPompaDiterimaController::class, 'update'])->name('kabupaten.diterima.update');
            Route::put('/verifikasi/diterima/{id}/approve', [KabupatenPompaDiterimaController::class, 'approve'])->name('kabupaten.diterima.approve');
            Route::put('/verifikasi/diterima/{id}/deny', [KabupatenPompaDiterimaController::class, 'deny'])->name('kabupaten.diterima.deny');
            Route::get('/verifikasi/dimanfaatkan', [KabupatenPompaDimanfaatkanController::class, 'index'])->name('kabupaten.dimanfaatkan');
            Route::put('/verifikasi/dimanfaatkan/{id}/update', [KabupatenPompaDimanfaatkanController::class, 'update'])->name('kabupaten.dimanfaatkan.update');
            Route::put('/verifikasi/dimanfaatkan/{id}/approve', [KabupatenPompaDimanfaatkanController::class, 'approve'])->name('kabupaten.dimanfaatkan.approve');
            Route::put('/verifikasi/dimanfaatkan/{id}/deny', [KabupatenPompaDimanfaatkanController::class, 'deny'])->name('kabupaten.dimanfaatkan.deny');
            Route::get('/verifikasi/luas-tanam', [KabupatenLuasTanamController::class, 'index'])->name('kabupaten.luas_tanam');
            Route::put('/verifikasi/luas-tanam/{id}/update', [KabupatenLuasTanamController::class, 'update'])->name('kabupaten.luas_tanam.update');
            Route::put('/verifikasi/luas-tanam/{id}/approve', [KabupatenLuasTanamController::class, 'approve'])->name('kabupaten.luas_tanam.approve');
            Route::put('/verifikasi/luas-tanam/{id}/deny', [KabupatenLuasTanamController::class, 'deny'])->name('kabupaten.luas_tanam.deny');
        });
    });
});
