<?php

use App\Http\Controllers\Api\AgensiController;
use App\Http\Controllers\Api\BahagianController;
use App\Http\Controllers\Api\DaerahController;
use App\Http\Controllers\Api\GredJawatanController;
use App\Http\Controllers\Api\JabatanController;
use App\Http\Controllers\Api\JawatanController;
use App\Http\Controllers\Api\JenisPenggunaController;
use App\Http\Controllers\Api\NegeriController;
use App\Http\Controllers\Api\ParlimenController;
use App\Http\Controllers\Api\LookupController;
use App\Http\Controllers\Api\DunController;
use App\Http\Controllers\Api\KementerianController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\PDS_Controller;
use App\Http\Controllers\Api\PSDA_Controller;
use App\Http\Controllers\Api\Pentadbir_Modules_Controller;
use App\Http\Controllers\Api\MukimController;
use App\Http\Controllers\Api\vae_Controller;
use App\Http\Controllers\Api\BahagianEpuController;
use App\Http\Controllers\Api\SektorUtamaController;
use App\Http\Controllers\Api\SektorController;
use App\Http\Controllers\Api\SubSektorController;
use App\Http\Controllers\Api\JenisKategoriController;
use App\Http\Controllers\Api\JenisSubKategoriController;
use App\Http\Controllers\Api\rmk_Controller;
use App\Http\Controllers\Api\RmkObbController;
use App\Http\Controllers\Api\UnitsController;
use App\Http\Controllers\Api\KewanganKomponenController;
use App\Http\Controllers\Api\ProjectController;
use App\Http\Controllers\Api\SkopController;
use App\Http\Controllers\Api\SubSkopController;
use App\Http\Controllers\Api\LookupOptionsController;
use App\Http\Controllers\Api\RmkStrategiController;
use App\Http\Controllers\Api\DeliverableHeadingController;
use App\Http\Controllers\Api\DeliverableController;
use App\Http\Controllers\Api\BelanjaMengurusSkopController;
use App\Http\Controllers\Api\BelanjaMengurusSubSkopController;
use App\Http\Controllers\Api\MasterAgensiController;


