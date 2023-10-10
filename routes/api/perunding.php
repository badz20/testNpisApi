<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Perunding\PerundingController;
use App\Http\Controllers\Api\Perunding\MaklumatController;
use App\Http\Controllers\Api\Perunding\PrestasiController;
use App\Http\Controllers\Api\Perunding\PenilaianController;
use App\Http\Controllers\Api\Perunding\UnjuranController;

Route::name('api.perunding.')
    //->middleware('auth:sanctum')
    ->prefix('perunding')
    ->group(function () {

        Route::get('/project_list', [PerundingController::class, 'index'])->name('perunding.project.list');
        Route::get('/project_filter_list', [PerundingController::class, 'filteredIndex'])->name('perunding.project.filtered.list');
        Route::get('/deliverable_list', [PerundingController::class, 'getDeliverables'])->name('perunding.deliverables.list');
        
        Route::post('/maklumat', [MaklumatController::class, 'store'])->name('perunding.makulumat.list');
        Route::get('/maklumat_details', [MaklumatController::class, 'edit'])->name('perunding.makulumat.details');

        Route::post('/prestasi', [PrestasiController::class, 'store'])->name('perunding.prestasi.list');
        Route::get('/prestasi_details', [PrestasiController::class, 'edit'])->name('perunding.prestasi.details');
        Route::get('/prestasi_masalah', [PrestasiController::class, 'getMasalahById'])->name('perunding.prestasi.masalah.edit');
        Route::get('/prestasi_masalah/list', [PrestasiController::class, 'getMasalah'])->name('perunding.prestasi.masalah.list');
        Route::post('/prestasi_masalah', [PrestasiController::class, 'storeMasalah'])->name('perunding.prestasi.masalah.store');
        Route::post('/prestasi_rekord_lampiran', [PrestasiController::class, 'storeRekordLampiran'])->name('perunding.prestasi.store.rekord.lampiran');
        Route::get('/prestasi_rekord_lampiran/list', [PrestasiController::class, 'getRekordLampiran'])->name('perunding.prestasi.list.rekord.lampiran');
        Route::post('/prestasi_rekord_lampiran/delete', [PrestasiController::class, 'deleteRekordLampiran'])->name('perunding.prestasi.delete.rekord.lampiran');

        Route::post('/penilaian', [PenilaianController::class, 'store'])->name('perunding.penilaian.store');
        Route::get('/penilaian', [PenilaianController::class, 'list'])->name('perunding.penilaian.list');
        Route::get('/penilaian_details', [PenilaianController::class, 'edit'])->name('perunding.penilaian.details');

        Route::get('/prestasi_sejarah/list', [PrestasiController::class, 'getPrestasiSejarah'])->name('perunding.prestasi.sejarah.list');
        Route::get('/prestasi_sejarah/latest', [PrestasiController::class, 'getLatestPrestasiSejarah'])->name('perunding.prestasi.sejarah.latest');

        Route::get('/penilaian_sejarah/list', [PenilaianController::class, 'getPenilaianSejarah'])->name('perunding.penilaian.sejarah.list');
        Route::get('/penilaian_sejarah/latest', [PenilaianController::class, 'getLatestPenilaianSejarah'])->name('perunding.penilaian.sejarah.latest');

        Route::get('/kewangan/unjuran/list', [UnjuranController::class, 'list'])->name('perunding.kewangan.unjuran.list');
        Route::post('/kewangan/unjuran/store', [UnjuranController::class, 'store'])->name('perunding.kewangan.unjuran.store');
    });


Route::name('api.perunding.')
    ->prefix('perunding')
    ->group(function () {
        Route::post('/add_perkara', [PerundingController::class, 'addPerkara'])->name('perunding.addPerkara');
        Route::post('/update_peraku', [PerundingController::class, 'updatePerkara'])->name('perunding.updatePerkara');
        Route::get('/list_perkara/{id}/{perolehan}/{bayaran}', [PerundingController::class, 'GetPerkara'])->name('perunding.GetPerkara');
        Route::get('/list_bayaran/{id}/{perolehan}', [PerundingController::class, 'ListBayaran'])->name('perunding.ListBayaran');
        Route::post('/add_bayaran', [PerundingController::class, 'addBayaran'])->name('perunding.addBayaran');
        Route::get('/get_yuran_perunding/{id}/{perolehan}/{bayaran}', [PerundingController::class, 'getYuranPerunding'])->name('perunding.getYuranPerunding');
        Route::get('/get_lejar_bayaran/{id}/{perolehan}', [PerundingController::class, 'getLejarBayaran'])->name('perunding.getLejarBayaran');
        Route::post('/update_bayaran', [PerundingController::class, 'updateBayaran'])->name('perunding.updateBayaran');
        Route::post('/rekord_bayaran_selesai', [PerundingController::class, 'rekordSelesai'])->name('perunding.rekordSelesai');
        Route::post('/update_yuran', [PerundingController::class, 'updateYuran'])->name('perunding.updateYuran');
        Route::get('/get_borang_perunding/{id}/{perolehan}/{bayaran}', [PerundingController::class, 'getBorangdata'])->name('perunding.getBorangdata');
        Route::get('/get_history/{id}/{perolehan}/{bayaran}', [PerundingController::class, 'getHistory'])->name('perunding.getHistory');
    });
    
    