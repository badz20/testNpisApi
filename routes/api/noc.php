<?php

use App\Http\Controllers\Api\Perunding\PerundingController;
use App\Http\Controllers\Api\Perunding\MaklumatController;
use App\Http\Controllers\Api\Perunding\PrestasiController;
use App\Http\Controllers\Api\ProjectNegeriLokasController;
use App\Http\Controllers\Api\NOC_Controller;
use Illuminate\Support\Facades\Route;


Route::name('api.noc.')
    //->middleware('auth:sanctum')
    ->prefix('noc')
    ->group(function () {
        Route::get('/noc_projectDetails/{id}', [NOC_Controller::class, 'list'])->name('noc.project.list');
        Route::post('/StoreNoc', [NOC_Controller::class, 'store'])->name('noc.project.store');
        Route::post('/update-negerinoc', [NOC_Controller::class, 'updateNegeriNOC'])->name('projectnegerilokasnoc.update'); 
        Route::post('/StoreNocKpi', [NOC_Controller::class, 'StoreNocKpi'])->name('noc.project.StoreNocKpi');
        Route::post('/StoreNocOutput', [NOC_Controller::class, 'StoreNocOutput'])->name('noc.project.StoreNocOutput');
        Route::post('/StoreNocOutcome', [NOC_Controller::class, 'StoreNocOutcome'])->name('noc.project.StoreNocOutcome');
        Route::get('/nocList/{id}', [NOC_Controller::class, 'nocList'])->name('noc.project.nocList');
        Route::get('/nocPageData/{kod}', [NOC_Controller::class, 'nocPageData'])->name('noc.project.nocPageData');
        Route::get('/projectData/{kod}', [NOC_Controller::class, 'projectData'])->name('noc.project.projectData');
        Route::get('/nocKpiData/{kod}', [NOC_Controller::class, 'nocKpiData'])->name('noc.project.nocKpiData');
        Route::get('/NocOutputData/{noc_id}', [NOC_Controller::class, 'NocOutputData'])->name('noc.project.NocOutputData');
        Route::get('/NocOutcomeData/{noc_id}', [NOC_Controller::class, 'NocOutcomeData'])->name('noc.project.NocOutcomeData');
        Route::get('/negeri-details/{id}', [NOC_Controller::class, 'negeriDetails_noc'])->name('projnegerilokasnoc.details');
        Route::get('/negeri-details-pementuan/{id}', [NOC_Controller::class, 'negeriDetails_pementuan'])->name('projnegerilokaspem.details');
        Route::post('/StoreNocButiranBaharu', [NOC_Controller::class, 'StoreNocButiranBaharu'])->name('noc.project.StoreNocButiranBaharu');
        Route::post('/StoreNocSemulaButiran', [NOC_Controller::class, 'StoreNocSemulaButiran'])->name('noc.project.StoreNocSemulaButiran');
        Route::post('/status-update', [NOC_Controller::class, 'StatusUpdate'])->name('noc.StatusUpdate');
        Route::post('/kementerian-update', [NOC_Controller::class, 'KementerianUpdate'])->name('noc.KementerianUpdate'); 
        Route::get('/noc-kementerian-data/{kod}', [NOC_Controller::class, 'nocKementerianData'])->name('noc.project.nocKementerianData');
        Route::get('/previewfile', [NOC_Controller::class, 'previewfile'])->name('noc.previewfile');
        Route::post('/kementerian-economi-update', [NOC_Controller::class, 'KementerianEconomicUpdate'])->name('noc.KementerianEconomicUpdate'); 
        Route::get('/list_noc', [NOC_Controller::class, 'ListNoc'])->name('noc.ListNoc');
        Route::post('/addNOC', [NOC_Controller::class, 'addNOC'])->name('noc.addNOC');
        Route::get('/get-old-project-details/{kod}', [NOC_Controller::class, 'getOldProjectDetails'])->name('noc.project.getOldProjectDetails');
        Route::get('/get-checkbox-statuses/{pp_id}/{noc_id}', [NOC_Controller::class, 'getCheckboxStatuses'])->name('noc.project.getCheckboxStatuses');
        Route::get('/list_projects/{id}/{type}', [NOC_Controller::class, 'ListProjects'])->name('noc.ListProjects');
        Route::post('/addNOC-project', [NOC_Controller::class, 'addNOCProject'])->name('noc.addNOCProject');
        Route::post('/deleteNOC', [NOC_Controller::class, 'deleteNOC'])->name('noc.deleteNOC');
        Route::post('/update_noc_status', [NOC_Controller::class, 'updateNocStatus'])->name('noc.updateNocStatus');
        Route::get('/noc-kementerian-silling-data/{kod}', [NOC_Controller::class, 'nocKementerianSillingData'])->name('noc.nocKementerianSillingData');
        Route::post('/kementerian-silling-update', [NOC_Controller::class, 'KementerianSillingUpdate'])->name('noc.KementerianSillingUpdate'); 
        Route::post('/kementerian-silling-economi-update', [NOC_Controller::class, 'KementerianSillingEconomicUpdate'])->name('noc.KementerianSillingEconomicUpdate'); 


        Route::post('/save-noc-data', [NOC_Controller::class, 'saveNocData'])->name('noc.saveNocData');
        Route::post('/save-pindan-data', [NOC_Controller::class, 'savePindanData'])->name('noc.savePindanData');
        Route::post('/save-maklubalas-data', [NOC_Controller::class, 'saveMaklubalasData'])->name('noc.saveMaklubalasData');
        Route::post('/updateBilanganData', [NOC_Controller::class, 'updateBilanganData'])->name('noc.updateBilanganData');
        Route::post('/update_bilangan_toggle_status', [NOC_Controller::class, 'updateBilanganToggleStatus'])->name('noc.updateBilanganToggleStatus');
        Route::post('/updateKeperluanData', [NOC_Controller::class, 'updateKeperluanData'])->name('noc.updateKeperluanData');
});


Route::name('api.noc.')    
    ->prefix('noc')
    ->group(function () {
        Route::get('/get_noc_data', [NOC_Controller::class, 'getNocData'])->name('noc.getNocData');
        Route::get('/jbt_project_list', [NOC_Controller::class, 'getJBTdata'])->name('noc.getJBTdata');
        Route::get('/project_filter_list', [NOC_Controller::class, 'filteredIndex'])->name('noc.project.filtered.list');
});


