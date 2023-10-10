<?php

use App\Http\Controllers\Api\ExcelController;

Route::name('api.export.')
    ->middleware('auth:sanctum')
    ->prefix('export')
    ->group(function () {

        Route::get('/projects', [ExcelController::class, 'export_projects'])->name('project.export');

    });