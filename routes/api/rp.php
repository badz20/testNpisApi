<?php

use App\Http\Controllers\Api\RP\BahagianController;
use App\Http\Controllers\Api\RP\BKORController;
use Illuminate\Support\Facades\Route;

Route::name('api.rp.')
    ->middleware('auth:sanctum')
    ->prefix('rp')
    ->group(function () {

        Route::resource('/bkor', BKORController::class)->only([
            'index', 'store' , 'edit'
        ]);
        Route::resource('/bahagian', BahagianController::class)->only([
            'store'
        ]);

        Route::post('/negeri', [BKORController::class, 'storeNegeri'])->name('project.rp.store.negeri');
        Route::get('/project_filter_list', [BKORController::class, 'filteredIndex'])->name('project.rp.filtered.index');
    });