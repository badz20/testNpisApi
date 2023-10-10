<?php

use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\PermissionController;
use App\Http\Controllers\Api\UserTypeController;

Route::name('api')
    ->middleware('auth:sanctum')
    ->group(function () {
        Route::resource('/role', RoleController::class);
        Route::resource('/permission', PermissionController::class);
        Route::resource('/usertypes', UserTypeController::class);

        Route::get('/user_types/roles/{id}', [RoleController::class, 'userTypeRoles'])->name('user.type.roles');
        Route::get('/user/permissions/{id}', [PermissionController::class, 'userPermissions'])->name('user.permissions');
        Route::post('/role/permissions', [RoleController::class, 'rolePermissions'])->name('user.role.permission');
        Route::post('/user/permissions', [RoleController::class, 'userSpecificPermissions'])->name('user.specific.permission');
        
        
    });