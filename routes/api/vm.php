<?php

use App\Http\Controllers\Api\VM\VmMmpmController;
use App\Http\Controllers\Api\VM\VmObjektifController;
use App\Http\Controllers\Api\VM\VmButiranController;
use App\Http\Controllers\Api\VM\UlasanController;
use App\Http\Controllers\Api\ValueManagementController;



Route::name('api.vm.')
    //->middleware('auth:sanctum')
    ->prefix('vm')
    ->group(function () {

        Route::resource('/mmpm', VmMmpmController::class)->only([
            'index', 'store' , 'edit'
        ]);
        Route::resource('/butiran', VmButiranController::class)->only([
            'index', 'store' , 'edit'
        ]);
        Route::resource('/ulasan', UlasanController::class)->only([
            'index', 'store' , 'edit'
        ]);
        Route::post('/remove/mmpm', [VmMmpmController::class, 'destroy'])->name('mmpm.delete');;
        Route::resource('/objektif', VmObjektifController::class);
        Route::get('/dokumen_download/mmpm', [VmMmpmController::class, 'downloadDoc'])->name('mmpm.dokumen_download');
        Route::get('/dokumen_download/lampiran', [UlasanController::class, 'downloadLampiranDoc'])->name('lampiran.dokumen_download');
        Route::get('/dokumen_download/objektif', [VmObjektifController::class, 'downloadDoc'])->name('objektif.dokumen_download');
        Route::post('/selasai', [UlasanController::class, 'selasaiVM'])->name('ulasan.selasaiVM');
        Route::post('/vr_objectiveData', [VmObjektifController::class, 'vr_objectiveData'])->name('VM.vr_objectiveData');
        
        Route::get('/vrData/{kod}/{type}', [VmObjektifController::class, 'vrData'])->name('objektif.vrData');
       
    });