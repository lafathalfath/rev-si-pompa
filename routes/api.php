<?php

use App\Http\Controllers\API\APINotificationController;
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
    
    Route::get('/notification', [APINotificationController::class, 'getAll']);
    Route::get('/notification/{id}', [APINotificationController::class, 'get']);
    Route::post('/notification', [APINotificationController::class, 'send']);
    Route::put('/notification/{id}/mark-as-read', [APINotificationController::class, 'read']);
    Route::delete('/notification/{id}/delete', [APINotificationController::class, 'delete']);
});

Route::get('/provinsi', [APIRegionController::class, 'getProvinsi']);
Route::get('/kabupaten', [APIRegionController::class, 'getKabupaten']);
Route::get('/kecamatan', [APIRegionController::class, 'getKecamatan']);
Route::get('/desa', [APIRegionController::class, 'getDesa']);
Route::get('/desa/{name}', [APIRegionController::class, 'getDesaByKecamatanName']);
