<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\DownloadFileController;
// use App\Http\Controllers\Api\vae_Controller;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::post('gambar-profil-upload', [UserController::class, 'gambarProfil']);
Route::post('dokumen-sokongan', [UserController::class, 'dokumenSokongan']); 
Route::get('/media/download', [DownloadFileController::class, 'downloadMedia'])->name('media.download');
Route::get('/media/download/perunding/prestasi', [DownloadFileController::class, 'downloadPrestasiTemplate'])->name('media.download.PrestasiTemplate');

// Route::post('vae_data', [vae_Controller::class, 'vae_data']);