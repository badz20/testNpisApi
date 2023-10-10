<?php

use App\Http\Controllers\Api\AgensiController;
use App\Http\Controllers\Api\BahagianController;
use App\Http\Controllers\Api\DaerahController;
use App\Http\Controllers\Api\GredJawatanController;
use App\Http\Controllers\Api\JabatanController;
use App\Http\Controllers\Api\JawatanController;
use App\Http\Controllers\Api\JenisPenggunaController;
use App\Http\Controllers\Api\NegeriController;
use App\Http\Controllers\Api\LookupController;
use App\Http\Controllers\Api\ParlimenController;
use App\Http\Controllers\Api\DunController;
use App\Http\Controllers\Api\PDS_Controller;
use App\Http\Controllers\Api\PSDA_Controller;
use App\Http\Controllers\Api\Pentadbir_Modules_Controller;
use App\Http\Controllers\Api\KementerianController;
use App\Http\Controllers\Api\MukimController;
use App\Http\Controllers\Api\rmk_Controller;
use App\Http\Controllers\Api\RmkObbController;
use App\Http\Controllers\Api\ProjectController;
use App\Http\Controllers\Api\SkopController;
use App\Http\Controllers\Api\SubSkopController;
use App\Http\Controllers\Api\LookupOptionsController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\RmkSdgController;
use App\Http\Controllers\Api\RmkSasaranController;
use App\Http\Controllers\Api\RmkIndikatoriController;






