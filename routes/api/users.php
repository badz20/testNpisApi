<?php

use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DownloadFileController;
use App\Http\Controllers\Api\DashboardController;
use Illuminate\Support\Facades\Route;

Route::name('api.users.')
    ->middleware('auth:sanctum')
    ->prefix('user')
    ->group(function () {
        Route::get('/list', [UserController::class, 'getUsers'])->name('users');
        Route::get('/temp/list', [UserController::class, 'getTempUsers'])->name('users.temp');
        Route::get('/temp/all/list', [UserController::class, 'getAllTempUsers'])->name('users.all.temp');
        Route::get('/details/{id}', [UserController::class, 'userDetails'])->name('users.details');
        Route::post('/create', [UserController::class, 'createUser'])->name('users.create');
        Route::post('/approval', [UserController::class, 'userApproval'])->name('users.approval');
        Route::get('/user/download', [DownloadFileController::class, 'downloadFile'])->name('users.download');
        Route::post('/user/update', [UserController::class, 'updateUser'])->name('users.update');
        Route::get('/dashboard', [DashboardController::class, 'getUsersCount'])->name('users.dashboard');
        Route::post('/jawatanName', [UserController::class, 'jawatanName'])->name('users.jawatanName');
        Route::get('/active', [UserController::class, 'activeUser'])->name('users.active');
        Route::post('/ActivateUser', [UserController::class, 'ActivateUser'])->name('users.ActivateUser');

        Route::post('/deActivateUser', [UserController::class, 'deActivateUser'])->name('users.deActivateUser');    
        
        
        Route::get('/maps', [UserController::class, 'mapsService'])->name('users.maps');
        Route::post('/mapServiceData', [UserController::class, 'mapServiceData'])->name('users.mapServiceData');
        Route::post('/mapsedit', [UserController::class, 'mapServiceEdit'])->name('users.mapServiceEdit');
        Route::post('/mapsupdate', [UserController::class, 'mapsupdate'])->name('users.mapsupdate');
        Route::post('CheckMapserviceStatus',[UserController::class, 'CheckMapserviceStatus'])->name('users.mapsupdate');


        Route::post('/deActivateUser', [UserController::class, 'deActivateUser'])->name('users.deActivateUser');
        Route::get('/get-registration-log', [UserController::class, 'getRegistrationLog'])->name('users.getRegistrationLog');
        Route::get('/UserLoginMonthly', [UserController::class, 'userMonthlyLoginCount'])->name('users.monthly.count');

        Route::post('/change-password', [UserController::class, 'ChangePassword'])->name('users.ChangePassword');
        Route::get('/get-notifications', [UserController::class, 'getNotifications'])->name('users.getNotifications');
        Route::post('/mark-notifications', [UserController::class, 'markNotifications'])->name('users.markNotifications');
         
    });

    Route::name('api.users.')
    ->prefix('user')
    ->group(function () {
        Route::get('/userDetails/{email}/{ic}', [UserController::class, 'userByEmailIc'])->name('users.detail.email.ic');
    });

Route::name('api.temp.')    
    ->prefix('temp')
    ->group(function () {
        Route::post('/user/temp/updateRejection', [UserController::class, 'updateRejectionDetails'])->name('users.temp.reject');
        Route::post('/temp/user/update', [UserController::class, 'updateTempUser'])->name('users.temp.update');
        Route::get('/details/temp/{id}', [UserController::class, 'userTempDetails'])->name('users.temp.details');
        Route::post('user/auth', [AuthController::class, 'login'])->name('users.temp.login');
        Route::post('user/create', [UserController::class, 'createTempUser'])->name('users.temp.create');
});