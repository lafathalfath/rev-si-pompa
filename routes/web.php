<?php

use App\Http\Controllers\Authenticated\AdminController;
use App\Http\Controllers\Authenticated\DashboardController;
use App\Http\Controllers\Guest\AuthController;
use App\Http\Controllers\PjKabupaten\KabupatenPompaDimanfaatkanController;
use App\Http\Controllers\PjKabupaten\KabupatenPompaDiterimaController;
use App\Http\Controllers\PjKabupaten\KabupatenPompaHistoryController;
use App\Http\Controllers\PjKabupaten\KabupatenPompaUsulanController;
use App\Http\Controllers\PjKecamatan\KecamatanPompaDimanfaatkanController;
use App\Http\Controllers\PjKecamatan\KecamatanPompaDiterimaController;
use App\Http\Controllers\PjKecamatan\KecamatanPompaHistoryController;
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
Route::get('/manfaat_pengisiandata', function () { return view('pj_kecamatan.manfaat_pengisiandata'); });

Route::prefix('/auth')->group(function () {
    Route::middleware('guest')->group(function () {
        Route::get('/login', [AuthController::class, 'loginView'])->name('auth.login.view');
        Route::post('/login', [AuthController::class, 'login'])->name('auth.login');
    });
    Route::get('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('auth.logout');
});

Route::middleware('auth')->group(function () {
    Route::middleware('nonactive')->group(function () {
        Route::get('/aktivasi', [DashboardController::class, 'activation'])->name('activation');
        Route::post('/aktivasi', [DashboardController::class, 'activate'])->name('activate');
    });
    
    Route::middleware('active')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        
        Route::middleware('access:admin')->group(function () {
            Route::get('/daftar-pj', [AdminController::class, 'userList'])->name('admin.daftar_pj');
            Route::post('/tambah-pj', [AdminController::class, 'createUser'])->name('admin.tambah_pj');
            Route::put('/edit-pj/{id}', [AdminController::class, 'editUser'])->name('admin.edit_pj');
            Route::delete('/hapus-pj/{id}', [AdminController::class, 'deleteUser'])->name('admin.hapus_pj');
        });
        
        Route::prefix('/kecamatan')->middleware('access:pj_kecamatan')->group(function () {
            Route::post('/poktan/store', [PoktanController::class, 'store'])->name('kecamatan.poktan.store');
            Route::prefix('/usulan')->group(function () {
                Route::get('/', [KecamatanPompaUsulanController::class, 'index'])->name('kecamatan.usulan');
                Route::get('/create', [KecamatanPompaUsulanController::class, 'create'])->name('kecamatan.usulan.create');
                Route::post('/store', [KecamatanPompaUsulanController::class, 'store'])->name('kecamatan.usulan.store');
                Route::put('/{id}/update', [KecamatanPompaUsulanController::class, 'update'])->name('kecamatan.usulan.update');
                Route::delete('/{id}/destroy', [KecamatanPompaUsulanController::class, 'destroy'])->name('kecamatan.usulan.destroy');
            });
            Route::get('/diterima', [KecamatanPompaDiterimaController::class, 'index'])->name('kecamatan.diterima');
            Route::prefix('/dimanfaatkan')->group(function () {
                Route::get('/', [KecamatanPompaDimanfaatkanController::class, 'index'])->name('kecamatan.dimanfaatkan');
                Route::get('/{id}/detail', [KecamatanPompaDimanfaatkanController::class, 'detail'])->name('kecamatan.dimanfaatkan.detail');
                Route::post('/{id}/store', [KecamatanPompaDimanfaatkanController::class, 'store'])->name('kecamatan.dimanfaatkan.store');
                Route::put('/{id}/update', [KecamatanPompaDimanfaatkanController::class, 'update'])->name('kecamatan.dimanfaatkan.update');
                Route::delete('/{id}/destroy', [KecamatanPompaDimanfaatkanController::class, 'destroy'])->name('kecamatan.dimanfaatkan.destroy');
                Route::put('/{id}/ready-verify', [KecamatanPompaDimanfaatkanController::class, 'makeReadyVerify'])->name('kecamatan.dimanfaatkan.ready_verify');
            });
            Route::prefix('/history')->group(function () {
                Route::get('/verified', [KecamatanPompaHistoryController::class, 'verified'])->name('kecamatan.history.verified');
                Route::get('/verified/{id}/detail', [KecamatanPompaDimanfaatkanController::class, 'detail'])->name('kecamatan.history.verified.detail');
                Route::get('/denied', [KecamatanPompaHistoryController::class, 'denied'])->name('kecamatan.history.denied');
            });
        });
        
        Route::prefix('/kabupaten')->middleware('access:pj_kabupaten')->group(function () {
            Route::prefix('/usulan')->group(function () {
                Route::get('/', [KabupatenPompaUsulanController::class, 'index'])->name('kabupaten.usulan');
                Route::put('/{id}/update', [KabupatenPompaUsulanController::class, 'update'])->name('kabupaten.usulan.update');
                Route::put('/{id}/approve', [KabupatenPompaUsulanController::class, 'approve'])->name('kabupaten.usulan.approve');
                Route::put('/{id}/deny', [KabupatenPompaUsulanController::class, 'deny'])->name('kabupaten.usulan.deny');
            });
            Route::get('/diterima', [KabupatenPompaDiterimaController::class, 'index'])->name('kabupaten.diterima');
            Route::prefix('/dimanfaatkan')->group(function () {
                Route::get('/', [KabupatenPompaDimanfaatkanController::class, 'index'])->name('kabupaten.dimanfaatkan');
                Route::get('/{id}/datail', [KabupatenPompaDimanfaatkanController::class, 'detail'])->name('kabupaten.dimanfaatkan.detail');
                Route::put('/{id}/verify', [KabupatenPompaDimanfaatkanController::class, 'verify'])->name('kabupaten.dimanfaatkan.verify');
            });
            Route::prefix('/history')->group(function () {
                Route::get('/verified', [KabupatenPompaHistoryController::class, 'verified'])->name('kabupaten.history.verified');
                Route::get('/verified/{id}/detail', [KabupatenPompaDimanfaatkanController::class, 'detail'])->name('kabupaten.history.verified.detail');
                Route::get('/denied', [KabupatenPompaHistoryController::class, 'denied'])->name('kabupaten.history.denied');
            });
        });
    });
});