Route::name('api.lookup.')
    //->middleware('auth:sanctum')
    ->prefix('lookup')
    ->group(function () {
        Route::get('/agensi/list', [AgensiController::class, 'list'])->name('agensi.list');
        Route::get('/bahagian/list', [BahagianController::class, 'list'])->name('bahagian.list');
        Route::get('/bahagian/list_bahagian', [BahagianController::class, 'listBahagian'])->name('bahagian.listBahagian');
        Route::get('/daerah/list', [DaerahController::class, 'list'])->name('daerah.list');
        Route::get('/daerah/list_daerah', [DaerahController::class, 'listDaerah'])->name('daerah.listDaerah');
        Route::get('/gredjawatan/list', [GredJawatanController::class, 'list'])->name('gred.jawatan.list');
        Route::get('/gredjawatan/list_gred', [GredJawatanController::class, 'listGred'])->name('gred.jawatan.listGred');

        Route::get('/masterData/list', [GredJawatanController::class, 'masterData'])->name('gred.jawatan.list');
        Route::get('/jabatan/list', [JabatanController::class, 'list'])->name('jabatan.list');
        Route::get('/jabatan/list_jabatan', [JabatanController::class, 'listJabatan'])->name('jabatan.listJabatan');
        Route::get('/jawatan/list', [JawatanController::class, 'list'])->name('jawatan.list');
        Route::get('/jawatan/list_jawatan', [JawatanController::class, 'listJawatan'])->name('jawatan.listJawatan');

        Route::get('/jenis/pengguna/list', [JenisPenggunaController::class, 'list'])->name('jenis.pengguna.list');
        Route::get('/negeri/list', [NegeriController::class, 'list'])->name('negeri.list');
        Route::get('/negeri/list_negeri', [NegeriController::class, 'listNegeri'])->name('negeri.listNegeri');
        Route::get('/parlimen/list', [ParlimenController::class, 'list'])->name('parliment.list');  
        Route::get('/parlimen/list_parlimen', [ParlimenController::class, 'listPalimen'])->name('parliment.listPalimen');      
        Route::get('/mukim/list', [MukimController::class, 'list'])->name('mukim.list');    
        Route::get('/dun/list', [DunController::class, 'list'])->name('dun.list');
        Route::get('/dun/list_dun', [DunController::class, 'listDun'])->name('dun.listDun');
        Route::get('/Pds/list', [PSDA_Controller::class, 'list'])->name('pds.list');
        Route::get('/rmk/list', [rmk_Controller::class, 'list'])->name('rmk.list');  
        Route::get('/obb/list', [RmkObbController::class, 'obbmasterlist'])->name('rmk.obbmasterlist'); 
        Route::get('/bahagian-list', [BahagianController::class, 'listWithKementerien'])->name('bahagian.listWithKementerien');
        Route::get('/kementerian-list-by-name', [KementerianController::class, 'listByname'])->name('kementerian.listByname');
        Route::get('/kementerian-list-with-id', [KementerianController::class, 'listwithKementerianId'])->name('kementerian.listwithKementerianId');
        Route::get('/kementerian/list_kementerian', [KementerianController::class, 'listwithKementerian'])->name('kementerian.listwithKementerian');
        Route::get('/pejabat-projek/list', [ProjectController::class, 'listPejabatProject'])->name('project.listPejabatProject');   
        Route::get('/pejabat-projek/list_pejabat_projek', [ProjectController::class, 'PejabatProject'])->name('project.PejabatProject');   
        Route::get('/pejabat-projek/list_pejabat_projek/{id}', [ProjectController::class, 'PejabatProject'])->name('project.PejabatProject');   

        Route::get('/skop/list', [SkopController::class, 'list'])->name('skop.list');
        Route::get('/skop/list_skop', [SkopController::class, 'listSkop'])->name('skop.listSkop');
        Route::get('/sub_skop/list', [SubSkopController::class, 'list'])->name('subskop.list');
        Route::get('/sub_skop/list_sub_skop', [SubSkopController::class, 'listSubSkop'])->name('subskop.listSubSkop');

        Route::get('/options/list', [LookupOptionsController::class, 'list'])->name('options.list');
        Route::get('/options/distint/key', [LookupOptionsController::class, 'listKey'])->name('options.list.key');

        Route::get('/sdg/list', [RmkSdgController::class, 'listallsdg'])->name('SDG.list');
        Route::get('/sdg-details/{id}', [RmkSdgController::class, 'listsdg_single'])->name('SDG.listSingle');
        Route::post('/update-SDG', [RmkSdgController::class, 'updateSDGMasterData'])->name('SDG.updatesasaran');
        Route::post('/ActivateSDG', [RmkSdgController::class, 'activate'])->name('SDG.updatesasaran');
        Route::post('/DeactivateSDG', [RmkSdgController::class, 'deactivate'])->name('SDG.updatesasaran');

        Route::get('/sasaran/list', [RmkSasaranController::class, 'list'])->name('Sasaran.list');
        Route::get('/sasaran/list/{id}', [RmkSasaranController::class, 'listsingle'])->name('Sasaran.list');
        Route::get('/sasaran-single/{id}', [RmkSasaranController::class, 'listsasaran_single'])->name('Sasaran.Singlelist');
        Route::post('/update-sasaran', [RmkSasaranController::class, 'updateSasaranMasterData'])->name('Sasaran.updatesasaran');
        Route::post('/ActivateSasaran', [RmkSasaranController::class, 'activate'])->name('Sasaran.updatesasaran');
        Route::post('/DeactivateSasaran', [RmkSasaranController::class, 'deactivate'])->name('Sasaran.updatesasaran');

        Route::get('/indikator/list', [RmkIndikatoriController::class, 'list'])->name('Indikator.list');
        Route::get('/indikator/listall', [RmkIndikatoriController::class, 'listall'])->name('Indikator.list');
        Route::get('/indikator-single/{id}', [RmkIndikatoriController::class, 'listindikator_single'])->name('Indikator.Singlelist');
        Route::post('/update-indikator', [RmkIndikatoriController::class, 'updateIndikatorMasterData'])->name('Indikator.updateIndikator');
        Route::post('/ActivateIndikator', [RmkIndikatoriController::class, 'activate'])->name('Indikator.updateIndikator');
        Route::post('/DeactivateIndikator', [RmkIndikatoriController::class, 'deactivate'])->name('Indikator.updateIndikator');


        Route::get('/getMasterLinks', [Pentadbir_Modules_Controller::class, 'listMasterlinks'])->name('Pentadbir_Modules_Controller.listMasterlinks');
        Route::get('/getMasterLinksforUserprofile', [Pentadbir_Modules_Controller::class, 'Masterlinks'])->name('Pentadbir_Modules_Controller.listMasterlinks');
        Route::get('/get_master_details/{id}/edit', [Pentadbir_Modules_Controller::class, 'getMasterDetails'])->name('Pentadbir_Modules_Controller.getMasterDetails');
        Route::post('/add_master_details', [Pentadbir_Modules_Controller::class, 'addMasterDetails'])->name('Pentadbir_Modules_Controller.addMasterDetails');
        Route::post('/Activatemasterdata', [Pentadbir_Modules_Controller::class, 'activate'])->name('Pentadbir_Modules_Controller.Activatemasterdata');
        Route::post('/Deactivatemasterdata', [Pentadbir_Modules_Controller::class, 'deactivate'])->name('Pentadbir_Modules_Controller.deactivate');
        Route::get('/get_module_access_by_usertype', [Pentadbir_Modules_Controller::class, 'getModuleAccessByUsertype'])->name('Pentadbir_Modules_Controller.getModuleAccessByUsertype');

    });
   