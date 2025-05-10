<?php

use App\Http\Controllers\API\APIPoktanController;
use App\Http\Controllers\API\APIRegionController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/poktan', [APIPoktanController::class, 'getPoktan']);
    Route::get('/poktan/{name}', [APIPoktanController::class, 'getPoktanByName']);
});

Route::get('/provinsi', [APIRegionController::class, 'getProvinsi']);
Route::get('/kabupaten', [APIRegionController::class, 'getKabupaten']);
Route::get('/kecamatan', [APIRegionController::class, 'getKecamatan']);
Route::get('/desa', [APIRegionController::class, 'getDesa']);
