<?php

use App\Http\Controllers\Api\PortalController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\PerananController;

use Illuminate\Support\Facades\Route;

Route::name('api.portal.')
    ->middleware('auth:sanctum')
    ->prefix('portal')
    ->group(function () {
        Route::post('/header', [PortalController::class, 'storeHeader'])->name('header.store');
        Route::get('/header', [PortalController::class, 'listHeader'])->name('header.list');
        Route::post('/landing', [PortalController::class, 'storeLanding'])->name('landing.store');
        Route::get('/landing', [PortalController::class, 'listLanding'])->name('landing.list');
        Route::post('/pengenalan', [PortalController::class, 'storePenganalan'])->name('pengenalan.store');
        Route::get('/pengenalan', [PortalController::class, 'listPenganalan'])->name('penganalan.list');
        Route::post('/contact', [PortalController::class, 'storeContact'])->name('contact.store');
        Route::get('/contact', [PortalController::class, 'listContact'])->name('contact.list');
        // Route::post('/pengumuman', [PortalController::class, 'storePengumuman'])->name('pengumuman.store');
        Route::get('/pengumuman', [PortalController::class, 'listPemgumuman'])->name('pengumuman.list');
        Route::get('/pengumuman/{id}', [PortalController::class, 'getPengumuman'])->name('pengumuman.details');
        Route::post('deActivatePengumuman',[PortalController::class, 'deActivatePengumuman'])->name('pengumuman.deActivatePengumuman');
        Route::post('ActivatePengumuman',[PortalController::class, 'ActivatePengumuman'])->name('pengumuman.ActivatePengumuman');

        Route::post('/footer', [PortalController::class, 'storeFooter'])->name('footer.store');
        Route::get('/footer', [PortalController::class, 'getFooter'])->name('footer.list');
        Route::post('/removefooterlogo', [PortalController::class, 'removefooterlogo'])->name('footer.list');


        Route::post('/add-peranan', [PerananController::class, 'addPeranan'])->name('peranan.addPeranan');
        Route::get('/getperanan/list', [PerananController::class, 'getPeranan'])->name('peranan.getPeranan');
        Route::post('/add-user-peranan', [PerananController::class, 'addUserPeranan'])->name('peranan.addUserPeranan');
        Route::post('/add-user-role', [PerananController::class, 'addUserRole'])->name('peranan.addUserRole');
        Route::post('/add-user-permission', [PerananController::class, 'addUserPermission'])->name('peranan.addUserPermission');
        Route::post('/delete-user-peranan', [PerananController::class, 'deleteUserPeranan'])->name('peranan.deleteUserPeranan');
        Route::post('/delete-peranan', [PerananController::class, 'deleteMasterPeranan'])->name('peranan.deleteMasterPeranan');
        Route::get('/check-user-peranan', [PerananController::class, 'getUserPeanan'])->name('peranan.getUserPeanan');
        Route::get('/get-peranan-data', [PerananController::class, 'getPerananData'])->name('peranan.getPerananData');
        Route::post('/update-peranan', [PerananController::class, 'updatePerananData'])->name('peranan.updatePerananData');

    });

Route::name('api.portal.')    
    ->prefix('portal')
    ->group(function () {
        Route::get('/main', [PortalController::class, 'getPortal'])->name('portal.main');
        Route::post('/pengumuman', [PortalController::class, 'storePengumuman'])->name('pengumuman.store');
});