Route::name('api.lookup.')
    ->middleware('auth:sanctum')
    ->prefix('lookup')
    ->group(function () {
        Route::get('/master', [LookupController::class, 'index'])->name('lookup.index');
        Route::post('/master', [LookupController::class, 'store'])->name('lookup.store');
        Route::post('/Negeri', [NegeriController::class, 'store'])->name('negeri.store');
        Route::get('/Negeri/{id}', [NegeriController::class, 'edit'])->name('negeri.edit');
        Route::put('/Negeri', [NegeriController::class, 'update'])->name('negeri.update');
        Route::post('/ActivateNegeri', [NegeriController::class, 'activate'])->name('negeri.ActivateNegeri'); 
        Route::post('/DeactivateNegeri', [NegeriController::class, 'deactivate'])->name('negeri.DeactivateNegeri');
        Route::post('/Daerah', [DaerahController::class, 'store'])->name('daerah.store');    
        Route::get('/Daerah/{id}', [DaerahController::class, 'edit'])->name('daerah.edit');
        Route::post('/ActivateDaerah', [DaerahController::class, 'activate'])->name('daerah.ActivateDaerah'); 
        Route::post('/DeactivateDaerah', [DaerahController::class, 'deactivate'])->name('daerah.DeactivateDaerah');
        Route::post('/Mukim', [MukimController::class, 'store'])->name('mukim.store');
        Route::get('/Mukim/{id}', [MukimController::class, 'edit'])->name('mukim.edit');
        Route::post('/ActivateMukim', [MukimController::class, 'activate'])->name('mukim.ActivateMukim'); 
        Route::post('/DeactivateMukim', [MukimController::class, 'deactivate'])->name('mukim.DeactivateMukim');
        Route::post('/Parlimen', [ParlimenController::class, 'store'])->name('parliment.store');
        Route::get('/Parlimen/{id}', [ParlimenController::class, 'edit'])->name('parliment.edit');
        Route::post('/ActivateParlimen', [ParlimenController::class, 'activate'])->name('mukim.ActivateParlimen'); 
        Route::post('/DeactivateParlimen', [ParlimenController::class, 'deactivate'])->name('mukim.DeactivateParlimen');
        Route::post('/Dun', [DunController::class, 'store'])->name('dun.store');
        Route::get('/Dun/{id}', [DunController::class, 'edit'])->name('dun.edit');
        Route::post('/ActivateDun', [DunController::class, 'activate'])->name('dun.ActivateDun'); 
        Route::post('/DeactivateDun', [DunController::class, 'deactivate'])->name('dun.DeactivateDun');
        Route::get('/kementerian/list', [KementerianController::class, 'list'])->name('kementerian.list');
        Route::post('/Kementerian', [KementerianController::class, 'store'])->name('kementerian.store');        
        Route::get('/Kementerian/{id}', [KementerianController::class, 'edit'])->name('kementerian.edit');
        Route::post('/ActivateKementerian', [KementerianController::class, 'activate'])->name('kementerian.ActivateKementerian'); 
        Route::post('/DeactivateKementerian', [KementerianController::class, 'deactivate'])->name('kementerian.DeactivateKementerian');
        Route::post('/Gred', [GredJawatanController::class, 'store'])->name('gred.store');
        Route::get('/Gred/{id}', [GredJawatanController::class, 'edit'])->name('gred.edit');
        Route::post('/ActivateGred', [GredJawatanController::class, 'activate'])->name('gred.ActivateGred'); 
        Route::post('/DeactivateGred', [GredJawatanController::class, 'deactivate'])->name('gred.DeactivateGred');
        
        Route::post('/Bahagian', [BahagianController::class, 'store'])->name('bahagian.store'); 
        Route::post('/ActivateBahagian', [BahagianController::class, 'activate'])->name('bahagian.ActivateBahagian'); 
        Route::post('/DeactivateBahagian', [BahagianController::class, 'deactivate'])->name('bahagian.DeactivateBahagian'); 
        Route::get('/Bahagian/{id}', [BahagianController::class, 'edit'])->name('bahagian.edit'); 
        
        Route::post('/Jabatan', [JabatanController::class, 'store'])->name('jabatan.store');
        Route::get('/Jabatan/{id}', [JabatanController::class, 'edit'])->name('jabatan.edit'); 
        Route::post('/ActivateJabatan', [JabatanController::class, 'activate'])->name('negeri.ActivateJabatan'); 
        Route::post('/DeactivateJabatan', [JabatanController::class, 'deactivate'])->name('negeri.DeactivateJabatan'); 
        Route::post('/Jawatan', [JawatanController::class, 'store'])->name('jawatan.store');
        Route::get('/Jawatan/{id}', [JawatanController::class, 'edit'])->name('jawatan.edit'); 
        Route::post('/ActivateJawatan', [JawatanController::class, 'activate'])->name('jawatan.ActivateJawatan'); 
        Route::post('/DeactivateJawatan', [JawatanController::class, 'deactivate'])->name('jawatan.DeactivateJawatan');

        Route::get('/BahagianEpu/list', [BahagianEpuController::class, 'list'])->name('bahagian.epu.list');
        Route::post('/BahagianEpu', [BahagianEpuController::class, 'store'])->name('bahagian.epu.store'); 
        Route::get('/BahagianEpu/{id}', [BahagianEpuController::class, 'edit'])->name('bahagian.epu.edit');
        Route::post('/ActivateBahagianEpu', [BahagianEpuController::class, 'activate'])->name('jawatan.ActivateBahagianEpu'); 
        Route::post('/DeactivateBahagianEpu', [BahagianEpuController::class, 'deactivate'])->name('jawatan.DeactivateBahagianEpu');
        

        Route::get('/SektorUtama/list', [SektorUtamaController::class, 'list'])->name('sektor.utama.list');
        Route::post('/SektorUtama', [SektorUtamaController::class, 'store'])->name('sektor.utama.store'); 
        Route::get('/SektorUtama/{id}', [SektorUtamaController::class, 'edit'])->name('sektor.utama.edit');
        Route::post('/ActivateSektorUtama', [SektorUtamaController::class, 'activate'])->name('sektor.ActivateSektorUtama'); 
        Route::post('/DeactivateSektorUtama', [SektorUtamaController::class, 'deactivate'])->name('sektor.DeactivateSektorUtama');

        Route::get('/Sektor/list', [SektorController::class, 'list'])->name('sektor.list');
        Route::post('/Sektor', [SektorController::class, 'store'])->name('sektor.store');
        Route::get('/Sektor/{id}', [SektorController::class, 'edit'])->name('sektor.edit');
        Route::post('/ActivateSektor', [SektorController::class, 'activate'])->name('sektor.ActivateSektor'); 
        Route::post('/DeactivateSektor', [SektorController::class, 'deactivate'])->name('sektor.DeactivateSektor');

        Route::get('/SubSektor/list', [SubSektorController::class, 'list'])->name('sektor.sub.list');
        Route::post('/SubSektor', [SubSektorController::class, 'store'])->name('sektor.sub.store'); 
        Route::get('/SubSektor/{id}', [SubSektorController::class, 'edit'])->name('sektor.sub.edit');
        Route::post('/ActivateSubSektor', [SubSektorController::class, 'activate'])->name('sektor.ActivateSubSektor'); 
        Route::post('/DeactivateSubSektor', [SubSektorController::class, 'deactivate'])->name('sektor.DeactivateSubSektor');


        Route::get('/SubSektor/fixed', [SubSektorController::class, 'fixedValue'])->name('sektor.sub.fixed');

        Route::get('/Kategori/list', [JenisKategoriController::class, 'list'])->name('kategori.list'); 
        Route::post('/Kategori', [JenisKategoriController::class, 'store'])->name('kategori.store'); 
        Route::get('/Kategori/{id}', [JenisKategoriController::class, 'edit'])->name('kategori.edit');
        Route::post('/ActivateKategori', [JenisKategoriController::class, 'activate'])->name('kategori.ActivateKategori'); 
        Route::post('/DeactivateKategori', [JenisKategoriController::class, 'deactivate'])->name('kategori.DeactivateKategori');

        Route::get('/SubKategori/list', [JenisSubKategoriController::class, 'list'])->name('kategori.sub.list'); 
        Route::post('/SubKategori', [JenisSubKategoriController::class, 'store'])->name('kategori.sub.store'); 
        Route::get('/SubKategori/{id}', [JenisSubKategoriController::class, 'edit'])->name('kategori.sub.edit');
        Route::post('/ActivatesubKategori', [JenisSubKategoriController::class, 'activate'])->name('kategori.ActivatesubKategori'); 
        Route::post('/DeactivatesubKategori', [JenisSubKategoriController::class, 'deactivate'])->name('kategori.DeactivatesubKategori');

        Route::post('/modulelist', [Pentadbir_Modules_Controller::class, 'modulelist'])->name('modulelist.list'); 
        Route::post('/modul-link', [PSDA_Controller::class, 'store'])->name('modul-link.link');  
        Route::post('/editModul', [PSDA_Controller::class, 'edit'])->name('editModul.link');       
        Route::post('/update', [PSDA_Controller::class, 'update'])->name('update.link'); 
        Route::post('/activateModul', [PSDA_Controller::class, 'activate'])->name('update.link'); 
        Route::post('/deactivateModul', [PSDA_Controller::class, 'deactivate'])->name('update.link'); 
        Route::post('/Pds', [PDS_Controller::class, 'dataStore'])->name('pds.dataStore'); 

        Route::post('/rmk_strategi', [rmk_Controller::class, 'store'])->name('strategi.store'); 
        Route::post('/rmk_strategi_edit/{id}', [rmk_Controller::class, 'edit'])->name('strategi.edit');
        Route::post('/rmk_update_strategi', [rmk_Controller::class, 'update'])->name('strategi.update');
        Route::post('/activate_strategi', [rmk_Controller::class, 'activate'])->name('strategi.update');
        Route::post('/deactivate_strategi', [rmk_Controller::class, 'deactivate'])->name('strategi.update');
        
        
         
        Route::post('/rmk_obb', [RmkObbController::class, 'store'])->name('obb.store'); 
        Route::post('/rmk_obb_edit/{id}', [RmkObbController::class, 'edit'])->name('obb.edit');
        Route::post('/rmk_obb_update', [RmkObbController::class, 'update'])->name('obb.update');
        Route::post('/activate_obb', [RmkObbController::class, 'activate'])->name('obb.update');
        Route::post('/deactivate_obb', [RmkObbController::class, 'deactivate'])->name('obb.update');


        Route::get('/units/list', [UnitsController::class, 'listunits'])->name('UnitsDetails.Getunits');
        Route::get('/units/list/{id}', [UnitsController::class, 'listunits'])->name('UnitsDetails.Getunits');
        Route::post('/unit_update', [UnitsController::class, 'updateunits'])->name('UnitsDetails.Updateunits');
        Route::post('/unit_add', [UnitsController::class, 'addunits'])->name('UnitsDetails.addunits');
        Route::post('/update_status', [UnitsController::class, 'updateStatus'])->name('UnitsDetails.updateStatus');

        Route::get('/komponen/list', [KewanganKomponenController::class, 'listkomponen'])->name('Komponen.listkomponen');
        Route::get('/komponen/list/{id}', [KewanganKomponenController::class, 'listkomponen'])->name('Komponen.listkomponen');
        Route::post('/komponen_update', [KewanganKomponenController::class, 'updatekomponen'])->name('Komponen.updatekomponen');
        Route::post('/komponen_add', [KewanganKomponenController::class, 'addkomponen'])->name('Komponen.addkomponen');
        Route::post('/komponen_update_status', [KewanganKomponenController::class, 'updateKomponenStatus'])->name('Komponen.updateKomponenStatus');


        Route::post('/pejabat_update', [ProjectController::class, 'updatepejabat'])->name('Pejabat.updatepejabat');
        Route::post('/pejabat_add', [ProjectController::class, 'addpejabat'])->name('Pejabat.addpejabat');
        Route::post('/pejabat_update_status', [ProjectController::class, 'updatePejabatStatus'])->name('Pejabat.updatePejabatStatus');


        Route::post('/Skop', [SkopController::class, 'store'])->name('skop.store');
        Route::get('/Skop/{id}', [SkopController::class, 'edit'])->name('skop.edit');
        Route::put('/Skop', [SkopController::class, 'update'])->name('skop.update');
        Route::post('/ActivateSkop', [SkopController::class, 'activate'])->name('skop.ActivateSkop'); 
        Route::post('/DeactivateSkop', [SkopController::class, 'deactivate'])->name('skop.DeactivateSkop');



        Route::post('/sub_skop', [SubSkopController::class, 'store'])->name('subskop.store');    
        Route::get('/sub_skop/{id}', [SubSkopController::class, 'edit'])->name('subskop.edit');
        Route::post('/ActivateSubSkop', [SubSkopController::class, 'activate'])->name('subskop.ActivateSubSkop'); 
        Route::post('/DeactivateSubSKop', [SubSkopController::class, 'deactivate'])->name('subskop.DeactivateSubSKop');


        Route::post('/options', [LookupOptionsController::class, 'store'])->name('lookup.options.store');    
        Route::get('/options/{id}', [LookupOptionsController::class, 'edit'])->name('lookup.options.edit');
        Route::post('/ActivateLookupOptions', [LookupOptionsController::class, 'activate'])->name('lookup.options.Activate'); 
        Route::post('/DeactivateLookupOptions', [LookupOptionsController::class, 'deactivate'])->name('lookup.options.Deactivate');

        Route::get('/strategi/list', [RmkStrategiController::class, 'list'])->name('Strategi.list');
        Route::get('/StrategiData/{id}', [RmkStrategiController::class, 'list'])->name('Strategi.list');
        Route::post('/StrategiStore', [RmkStrategiController::class, 'store'])->name('Strategi.list');
        Route::post('/ActivateStrategi', [RmkStrategiController::class, 'activate'])->name('lookup.options.Activate'); 
        Route::post('/DeactivateStrategi', [RmkStrategiController::class, 'deactivate'])->name('lookup.options.Deactivate');



        Route::get('/DeliverableHeading/list', [DeliverableHeadingController::class, 'list'])->name('deliverable.heading.list');  
        // Route::get('/DeliverableHeading/list_parlimen', [DeliverableHeadingController::class, 'listPalimen'])->name('deliverable.heading.listPalimen');      
        Route::post('/DeliverableHeading', [DeliverableHeadingController::class, 'store'])->name('deliverable.heading.store');
        Route::get('/DeliverableHeading/{id}', [DeliverableHeadingController::class, 'edit'])->name('deliverable.heading.edit');
        Route::post('/ActivateDeliverableHeading', [DeliverableHeadingController::class, 'activate'])->name('deliverable.heading.activate'); 
        Route::post('/DeactivateDeliverableHeading', [DeliverableHeadingController::class, 'deactivate'])->name('deliverable.heading.deactivate');

        Route::get('/Deliverable/list', [DeliverableController::class, 'list'])->name('deliverable.list');  
        // Route::get('/Deliverable/list_parlimen', [DeliverableController::class, 'listPalimen'])->name('deliverable.listPalimen');      
        Route::post('/Deliverable', [DeliverableController::class, 'store'])->name('deliverable.store');
        Route::get('/Deliverable/{id}', [DeliverableController::class, 'edit'])->name('deliverable.edit');
        Route::post('/ActivateDeliverable', [DeliverableController::class, 'activate'])->name('deliverable.activate'); 
        Route::post('/DeactivateDeliverable', [DeliverableController::class, 'deactivate'])->name('deliverable.deactivate');

        Route::get('/Belanja_Mengurus_Skop/list', [BelanjaMengurusSkopController::class, 'list'])->name('belanganmengurusskop.list');  
        Route::post('/Belanja_Mengurus_Skop', [BelanjaMengurusSkopController::class, 'store'])->name('belanganmengurusskop.store');
        Route::get('/Belanja_Mengurus_Skop/{id}', [BelanjaMengurusSkopController::class, 'edit'])->name('belanganmengurusskop.edit');
        Route::post('/ActivateBelanja_Mengurus_Skop', [BelanjaMengurusSkopController::class, 'activate'])->name('belanganmengurusskop.activate'); 
        Route::post('/DeactivateBelanja_Mengurus_Skop', [BelanjaMengurusSkopController::class, 'deactivate'])->name('belanganmengurusskop.deactivate');
        
        Route::get('/Belanja_Mengurus_Sub_Skop/list', [BelanjaMengurusSubSkopController::class, 'list'])->name('belanganmengurussubskop.list');  
        Route::post('/Belanja_Mengurus_Sub_Skop', [BelanjaMengurusSubSkopController::class, 'store'])->name('belanganmengurussubskop.store');
        Route::get('/Belanja_Mengurus_Sub_Skop/{id}', [BelanjaMengurusSubSkopController::class, 'edit'])->name('belanganmengurussubskop.edit');
        Route::post('/ActivateBelanja_Mengurus_Sub_Skop', [BelanjaMengurusSubSkopController::class, 'activate'])->name('belanganmengurussubskop.activate'); 
        Route::post('/DeactivateBelanja_Mengurus_Sub_Skop', [BelanjaMengurusSubSkopController::class, 'deactivate'])->name('belanganmengurussubskop.deactivate');

        Route::get('/nama_agensi/list', [MasterAgensiController::class, 'list'])->name('nama_agensi.list');  
        Route::post('/nama_agensi', [MasterAgensiController::class, 'store'])->name('nama_agensi.store');
        Route::get('/nama_agensi/{id}', [MasterAgensiController::class, 'edit'])->name('nama_agensi.edit');
        Route::post('/Activatenama_agensi', [MasterAgensiController::class, 'activate'])->name('nama_agensi.activate'); 
        Route::post('/Deactivatenama_agensi', [MasterAgensiController::class, 'deactivate'])->name('nama_agensi.deactivate');
    });