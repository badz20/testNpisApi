<?php

use App\Http\Controllers\Api\AgensiController;
use App\Http\Controllers\Api\ProjectNegeriLokasController;
use App\Http\Controllers\Api\ProjectController;
use App\Http\Controllers\Api\RmkObbController;
use App\Http\Controllers\Api\RmkObbPageController;
use App\Http\Controllers\Api\RmkSdgController;
use App\Http\Controllers\Api\RmkSasaranController;
use App\Http\Controllers\Api\RmkIndikatoriController;
use App\Http\Controllers\Api\RmkStrategiController;
use App\Http\Controllers\Api\ProjectCIController;
use App\Http\Controllers\Api\OutputUnitController;
use App\Http\Controllers\Api\OutputPageController;
use App\Http\Controllers\Api\OutcomeController;
use App\Http\Controllers\Api\DokumenLampiranController;
use App\Http\Controllers\Api\ProjectKpiController;
use App\Http\Controllers\Api\KewanganSkopController;
use App\Http\Controllers\Api\KewanganSkopSillingController;
use App\Http\Controllers\Api\KewanganBayaranSukuTahunanController;
use App\Http\Controllers\Api\KewanganMaklumatPeruntukanController;
use App\Http\Controllers\Api\KewanganProjekDetailsController;
use App\Http\Controllers\Api\KewanganKomponenController;
use App\Http\Controllers\Api\UnitsController;
use App\Http\Controllers\Api\TableForCalculationController;
use App\Http\Controllers\Api\GetSkopOptionsController;
use App\Http\Controllers\Api\GetSubSkopOptionsController;
use App\Http\Controllers\Api\PptController;
use App\Http\Controllers\Api\KementerianController;
use App\Http\Controllers\Api\ConvertPptToPdf;
use App\Http\Controllers\Api\vae_Controller;
use App\Http\Controllers\Api\Permohonan\KewanganBelanjaMengurusController;

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ValueManagementController;
use App\Models\ProjectNegeriLokas;

