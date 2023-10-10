<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\ExcelImportController;



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

Route::get('/', function () {
    return view('welcome');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified'
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::get('/user-profile', function () {
        return view('admin.userprofile');
    })->name('admin.userprofile');

    Route::get('/daftar-pengguna-baharu', function () {
        return view('admin.add_new_user');
    })->name('admin.add_new_user');
});


    Route::get('/file-import',[ExcelImportController::class,
            'importView'])->name('import-view');
    Route::post('/import',[ExcelImportController::class,
            'import'])->name('import');