Route::name('api.project.')
    ->middleware('auth:sanctum')
    ->prefix('project')
    ->group(function () {
        Route::get('/projects', [ProjectController::class, 'index'])->name('project.index');
        Route::get('/projects/{id}', [ProjectController::class, 'projectDraftEdit'])->name('project.draft.edit');
        Route::get('/draftDetails', [ProjectController::class, 'draftCreate'])->name('project.draft.details');
        Route::get('/jenis_sub_kategori/{id}', [ProjectController::class, 'getJenisSubKategori'])->name('project.jenis.sub.kategori');
        Route::get('/sektor_utama/{id}', [ProjectController::class, 'getSektorUtama'])->name('project.sektor.utama');
        Route::get('/sektor/{id}', [ProjectController::class, 'getSektor'])->name('project.sektor');
        Route::get('/sektor_sub/{id}', [ProjectController::class, 'getSektorSub'])->name('project.sektor.sub');

        Route::get('/projects-with-userid', [ProjectController::class, 'projectWithUserId'])->name('project.projectWithUserId');
        Route::post('/cancel_project', [ProjectController::class, 'cancelProject'])->name('project.cancelProject');
        Route::post('/aprove_project', [ProjectController::class, 'approveProject'])->name('project.approveProject');
        Route::get('/get-perakuan-data', [ProjectController::class, 'getPerakuan'])->name('project.getPerakuan');
        Route::get('/get-semak-project-list', [ProjectController::class, 'getSemakProjectList'])->name('project.getSemakProjectList');
        Route::get('/get-salin-project-list', [ProjectController::class, 'getSalinProjectList'])->name('project.getSalinProjectList');
        Route::get('/get-pengesahan-project-list', [ProjectController::class, 'getPengesahanProjectList'])->name('project.getPengesahanProjectList');
        Route::get('/get-peraku-project-list', [ProjectController::class, 'getPerakuProjectList'])->name('project.getPerakuProjectList');


        Route::post('/set_approve', [ProjectController::class, 'setApprove'])->name('project.setApprove');
        Route::post('/update_project_status', [ProjectController::class, 'updateProjectStatus'])->name('project.updateProjectStatus');
        Route::get('/rmkData', [ProjectController::class, 'rmkDataList'])->name('project.rmkData');
        Route::post('/filter-projects-of-daerah', [ProjectController::class, 'FilterprojectOfDaerah'])->name('project.FilterprojectOfDaerah');
        Route::post('/filter-projects-of-negeri', [ProjectController::class, 'FilterprojectOfNegeri'])->name('project.FilterprojectOfNegeri');
        Route::post('/filter-projects-of-bahagian', [ProjectController::class, 'FilterprojectOfBahagian'])->name('project.FilterprojectOfBahagian');
        Route::post('/filter-projects-of-bkor', [ProjectController::class, 'FilterprojectOfBkor'])->name('project.FilterprojectOfBkor');
        Route::get('/get-dashboard-data', [ProjectController::class, 'getDashboardData'])->name('project.getDashboardData');
        Route::get('/get-project-log', [ProjectController::class, 'getProjectLog'])->name('project.getProjectLog');
        Route::get('/get-login-log', [ProjectController::class, 'getLoginLog'])->name('project.getLoginLog');
        Route::post('/send_update_request', [ProjectController::class, 'sendUpdateRequest'])->name('project.sendUpdateRequest');
        Route::post('/set_priority', [ProjectController::class, 'setPriority'])->name('project.setPriority');
        Route::post('/sususan_status_update', [ProjectController::class, 'setSusunanStatus'])->name('project.setSusunanStatus');
        Route::post('/hanter_project_data', [ProjectController::class, 'HanterProjectData'])->name('project.HanterProjectData');

        
        Route::get('/active-indikatori-details/{id}', [RmkIndikatoriController::class, 'list_rmk'])->name('Rmk.Indikatori.Indikatoridetails');
        Route::get('/active-indikatori-details/', [RmkIndikatoriController::class, 'list_rmk'])->name('Rmk.Indikatori.Indikatoridetails');
        Route::get('/active-sasaran-details/{id}', [RmkSasaranController::class, 'list_rmk'])->name('Rmk.Sasaran.Sasarandetails');
        Route::get('/aktivity-details', [RmkObbController::class, 'list'])->name('Rmk.Obb.Aktivitydetails');
        Route::get('/aktivity-details/{id}', [RmkObbController::class, 'list'])->name('Rmk.Obb.Aktivitydetails');
        Route::get('/sdg-details', [RmkSdgController::class, 'list'])->name('Rmk.Sdg.SDGdetails');
        Route::get('/sasaran-details/{id}', [RmkSasaranController::class, 'list'])->name('Rmk.Sasaran.Sasarandetails');
        Route::get('/rmk-sasaran-details/{id}/{projectid}', [RmkObbPageController::class, 'getsasaranlist'])->name('Rmk.Sasaran.Sasarandetails');
        Route::get('/rmk-indikatori-details/{sdgid}/{projectid}', [RmkObbPageController::class, 'getindikatorilist'])->name('Rmk.Sasaran.Sasarandetails');
        Route::post('/rmksasaran-store/{id}', [RmkSasaranController::class, 'store'])->name('Rmk.Sasaran.Sasarandetails');
        Route::post('/rmkindikatori-store/{id}', [RmkIndikatoriController::class, 'store'])->name('Rmk.Indikatori.Indikatoridetails');
        Route::get('/indikatori-details/{id}', [RmkIndikatoriController::class, 'list'])->name('Rmk.Indikatori.Indikatoridetails');
        Route::get('/strategi-details/{id}', [RmkStrategiController::class, 'list'])->name('Rmk.Strategi.Strategidetails');
        Route::get('/strategi-details', [RmkStrategiController::class, 'list'])->name('Rmk.Strategi.Strategidetails');
        Route::post('/brif', [ProjectController::class, 'storeBrifProject'])->name('project.brif.store');
        Route::post('/brif/update', [ProjectController::class, 'updateBrifProject'])->name('project.brif.update');
       
        Route::post('/vae_data', [vae_Controller::class, 'vae_data']);
        Route::get('/fetch_vae_data/{id}', [vae_Controller::class, 'fetch_vae_data']);
        Route::post('/update_vae_data', [vae_Controller::class, 'update_vae_data']);
        
        Route::post('/update-negeri', [ProjectNegeriLokasController::class, 'updateNegeri'])->name('projectnegerilokas.update'); 
        
        Route::get('/negeri-details/{id}', [ProjectNegeriLokasController::class, 'negeriDetails'])->name('projectnegerilokas.details');
        Route::post('/add-gambar-image', [ProjectNegeriLokasController::class, 'addDocument'])->name('projectnegerilokas.adddocument'); 
        Route::post('/delete-gambar-image', [ProjectNegeriLokasController::class, 'deleteDocument'])->name('projectnegerilokas.deletedocument'); 
        Route::get('/rmkobb-details', [RmkObbController::class, 'list'])->name('Rmk.Obb.Aktivitydetails');
        Route::get('/negeri-details-ringkasan/{id}', [ProjectNegeriLokasController::class, 'negeriDetailsforringkasan'])->name('projectnegerilokas.negeriDetailsforringkasan');
        Route::get('/negeri_dokumen_download', [ProjectNegeriLokasController::class, 'downloadDokumen'])->name('projectnegerilokas.downloadDokumen');

        Route::post('/rmkobbpage-details/{id}', [RmkObbPageController::class, 'store'])->name('Rmk.Obb.Pagedetails');
        
        Route::get('/rmkobbpage-details/{id}', [RmkObbPageController::class, 'list'])->name('Rmk.Obb.Getpagedetails');

        Route::get('/rmkobbpage-update/{id}', [RmkObbPageController::class, 'updatermk'])->name('Rmk.Obb.Updatepagedetails');
        Route::get('/unit-details/{id}', [OutputUnitController::class, 'updatermk'])->name('Rmk.Obb.Updatepagedetails');
        Route::get('/unit-details', [OutputUnitController::class, 'list'])->name('Output.Outcome.Unitdetails');

        Route::get('/ci/{id}', [ProjectCIController::class, 'index'])->name('project.ci.index');
        Route::post('/ci', [ProjectCIController::class, 'store'])->name('project.ci.store');

        Route::post('/outputpage-details/{id}', [OutputPageController::class, 'store'])->name('Output.CreatePagedetails');
        Route::get('/outputpage-details/{id}', [OutputPageController::class, 'index'])->name('Output.Getpagedetails');
        Route::get('/outputpage-update/{id}', [OutputPageController::class, 'updateoutputdata'])->name('Output.Updatepagedetails');

        Route::post('/outcomepage-details/{id}', [OutcomeController::class, 'store'])->name('Outcome.CreatePagedetails');
        Route::get('/outcomepage-details/{id}', [OutcomeController::class, 'list'])->name('Outcome.Getpagedetails');
        Route::get('/outcomepage-update/{id}', [OutcomeController::class, 'updateoutputdata'])->name('Outcome.Updatepagedetails');

        Route::get('/dokumen-lampiran/{id}', [DokumenLampiranController::class, 'list'])->name('DokumenLampiran.list');
        Route::post('/dokumen-lampiran-upload', [DokumenLampiranController::class, 'addDocumentlampiran'])->name('DokumenLampiran.addDocumentlampiran');
        Route::post('/dokumen-upload', [DokumenLampiranController::class, 'addDocument'])->name('DokumenLampiran.addDocument');
        Route::post('/add-lain-document', [DokumenLampiranController::class, 'addLainDocument'])->name('DokumenLampiran.addLainDocument');
        Route::post('/delete-lampiran-image', [DokumenLampiranController::class, 'deleteDocumentlampiran'])->name('DokumenLampiran.deletedocumentlampiran');
        Route::get('/dokumen_download', [DokumenLampiranController::class, 'downloadImg'])->name('DokumenLampiran.dokumen_download');
        Route::get('/download-dokumen-format', [DokumenLampiranController::class, 'docFormat'])->name('DokumenLampiran.docFormat');
        Route::get('/get-dokumen-lain-data', [DokumenLampiranController::class, 'getDocumentLainData'])->name('DokumenLampiran.getDocumentLainData');

        Route::post('/add-project-kpi', [ProjectKpiController::class, 'addProjectKpi'])->name('projectKPI.addProjectKpi'); 
        Route::get('/list-project-kpi/{id}', [ProjectKpiController::class, 'listProjectKpi'])->name('projectKPI.listProjectKpi'); 
        Route::post('/delete-project-kpi', [ProjectKpiController::class, 'deleteProjectKpi'])->name('projectKPI.deleteProjectKpi'); 
        Route::get('/get-project-kpi', [ProjectKpiController::class, 'getProjectKpi'])->name('projectKPI.getProjectKpi'); 
        Route::post('/update-project-kpi', [ProjectKpiController::class, 'updateProjectKpi'])->name('projectKPI.updateProjectKpi'); 

        Route::post('/kewanganskop-details/{id}', [KewanganSkopController::class, 'store'])->name('Kewangan.CreatePage');
        Route::get('/kewanganskopsection-details/{id}', [KewanganSkopController::class, 'getprojectskop'])->name('Kewangan.GetSkopPage');
        Route::get('/get-kewangan-negeri-data/{id}', [KewanganKomponenController::class, 'getKewanganNegeriData'])->name('Kewangan.getKewanganNegeriData');
        Route::post('/add-kewangan-negeri-data', [KewanganKomponenController::class, 'addKewanganNegeriData'])->name('Kewangan.addKewanganNegeriData');


        Route::get('/getrollingplan-details/{id}', [KewanganProjekDetailsController::class, 'listrollingplan'])->name('Kewangan.Rollingplan');

        Route::get('/get-skop-for-kewangan/{id}', [KewanganSkopSillingController::class, 'getProjectSkopForKewangan'])->name('KewanganSkopController.getProjectSkopForKewangan');
        Route::post('/add-skop-for-kewangan/{id}', [KewanganSkopSillingController::class, 'addProjectSkopForKewangan'])->name('KewanganSkopController.addProjectSkopForKewangan');
        Route::get('/get-bayaran-suku-for-kewangan/{id}', [KewanganBayaranSukuTahunanController::class, 'getBayaranSukuForKewangan'])->name('KewanganBayaranSukuTahunanController.getBayaranSukuForKewangan');
        Route::post('/add-bayaran-suku-for-kewangan/{id}', [KewanganBayaranSukuTahunanController::class, 'addBayaranSukuForKewangan'])->name('KewanganBayaranSukuTahunanController.addBayaranSukuForKewangan');
        Route::get('/get-maklumat-peruntukan-for-kewangan/{id}', [KewanganMaklumatPeruntukanController::class, 'getMaklumatPeruntukan'])->name('KewanganMaklumatPeruntukanController.getMaklumatPeruntukan');
        Route::post('/add-maklumat-peruntukan/{id}', [KewanganMaklumatPeruntukanController::class, 'addMaklumatPeruntukan'])->name('KewanganMaklumatPeruntukanController.addMaklumatPeruntukan');
        Route::post('/add-Belanja-peruntukan/{id}', [KewanganMaklumatPeruntukanController::class, 'addMaklumatBelenja'])->name('KewanganMaklumatPeruntukanController.addMaklumatBelenja');

        Route::post('/addkewanganprojek-details/{id}', [KewanganProjekDetailsController::class, 'store'])->name('KewanganProjekDetails.CreateKewanganProjekDetailsPage');
        Route::get('/getkewanganprojek-details/{id}', [KewanganProjekDetailsController::class, 'list'])->name('KewanganProjekDetails.GetKewanganProjekDetailsPage');
        Route::get('/getkewangankomponen-details/{id}', [KewanganKomponenController::class, 'list'])->name('KewanganKomponenDetails.GetKewanganKomponenDetailsPage');
        Route::get('/getkewangankomponen-details', [KewanganKomponenController::class, 'list'])->name('KewanganKomponenDetails.GetKewanganKomponenDetailsPage');
        Route::get('/getunits', [UnitsController::class, 'list'])->name('UnitsDetails.Getunits');
    
        Route::get('/getcalculation-details', [TableForCalculationController::class, 'list'])->name('project.listkewangancalculations');

        Route::get('/getskopoptions-details', [GetSkopOptionsController::class, 'list'])->name('project.listskopoptions');
        Route::get('/getsubskopoptions-details', [GetSubSkopOptionsController::class, 'list'])->name('project.listsubskopoptions');

        Route::get('/ppt-download/{id}', [PptController::class, 'createPPt'])->name('project.ppt.download');
        Route::post('/convert-pptTopdf', [ConvertPptToPdf::class, 'convertIntoPdf'])->name('project.pptTopdf.convert');
        
        
        Route::get('/SelenggaraKodProjek', [KementerianController::class, 'KementerianController'])->name('project.KementerianController');
        Route::post('/kod_kementerian', [KementerianController::class, 'kod_kementerian'])->name('project.kod_kementerian');
        Route::post('/updateKementerian', [KementerianController::class, 'updateKementerian'])->name('project.updateKementerian');
        Route::get('/dataKementerian', [KementerianController::class, 'dataKementerian'])->name('project.dataKementerian');


        Route::get('/project-completed/{id}', [ProjectController::class, 'sectionCompleted'])->name('project.section.complete');


        Route::get('/makmal_list', [ValueManagementController::class, 'index'])->name('VM.index');
        Route::get('/makmal_list_VA', [ValueManagementController::class, 'makmal_list_VA'])->name('VM.makmal_list_VA');
        Route::get('/brif_project_details/{kod_projek}', [ValueManagementController::class, 'brifProjectDetails'])->name('VM.brifProjectDetails');
        Route::post('/filter-projects-of-brif-makmal_mini', [ValueManagementController::class, 'FilterbrifProjectMakmalMini'])->name('VM.FilterbrifProjectMakmalMini');
        Route::post('/filter-projects-of-brif-makmal', [ValueManagementController::class, 'FilterbrifProjectMakmal'])->name('VM.FilterbrifProjectMakmal');
        Route::post('/filter-projects-va', [ValueManagementController::class, 'FilterbrifProjectMakmalVa'])->name('VM.FilterbrifProjectMakmalMini');
        Route::post('/filter-projects-vr', [ValueManagementController::class, 'FilterbrifProjectMakmalVr'])->name('VM.FilterbrifProjectMakmalMini');
        Route::post('/filter-projects-ve', [ValueManagementController::class, 'FilterbrifProjectMakmalVe'])->name('VM.FilterbrifProjectMakmalMini');
        Route::post('/filter-projects-mini_va', [ValueManagementController::class, 'FilterbrifProjectMakmalMiniVA'])->name('VM.FilterbrifProjectMakmalMiniVA');


        Route::post('/storeKalenderData', [ValueManagementController::class, 'storeKalenderData'])->name('VM.storeKalenderData');
        Route::get('/kalenderData/{id}/{type}/{user_id}/{user_type}', [ValueManagementController::class, 'kalenderData'])->name('VM.kalenderData');
        Route::get('/get_kalenderData', [ValueManagementController::class, 'kalenderDataDetails'])->name('VM.kalenderDataDetails');

        Route::get('/makmal_list_VE', [ValueManagementController::class, 'makmal_list_VE'])->name('VM.makmal_list_VE');
        Route::get('/makmal_list_VR', [ValueManagementController::class, 'makmal_list_VR'])->name('VM.makmal_list_VR');
        Route::get('/makmal_list_mini_va', [ValueManagementController::class, 'makmal_list_mini_va'])->name('VM.makmal_list_mini_va');



        //Route::get('/kalenderData', [ValueManagementController::class, 'kalenderData'])->name('VM.kalenderData');
        Route::get('/get-fasilitator-list', [ValueManagementController::class, 'getFasilitatorList'])->name('VM.getFasilitatorList');
        Route::post('/create_fasilitator', [ValueManagementController::class, 'addFasilitator'])->name('VM.addFasilitator');
        Route::get('/get-fasilitator-list/{id}', [ValueManagementController::class, 'getFasilitatorListById'])->name('VM.getFasilitatorList');
        Route::post('/update_fasilitator', [ValueManagementController::class, 'updateFasilitator'])->name('VM.updateFasilitator');
        Route::post('/set-as-makmal-va', [ValueManagementController::class, 'setAsMakmalVa'])->name('VM.setAsMakmalVa');
        Route::post('/pelakasanan', [ValueManagementController::class, 'pelakasanan'])->name('VM.pelakasanan');
        Route::get('/maklumat_pelakasanaan', [ValueManagementController::class, 'maklumat_pelakasanaan_makmal'])->name('VM.maklumat_pelakasanaan_makmal');
        Route::post('/update_status_perlaksanaan', [ValueManagementController::class, 'update_status_perlaksanaan'])->name('VM.update_status_perlaksanaan');
        Route::post('/tandatanganData', [ValueManagementController::class, 'tandatanganData'])->name('VM.tandatanganData');
        Route::get('/va_tandatanganData', [ValueManagementController::class, 'va_tandatanganData'])->name('VM.va_tandatanganData');
        Route::post('/VRtandatanganData', [ValueManagementController::class, 'VRtandatanganData'])->name('VM.VRtandatanganData');
        Route::post('/VRformData', [ValueManagementController::class, 'VRformData'])->name('VM.VRformData');
        Route::get('/vr_tandatanganData/{kod_projek}', [ValueManagementController::class, 'vr_tandatanganData'])->name('VM.vr_tandatanganData');
        Route::get('/kemukafileDownload', [DokumenLampiranController::class, 'kemukafileDownload'])->name('DokumenLampiran.kemukafileDownload');
        Route::get('/terimafileDownload', [DokumenLampiranController::class, 'terimafileDownload'])->name('DokumenLampiran.terimafileDownload');
        Route::get('/previewfile', [DokumenLampiranController::class, 'previewfile'])->name('DokumenLampiran.previewfile');
        Route::get('/get_project_data/{kod_projek}', [ValueManagementController::class, 'getProjectData'])->name('VM.getProjectData');
        Route::get('/vr_filedownload', [DokumenLampiranController::class, 'vr_filedownload'])->name('DokumenLampiran.vr_filedownload');

        Route::get('/mmpms_vr/{kod_projek}/{type}', [ValueManagementController::class, 'mmpms_vr'])->name('VM.mmpms_vr');
        

        Route::post('/pengeculian_update', [ValueManagementController::class, 'PengeculianUpdate'])->name('VM.PengeculianUpdate');
        Route::get('/get_pengeculian_data', [ValueManagementController::class, 'GetPengeculianData'])->name('VM.GetPengeculianData');
        Route::get('/preview_pengeculian_data', [ValueManagementController::class, 'previewPengeculianfile'])->name('VM.previewPengeculianfile');
        Route::post('/selesai_update', [ValueManagementController::class, 'selesaiUpdate'])->name('VM.selesaiUpdate');
        Route::post('/selesai', [ValueManagementController::class, 'selesai'])->name('VM.selesai');
        Route::post('/noc_update', [ValueManagementController::class, 'NocUpdate'])->name('VM.NocUpdate');
        Route::post('/update_penjidian_data', [ValueManagementController::class, 'updatePenjidianData'])->name('VM.updatePenjidianData');
        Route::post('/add_penjidian_data', [ValueManagementController::class, 'addPenjidianData'])->name('VM.addPenjidianData');
        Route::get('/get_penjidian_data', [ValueManagementController::class, 'getPenjidianData'])->name('VM.getPenjidianData');
        Route::get('/preview_penjilidanfile', [DokumenLampiranController::class, 'previewPenjilidanfile'])->name('DokumenLampiran.previewPenjilidanfile');
        Route::post('/tandakan_update', [ValueManagementController::class, 'tandakan_update'])->name('VM.tandakan_update');

        Route::post('/update-bayangan-data', [ProjectController::class, 'updateBayanganData'])->name('project.updateBayanganData');
        Route::post('/update-total-bayangan-data', [ProjectController::class, 'updateTotalBayangan'])->name('project.updateTotalBayangan');

        //kewangan belanja mengurus
        Route::post('/kewangan/belanja_mengurus', [KewanganBelanjaMengurusController::class, 'store'])->name('project.belanja.mengurus.store');
        Route::get('/kewangan/belanja_mengurus', [KewanganBelanjaMengurusController::class, 'index'])->name('project.belanja.mengurus.index');
    });