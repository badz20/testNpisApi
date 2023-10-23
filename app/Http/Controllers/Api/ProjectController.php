<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use \App\Models\refBahagian;
use \App\Models\BahagianEpuJpm;
use \App\Models\JenisKategori;
use \App\Models\JenisSubKategori;
use \App\Models\SektorUtama;
use \App\Models\Sektor;
use \App\Models\SubSektor;
use \App\Models\Project;
use \App\Models\ProjectRequestUpdateTracker;
use \App\Models\projectLog;
use \App\Models\RollingPlan;
use \App\Models\SkopProject;
use \App\Models\BahagianTerlibat;
use \App\Models\SkopOption;
use \App\Models\KewanganSkop;
use \App\Models\UserPeranan;
use \App\Model\MasterPeranan;
use \App\Models\User;
use \App\Models\Outcome;
use \App\Models\OutputPage;
use Illuminate\Support\Facades\Validator;
use \App\Models\PemberatProjeckBaharu;
use \App\Models\ProjectKajian;
use \App\Models\vae;
use \App\Models\PejabatProjek;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use \App\Notifications\SubmitProjectNotification;
use \App\Notifications\SendUpdationRequest;
use Jenssegers\Agent\Facades\Agent;
use Illuminate\Support\Facades\Log;
use \APP\Models\KewanganProjekDetails;
use App\Models\Role;
use \App\Models\totalBayangan;
use Illuminate\Support\Carbon;


class ProjectController extends Controller
{
    //

    public function index()
    {
        try {
            //code...
            $result = Project::with(['bahagianPemilik', 'jenisKategori'])->get();
            return response()->json([
                'code' => '200',
                'status' => 'Success',
                'data' => $result,
            ]);
        } catch (\Throwable $th) {
            logger()->error($th->getMessage());

            //------------ error log store and email --------------------

            $body = [
                'application_name' => env('APP_NAME'),
                'application_type' => Agent::isPhone(),
                'url' => request()->fullUrl(),
                'error_log' => $th->getMessage(),
                'error_code' => $th->getCode(),
                'ip_address' =>  request()->ip(),
                'user_agent' => request()->userAgent(),
                'email' => env('ERROR_EMAIL'),
            ];

            CallApi($body);

            //------------- end of store and email -----------------------

            return response()->json([
                'code' => '500',
                'status' => 'Failed',
                'error' => $th,
            ]);
        }
    }

    public function projectDraftEdit($id)
    {
        try {
            //code...
            $result = Project::whereId($id)
                ->with(['bahagianPemilik', 'jenisKategori', 'bahagianTerlibat', 'kajianProjects',])
                ->first();
            $result['skop_project'] = SkopProject::where('project_id', $id)->with('subskopProjects')->get();
            return response()->json([
                'code' => '200',
                'status' => 'Success',
                'data' => $result,
            ]);
        } catch (\Throwable $th) {
            logger()->error($th->getMessage());


            //------------ error log store and email --------------------

            $body = [
                'application_name' => env('APP_NAME'),
                'application_type' => Agent::isPhone(),
                'url' => request()->fullUrl(),
                'error_log' => $th->getMessage(),
                'error_code' => $th->getCode(),
                'ip_address' =>  request()->ip(),
                'user_agent' => request()->userAgent(),
                'email' => env('ERROR_EMAIL'),
            ];

            CallApi($body);

            //------------- end of store and email -----------------------

            return response()->json([
                'code' => '500',
                'status' => 'Failed',
                'error' => $th,
            ]);
        }
    }

    public function draftCreate()
    {
        try {
            $today = Carbon::now();

            if ($today->month == 11 && $today->day == 1) {
                $checkUpdate = RollingPlan::whereDate('updated_at', $today->format('Y-m-d'))->get();
    
                if ($checkUpdate->isEmpty()) {
                    // Check if more than 1 selectable item
                    $checkSelectableItem = RollingPlan::where('is_active', 1)->where('is_selectable', 1)->get();
                    
                    if($checkSelectableItem->count() > 1) {
                        $firstSelectableItem = $checkSelectableItem->first();
                        if ($firstSelectableItem) {
                            $firstSelectableItem->update(['is_selectable' => 0]); // Set the next item to selectable
                        }
                    }
                    
                    $lastSelectableItem = RollingPlan::where('is_active', 1)->where('is_selectable', 1)->first();
                    $nextSelectableItems = RollingPlan::where('is_active', 1)
                        ->where('created_at', '>', $lastSelectableItem->created_at)
                        ->where('is_selectable', 0)
                        ->first(); // Take the first items that are not selectable
    
                    if ($nextSelectableItems) {
                        $nextSelectableItems->update(['is_selectable' => 1]); // Set the next item to selectable
                    }
                }
            } 
    

            //code...
            $result['bahagian'] = refBahagian::with('jabatan')->where('is_hidden', '!=', 1)->orderBy('nama_bahagian', 'asc')->get();
            $result['bahagianEpu'] = BahagianEpuJpm::orderBy('name', 'asc')->get();
            $result['jenis_kategory'] = JenisKategori::with('subJenis')->get();
            $result['rolling'] = RollingPlan::where('is_active', 1)->get();
            $result['RMK'] = RollingPlan::select('rmk', 'is_selectable')->where('row_status', 1)->groupby('rmk', 'is_selectable')->get();
            $result['butiran'] = lookupOption('butiran');
            //$result['skop_project'] = lookupOption('skop_project');
            $result['skop_project'] = SkopOption::with('subskop')->get();
            $result['jenis_kajian'] = lookupOption('jenis_kajian');
            $result['kategory_hakisan'] = lookupOption('kategory_hakisan');
            $result['kajian_kemungkinan'] = lookupOption('kajian_kemungkinan');
            $result['status_tab1'] = lookupOption('status_tab1');
            $result['banjir_limpahan'] = lookupOption('banjir_limpahan');
            $result['koridor_pembangunan'] = lookupOption('koridor_pembangunan');
            $result['kategori_projeck'] = lookupOption('kategori_project');
            $result['sub_sektor'] = \App\Models\SubSektor::where('name', 'Pemeliharaan Alam Sekitar')->with(['updatedBy', 'bahagian', 'sektorUtama', 'sektor'])->first();

            return response()->json([
                'code' => '200',
                'status' => 'Success',
                'data' => $result,
            ]);
        } catch (\Throwable $th) {
            logger()->error($th->getMessage());

            //------------ error log store and email --------------------

            $body = [
                'application_name' => env('APP_NAME'),
                'application_type' => Agent::isPhone(),
                'url' => request()->fullUrl(),
                'error_log' => $th->getMessage(),
                'error_code' => $th->getCode(),
                'ip_address' =>  request()->ip(),
                'user_agent' => request()->userAgent(),
                'email' => env('ERROR_EMAIL'),
            ];

            CallApi($body);

            //------------- end of store and email -----------------------

            return response()->json([
                'code' => '500',
                'status' => 'Failed',
                'error' => $th->getMessage(),
            ]);
        }
    }

    public function getJenisSubKategori($id)
    {
        try {
            //code...
            $result = JenisSubKategori::where('kategori_id', $id)->get();

            return response()->json([
                'code' => '200',
                'status' => 'Success',
                'data' => $result,
            ]);
        } catch (\Throwable $th) {
            logger()->error($th->getMessage());

            //------------ error log store and email --------------------

            $body = [
                'application_name' => env('APP_NAME'),
                'application_type' => Agent::isPhone(),
                'url' => request()->fullUrl(),
                'error_log' => $th->getMessage(),
                'error_code' => $th->getCode(),
                'ip_address' =>  request()->ip(),
                'user_agent' => request()->userAgent(),
                'email' => env('ERROR_EMAIL'),
            ];

            CallApi($body);

            //------------- end of store and email -----------------------

            return response()->json([
                'code' => '500',
                'status' => 'Failed',
                'error' => $th,
            ]);
        }
    }

    public function getSektorUtama($id)
    {
        try {
            //code...
            $result = SektorUtama::where('bahagian_id', $id)->get();

            return response()->json([
                'code' => '200',
                'status' => 'Success',
                'data' => $result,
            ]);
        } catch (\Throwable $th) {
            logger()->error($th->getMessage());

            //------------ error log store and email --------------------

            $body = [
                'application_name' => env('APP_NAME'),
                'application_type' => Agent::isPhone(),
                'url' => request()->fullUrl(),
                'error_log' => $th->getMessage(),
                'error_code' => $th->getCode(),
                'ip_address' =>  request()->ip(),
                'user_agent' => request()->userAgent(),
                'email' => env('ERROR_EMAIL'),
            ];

            CallApi($body);

            //------------- end of store and email -----------------------

            return response()->json([
                'code' => '500',
                'status' => 'Failed',
                'error' => $th,
            ]);
        }
    }

    public function getSektor($id)
    {
        try {
            //code...
            $result = Sektor::where('sektor_utama_id', $id)->get();

            return response()->json([
                'code' => '200',
                'status' => 'Success',
                'data' => $result,
            ]);
        } catch (\Throwable $th) {
            logger()->error($th->getMessage());

            //------------ error log store and email --------------------

            $body = [
                'application_name' => env('APP_NAME'),
                'application_type' => Agent::isPhone(),
                'url' => request()->fullUrl(),
                'error_log' => $th->getMessage(),
                'error_code' => $th->getCode(),
                'ip_address' =>  request()->ip(),
                'user_agent' => request()->userAgent(),
                'email' => env('ERROR_EMAIL'),
            ];

            CallApi($body);

            //------------- end of store and email -----------------------

            return response()->json([
                'code' => '500',
                'status' => 'Failed',
                'error' => $th,
            ]);
        }
    }

    public function getSektorSub($id)
    {
        try {
            //code...
            $result = SubSektor::where('sektor_id', $id)->get();

            return response()->json([
                'code' => '200',
                'status' => 'Success',
                'data' => $result,
            ]);
        } catch (\Throwable $th) {
            logger()->error($th->getMessage());

            //------------ error log store and email --------------------

            $body = [
                'application_name' => env('APP_NAME'),
                'application_type' => Agent::isPhone(),
                'url' => request()->fullUrl(),
                'error_log' => $th->getMessage(),
                'error_code' => $th->getCode(),
                'ip_address' =>  request()->ip(),
                'user_agent' => request()->userAgent(),
                'email' => env('ERROR_EMAIL'),
            ];

            CallApi($body);

            //------------- end of store and email -----------------------

            return response()->json([
                'code' => '500',
                'status' => 'Failed',
                'error' => $th,
            ]);
        }
    }

    public function storeBrifProject(Request $request)
    {
        // print_r($request->toArray());
        // exit();

        try {
            //code...  

            if ($request->negeri_id == 0) {
                $negeri = NULL;
            } else {
                $negeri = $request->negeri_id;
            }
            if ($request->daerah_id == 0) {
                $daerah = NULL;
            } else {
                $daerah = $request->daerah_id;
            }
            if ($request->bahagian_pemilik) {
                $bahagian_pemilik = $request->bahagian_pemilik;
            } else {
                $bahagian_pemilik = $request->bahagian_preli_disabled;
            }
            $sub_sektor = \App\Models\SubSektor::where('name', 'Pemeliharaan Alam Sekitar')->with(['updatedBy', 'bahagian', 'sektorUtama', 'sektor'])->first();
            $bah_terlibat = 0;
            if ($request->bahagian_terlibat_checkbox == 'true') {
                $bah_terlibat = 1;
            }
            $project = Project::create([
                'kategori_Projek' => $request->kategori_project,
                'negeri_id' => $negeri,
                'daerah_id' => $daerah,
                'bahagian_pemilik' => $bahagian_pemilik,
                'rolling_plan_code' => $request->rolling_plan_options,
                'rmk' => $request->RMK,
                'butiran_code' => $request->butiran_options,
                'nama_projek' => strtoupper($request->project_name),
                'objektif' => $request->objektif,
                'ringkasan_projek' => $request->ringkasan_latar,
                'rasional_projek' => $request->rasional_keperluan,
                'Faedah' => $request->faedah,
                'jenis_kategori_code' => $request->jenis_kategori_options,
                'jenis_sub_kategori_code' => $request->jenis_sub_kategori_options,
                'implikasi_projek_tidak_lulus' => $request->implikasi_projeck,
                // 'bahagian_epu_id' => $request->bahagianepu_options,
                // 'sektor_utama_id' => $request->sektor_utama_options,
                // 'sektor_id' => $request->sektor_options,
                // 'sub_sektor_id' => $request->sub_sektor_options,
                'bahagian_epu_id' => $sub_sektor->bahagian->id,
                'sektor_utama_id' => $sub_sektor->sektorUtama->id,
                'sektor_id' => $sub_sektor->sektor->id,
                'sub_sektor_id' => $sub_sektor->id,
                'koridor_pembangunan' => $request->koridor_pembangunan_options,
                'kululusan_khas' => $request->radio_Kelulusan,
                'nota_tambahan' => $request->kelulus_khas,
                'sokongan_upen' => $request->radio_Sokongan,
                'tahun_jangka_mula' => $request->tahun_jangka_mula,
                'tahun_jangka_siap' => $request->tahun_jangka_siap,
                'tempoh_pelaksanaan' => $request->tempoh_pelaksanaan,
                'kajian' => $request->radio_Kajian,
                'jenis_kajian' => $request->jenis_kajian_options,
                'tahun_kajian_siap_terkini' => $request->tahun_terkini,
                'kategori_hakisan' => $request->kategori_hakisan_options,
                'nama_laporan_kajian' => $request->nama_laporan_kajian,
                'rujukan_pelan_induk' => $request->radio_rajukan,
                'rujukan_code' => $request->kajian_kemungkinan_options,
                'nama_laporan_pelan_induk' => $request->nama_laporan_pelan_induk,
                'rujukan_tahun_siap' => $request->tahun_siap_pelan_induk,
                'reka_bantuk_siap' => $request->reka_bentuk_siap,
                'status_reka_bantuk' => $request->radio_Status,
                'melibat_pembinaan_fasa' => $request->radio_pelaksanaan,
                'melibat_pembinaan_fasa_description' => $request->pelaksanaan_description,
                'melibat_pembinaan_fasa_status' => $request->status_options,
                'melibat_pembinaan_fasa_tahun' => $request->pelaksanaan_tahun_siap,
                'kekerapan_banjir_code' => $request->banjir_limpahan_options,
                'pernah_dibahasakan' => $request->radio_Adakah,
                'is_bahagian_terlibat' => $bah_terlibat,
                'dibuat_pada' => Carbon::now()->format('Y-m-d H:i:s'),
                'dibuat_oleh' => $request->user_id,
                'dikemaskini_oleh' => $request->user_id,
                'dikemaskini_pada' => Carbon::now()->format('Y-m-d H:i:s'),
                'tahun' => Carbon::now()->format('Y'),
                'workflow_status' => '1',
                'kos_projeck' => 0,
                'row_status' => 1,
            ]);

            $no_rujukan = generate_running_number($project->bahagianPemilik->acym);
            $project->no_rujukan = $no_rujukan;
            $project->save();

            // projectLog data store
            $section_name = 'Brif';
            if ($project) {
                $stored = Project::latest()->first();;
                $storedData = $stored->toArray();
                $lastId = $storedData['id'];

                $user_data = DB::table('users')
                    ->join('ref_jawatan', 'ref_jawatan.id', '=', 'users.jawatan_id')
                    ->select('users.*', 'ref_jawatan.nama_jawatan')->where('users.id', $request->user_id)->first();
                $no_rojukan_data = DB::table('projects')->select('no_rujukan')->where('id', $lastId)->first();

                $logData = [
                    'user_id' => $request->user_id,
                    'section_name' => $section_name,
                    'projek_id' => $lastId,
                    'modul' => 'Permohonan Projek',
                    'user_ic_no' => $user_data->no_ic,
                    'user_jawatan' => $user_data->nama_jawatan,
                    'user_name' => $user_data->name,
                    'no_rujukan' => $no_rojukan_data->no_rujukan,
                ];
                DB::connection(env('DB_CONNECTION_AUDIT'))->table('projek_log')->insert($logData);
            }

            $terlibat = explode(",", $request->bahagian_terliabt_all);
            if ($terlibat && $request->bahagian_terlibat_checkbox == 'false') {
                foreach ($terlibat as $bahagian_terlibat) {
                    BahagianTerlibat::create([
                        'project_id' => $project->id,
                        'bahagian_id' => $bahagian_terlibat,
                        'dibuat_oleh' => $request->user_id,
                        'dikemaskini_oleh' => $request->user_id,
                        'dikemaskini_pada' => Carbon::now()->format('Y-m-d H:i:s'),
                    ]);
                }
            }

            foreach ($request->skop_project_details as $skop_project) {
                $skop_json = json_decode($skop_project, TRUE);
                $skop_project = SkopProject::create([
                    'project_id' => $project->id,
                    'skop_project_code' => $skop_json['skop_value'],
                    'cost' => 0,
                    'dibuat_pada' => Carbon::now()->format('Y-m-d H:i:s'),
                    'dibuat_oleh' => $request->user_id,
                    'dikemaskini_oleh' => $request->user_id,
                    'dikemaskini_pada' => Carbon::now()->format('Y-m-d H:i:s'),
                    'row_status' => 1,
                ]);

                if ($skop_json['sub_skop_value']) {
                    $sub_skop_array = explode(",", $skop_json['sub_skop_value']);
                    $sub_skop_others = explode("#&", $skop_json['others']);
                    $counter = 0;
                    foreach ($sub_skop_array as $sub_skop) {
                        $lain_lain = null;
                        if ($sub_skop_others[$counter] != 0) {
                            $lain_lain = $sub_skop_others[$counter];
                        }
                        KewanganSkop::create([
                            'permohonan_projek_id' => $project->id,
                            'skop_id' => $skop_project->id,
                            'skop_project_code' => $skop_json['skop_value'],
                            'sub_skop_project_code' => $sub_skop,
                            'nama_componen' => $sub_skop,
                            'jumlahkos' => 0,
                            'Kuantiti' => 0,
                            'units' => 0,
                            'Kos' => 0,
                            'lain_lain' => $lain_lain,
                            'dibuat_pada' => Carbon::now()->format('Y-m-d H:i:s'),
                            'dibuat_oleh' => $request->user_id,
                            'dikemaskini_oleh' => $request->user_id,
                            'dikemaskini_pada' => Carbon::now()->format('Y-m-d H:i:s'),
                            'row_status' => 1,
                        ]);
                        $counter = $counter + 1;
                    }
                }
            }


            if ($request->kajian_project_details) {
                foreach ($request->kajian_project_details as $kajian_project) {
                    $data = json_decode($kajian_project, TRUE);
                    ProjectKajian::create([
                        'project_id' => $project->id,
                        'jenis_kajian_code' => $data['jenis_kajian'],
                        'nama_laporan' => $data['laporan'],
                        'kategori_hakisan' => $data['hakisan'],
                        'tahun_siap_terkini' => $data['tahun_terkini'],
                        'dibuat_pada' => Carbon::now()->format('Y-m-d H:i:s'),
                        'dibuat_oleh' => $request->user_id,
                        'dikemaskini_oleh' => $request->user_id,
                        'dikemaskini_pada' => Carbon::now()->format('Y-m-d H:i:s'),
                        'row_status' => 1,
                    ]);
                }
            }

            $final_pemberat = 0;
            $pemberat = PemberatProjeckBaharu::where('row_status', 1)->get(['name', 'json_values', 'pemberat']);
            foreach ($pemberat as $pemVal) {
                $pemDict[$pemVal->name]['json'] = $pemVal->json_values;
                $pemDict[$pemVal->name]['pemberat'] = $pemVal->pemberat;
            }

            if ($request->radio_Kajian == 1) {
                $json_value = json_decode($pemDict['kajian']['json']);
                $temp = $json_value->ada *  $pemDict['kajian']['pemberat'];
                $final_pemberat = $final_pemberat + $temp;
            }

            if ($request->radio_rajukan == 1) {
                $json_value = json_decode($pemDict['pelan_induk']['json']);
                $temp = $json_value->ada *  $pemDict['pelan_induk']['pemberat'];
                $final_pemberat = $final_pemberat + $temp;
            }

            if ($request->radio_Status_siap == 1) {
                $json_value = json_decode($pemDict['reka_bentuk']['json']);
                $temp = $json_value->reka_siap *  $pemDict['reka_bentuk']['pemberat'];
                $final_pemberat = $final_pemberat + $temp;
            }

            if ($request->radio_Status_siap == 2) {
                $json_value = json_decode($pemDict['reka_bentuk']['json']);
                $temp = $json_value->dalam_penyediaan_reka_bentuk *  $pemDict['reka_bentuk']['pemberat'];
                $final_pemberat = $final_pemberat + $temp;
            }

            if ($request->radio_Sokongan == 1) {
                $json_value = json_decode($pemDict['upen']['json']);
                $temp = $json_value->ada *  $pemDict['upen']['pemberat'];
                $final_pemberat = $final_pemberat + $temp;
            }

            if ($request->radio_Kelulusan == 1) {
                $json_value = json_decode($pemDict['kelulusan']['json']);
                $temp = $json_value->ada *  $pemDict['kelulusan']['pemberat'];
                $final_pemberat = $final_pemberat + $temp;
            }



            if ($request->radio_pelaksanaan == 1) {
                $json_value = json_decode($pemDict['projek_berfasa']['json']);
                $temp = $json_value->ada *  $pemDict['projek_berfasa']['pemberat'];
                $final_pemberat = $final_pemberat + $temp;
            }


            if ($request->radio_Adakah == 1) {
                $json_value = json_decode($pemDict['dibahasa']['json']);
                $temp = $json_value->ada *  $pemDict['dibahasa']['pemberat'];
                $final_pemberat = $final_pemberat + $temp;
            }

            if ($request->banjir_limpahan_options != 0) {
                $lookupValue = lookupOptionSingle('banjir_limpahan', $request->banjir_limpahan_options);
                switch ($lookupValue->value) {
                    case '2 - 4 kali setahun':
                        # code...
                        $json_value = json_decode($pemDict['banjir']['json']);
                        $temp = $json_value->option2 *  $pemDict['banjir']['pemberat'];
                        $final_pemberat = $final_pemberat + $temp;
                        break;
                    case '> 5 kali setahun':
                        # code...
                        $json_value = json_decode($pemDict['banjir']['json']);
                        $temp = $json_value->option3 *  $pemDict['banjir']['pemberat'];
                        $final_pemberat = $final_pemberat + $temp;
                        break;
                    case '1 kali setahun':
                        # code...
                        $json_value = json_decode($pemDict['banjir']['json']);
                        $temp = $json_value->option1 *  $pemDict['banjir']['pemberat'];
                        $final_pemberat = $final_pemberat + $temp;
                        break;
                    default:
                        # code...
                        $final_pemberat = $final_pemberat + 0;
                        break;
                }
            }

            $project->pemberat = $final_pemberat;
            $project->save();


            return response()->json([
                'code' => '200',
                'status' => 'Success',
                'data' => $project,
            ]);
        } catch (\Throwable $th) {
            logger()->error($th->getMessage());

            //------------ error log store and email --------------------

            $body = [
                'application_name' => env('APP_NAME'),
                'application_type' => Agent::isPhone(),
                'url' => request()->fullUrl(),
                'error_log' => $th->getMessage(),
                'error_code' => $th->getCode(),
                'ip_address' =>  request()->ip(),
                'user_agent' => request()->userAgent(),
                'email' => env('ERROR_EMAIL'),
            ];

            CallApi($body);

            //------------- end of store and email -----------------------

            return response()->json([
                'code' => '500',
                'status' => 'Failed',
                'error' => $th,
            ]);
        }
    }


    public function updateBrifProject(Request $request)
    {
        try {
            //code... 

            $sub_sektor = \App\Models\SubSektor::where('name', 'Pemeliharaan Alam Sekitar')->with(['updatedBy', 'bahagian', 'sektorUtama', 'sektor'])->first();
            $bah_terlibat = 0;
            if ($request->bahagian_terlibat_checkbox == 'true') {
                $bah_terlibat = 1;
            }
            if ($request->bahagian_pemilik) {
                $bahagian_pemilik = $request->bahagian_pemilik;
            } else {
                $bahagian_pemilik = $request->bahagian_preli_disabled;
            }
            $project = Project::updateOrCreate(
                // exit(),
                ['id' => $request->id],
                [
                    'kategori_Projek' => $request->kategori_project,
                    'bahagian_pemilik' => $bahagian_pemilik,
                    'rolling_plan_code' => $request->rolling_plan_options,
                    'rmk' => $request->RMK,
                    'butiran_code' => $request->butiran_options,
                    'nama_projek' => strtoupper($request->project_name),
                    'objektif' => $request->objektif,
                    'ringkasan_projek' => $request->ringkasan_latar,
                    'rasional_projek' => $request->rasional_keperluan,
                    'Faedah' => $request->faedah,
                    'jenis_kategori_code' => $request->jenis_kategori_options,
                    'jenis_sub_kategori_code' => $request->jenis_sub_kategori_options,
                    'implikasi_projek_tidak_lulus' => $request->implikasi_projeck,
                    'bahagian_epu_id' => $sub_sektor->bahagian->id,
                    'sektor_utama_id' => $sub_sektor->sektorUtama->id,
                    'sektor_id' => $sub_sektor->sektor->id,
                    'sub_sektor_id' => $sub_sektor->id,
                    'koridor_pembangunan' => $request->koridor_pembangunan_options,
                    'kululusan_khas' => $request->radio_Kelulusan,
                    'nota_tambahan' => $request->kelulus_khas,
                    'sokongan_upen' => $request->radio_Sokongan,
                    'tahun_jangka_mula' => $request->tahun_jangka_mula,
                    'tahun_jangka_siap' => $request->tahun_jangka_siap,
                    'tempoh_pelaksanaan' => $request->tempoh_pelaksanaan,
                    'kajian' => $request->radio_Kajian,
                    'jenis_kajian' => $request->jenis_kajian_options,
                    'tahun_kajian_siap_terkini' => $request->tahun_terkini,
                    'kategori_hakisan' => $request->kategori_hakisan_options,
                    'nama_laporan_kajian' => $request->nama_laporan_kajian,
                    'rujukan_pelan_induk' => $request->radio_rajukan,
                    'rujukan_code' => $request->kajian_kemungkinan_options,
                    'nama_laporan_pelan_induk' => $request->nama_laporan_pelan_induk,
                    'rujukan_tahun_siap' => $request->tahun_siap_pelan_induk,
                    'reka_bantuk_siap' => $request->reka_bentuk_siap,
                    'status_reka_bantuk' => $request->radio_Status,
                    'melibat_pembinaan_fasa' => $request->radio_pelaksanaan,
                    'melibat_pembinaan_fasa_description' => $request->pelaksanaan_description,
                    'melibat_pembinaan_fasa_status' => $request->status_options,
                    'melibat_pembinaan_fasa_tahun' => $request->pelaksanaan_tahun_siap,
                    'kekerapan_banjir_code' => $request->banjir_limpahan_options,
                    'pernah_dibahasakan' => $request->radio_Adakah,
                    'is_bahagian_terlibat' => $bah_terlibat,
                    //'dibuat_pada' => Carbon::now()->format('Y-m-d H:i:s'),
                    //'dibuat_oleh' => $request->user_id,
                    'dikemaskini_oleh' => $request->user_id,
                    'dikemaskini_pada' => Carbon::now()->format('Y-m-d H:i:s'),
                    'tahun' => Carbon::now()->format('Y'),
                    // 'workflow_status' => '1',
                    'kos_projeck' => 0,
                    'row_status' => 1,
                ]
            );

            $fizikal_jenis =  JenisKategori::where('name', 'Fizikal - Pembinaan')->first();

            if ($fizikal_jenis->id != $project->jenis_kategori_code) {
                $vae = vae::where('Permohonan_Projek_id', $project->id)->first();
                if ($vae) {
                    $vae->row_status = 0;
                    $vae->save();
                }
            }

            //$terlibat = explode(",",$request->bahagian_terliabt_all);
            if ($request->bahagian_terlibat_checkbox == 'true') {
                $terlibatProject = BahagianTerlibat::where('project_id', $request->id)->delete();
            } else {
                if ($request->bahagian_terliabt_all) {
                    $terlibatProject = BahagianTerlibat::where('project_id', $request->id)->delete();
                    foreach ($request->bahagian_terliabt_all as $bahagian_terlibat) {
                        BahagianTerlibat::create([
                            'project_id' => $project->id,
                            'bahagian_id' => $bahagian_terlibat,
                            'dibuat_oleh' => $request->user_id,
                            'dikemaskini_oleh' => $request->user_id,
                            'dikemaskini_pada' => Carbon::now()->format('Y-m-d H:i:s'),
                        ]);
                    }
                }
            }



            if ($request->kajian_project_details) {
                $kajianProject = ProjectKajian::where('project_id', $request->id)->delete();
                foreach ($request->kajian_project_details as $kajian_project) {
                    $data = json_decode($kajian_project, TRUE);
                    ProjectKajian::create([
                        'project_id' => $project->id,
                        'jenis_kajian_code' => $data['jenis_kajian'],
                        'nama_laporan' => $data['laporan'],
                        'kategori_hakisan' => $data['hakisan'],
                        'tahun_siap_terkini' => $data['tahun_terkini'],
                        'dibuat_pada' => Carbon::now()->format('Y-m-d H:i:s'),
                        'dibuat_oleh' => $request->user_id,
                        'dikemaskini_oleh' => $request->user_id,
                        'dikemaskini_pada' => Carbon::now()->format('Y-m-d H:i:s'),
                        'row_status' => 1,
                    ]);
                }
            }

            $existing_skop_id = SkopProject::where('project_id', $request->id)->pluck('id')->toArray();
            $existing_sub_skop_id = KewanganSkop::where('permohonan_projek_id', $request->id)->pluck('id')->toArray();
            $current_skop_id = [];
            $current_sub_skop_id = [];
            foreach ($request->skop_project_details as $skop_project) {
                $skop_json = json_decode($skop_project, TRUE);
                if ($skop_json['skop']['id'] != '') {
                    array_push($current_skop_id, $skop_json['skop']['id']);
                }
                foreach ($skop_json['sub_skop'] as $sub_skop_project) {
                    $sub_skop_json = json_decode($sub_skop_project, TRUE);
                    if ($sub_skop_json['id'] != '') {
                        array_push($current_sub_skop_id, $sub_skop_json['id']);
                    }
                }
            }

            $delete_skop_id = [];
            $delete_sub_skop_id = [];
            foreach ($existing_skop_id as $skop_id) {
                if (!in_array($skop_id, $current_skop_id)) {
                    array_push($delete_skop_id, $skop_id);
                }
            }

            foreach ($existing_sub_skop_id as $sub_skop_id) {
                if (!in_array($sub_skop_id, $current_sub_skop_id)) {
                    array_push($delete_sub_skop_id, $sub_skop_id);
                }
            }

            KewanganSkop::whereIn('id', $delete_sub_skop_id)->delete();
            SkopProject::whereIn('id', $delete_skop_id)->delete();


            foreach ($request->skop_project_details as $skop_project) {
                $skop_json = json_decode($skop_project, TRUE);
                $skop_id = $skop_json['skop']['id'];
                if ($skop_json['skop']['id'] != '') {
                    //array_push($current_skop_id,$skop_json['skop']['id']) ;
                    $skop_project = SkopProject::whereId($skop_json['skop']['id'])->update([
                        'skop_project_code' => $skop_json['skop']['value'],
                        'dikemaskini_oleh' => $request->user_id,
                        'dikemaskini_pada' => Carbon::now()->format('Y-m-d H:i:s'),
                    ]);
                } else {
                    $skop_project = SkopProject::create([
                        'project_id' => $project->id,
                        'skop_project_code' => $skop_json['skop']['value'],
                        'cost' => 0,
                        'dibuat_pada' => Carbon::now()->format('Y-m-d H:i:s'),
                        'dibuat_oleh' => $request->user_id,
                        'dikemaskini_oleh' => $request->user_id,
                        'dikemaskini_pada' => Carbon::now()->format('Y-m-d H:i:s'),
                        'row_status' => 1,
                    ]);
                    $skop_id = $skop_project->id;
                }
                foreach ($skop_json['sub_skop'] as $sub_skop_project) {
                    $sub_skop_json = json_decode($sub_skop_project, TRUE);
                    if ($sub_skop_json['id'] != '') {
                        //array_push($current_sub_skop_id,$sub_skop_json['id']);
                        if ($sub_skop_json['others'] != 'null') {
                            $lain_lain = $sub_skop_json['others'];
                        } else {
                            $lain_lain = null;
                        }
                        KewanganSkop::whereId($sub_skop_json['id'])->update([
                            'sub_skop_project_code' => $sub_skop_json['value'],
                            'nama_componen' => $sub_skop_json['value'],
                            'lain_lain' => $lain_lain,
                            'dikemaskini_oleh' => $request->user_id,
                            'dikemaskini_pada' => Carbon::now()->format('Y-m-d H:i:s'),
                        ]);
                    } else {
                        if ($sub_skop_json['others'] != 'null') {
                            $lain_lain = $sub_skop_json['others'];
                        } else {
                            $lain_lain = null;
                        }
                        KewanganSkop::create([
                            'permohonan_projek_id' => $project->id,
                            'skop_id' => $skop_id,
                            'skop_project_code' => $skop_json['skop']['value'],
                            'sub_skop_project_code' => $sub_skop_json['value'],
                            'nama_componen' => $sub_skop_json['value'],
                            'jumlahkos' => 0,
                            'Kuantiti' => 0,
                            'units' => 0,
                            'Kos' => 0,
                            'lain_lain' => $lain_lain,
                            'dibuat_pada' => Carbon::now()->format('Y-m-d H:i:s'),
                            'dibuat_oleh' => $request->user_id,
                            'dikemaskini_oleh' => $request->user_id,
                            'dikemaskini_pada' => Carbon::now()->format('Y-m-d H:i:s'),
                            'row_status' => 1,
                        ]);
                    }
                }
            }

            $final_pemberat = 0;
            $pemberat = PemberatProjeckBaharu::where('row_status', 1)->get(['name', 'json_values', 'pemberat']);
            foreach ($pemberat as $pemVal) {
                $pemDict[$pemVal->name]['json'] = $pemVal->json_values;
                $pemDict[$pemVal->name]['pemberat'] = $pemVal->pemberat;
            }

            if ($request->radio_Kajian == 1) {
                $json_value = json_decode($pemDict['kajian']['json']);
                $temp = $json_value->ada *  $pemDict['kajian']['pemberat'];
                $final_pemberat = $final_pemberat + $temp;
            }

            if ($request->radio_rajukan == 1) {
                $json_value = json_decode($pemDict['pelan_induk']['json']);
                $temp = $json_value->ada *  $pemDict['pelan_induk']['pemberat'];
                $final_pemberat = $final_pemberat + $temp;
            }

            if ($request->radio_Status == 1) {
                $json_value = json_decode($pemDict['reka_bentuk']['json']);
                $temp = $json_value->reka_siap *  $pemDict['reka_bentuk']['pemberat'];
                $final_pemberat = $final_pemberat + $temp;
            }

            if ($request->radio_Status == 2) {
                $json_value = json_decode($pemDict['reka_bentuk']['json']);
                $temp = $json_value->dalam_penyediaan_reka_bentuk *  $pemDict['reka_bentuk']['pemberat'];
                $final_pemberat = $final_pemberat + $temp;
            }

            if ($request->radio_Sokongan == 1) {
                $json_value = json_decode($pemDict['upen']['json']);
                $temp = $json_value->ada *  $pemDict['upen']['pemberat'];
                $final_pemberat = $final_pemberat + $temp;
            }

            if ($request->radio_Kelulusan == 1) {
                $json_value = json_decode($pemDict['kelulusan']['json']);
                $temp = $json_value->ada *  $pemDict['kelulusan']['pemberat'];
                $final_pemberat = $final_pemberat + $temp;
            }



            if ($request->radio_pelaksanaan == 1) {
                $json_value = json_decode($pemDict['projek_berfasa']['json']);
                $temp = $json_value->ada *  $pemDict['projek_berfasa']['pemberat'];
                $final_pemberat = $final_pemberat + $temp;
            }


            if ($request->radio_Adakah == 1) {
                $json_value = json_decode($pemDict['dibahasa']['json']);
                $temp = $json_value->ada *  $pemDict['dibahasa']['pemberat'];
                $final_pemberat = $final_pemberat + $temp;
            }

            if ($request->banjir_limpahan_options != 0) {
                $lookupValue = lookupOptionSingle('banjir_limpahan', $request->banjir_limpahan_options);
                switch ($lookupValue->value) {
                    case '2 - 4 kali setahun':
                        # code...
                        $json_value = json_decode($pemDict['banjir']['json']);
                        $temp = $json_value->option2 *  $pemDict['banjir']['pemberat'];
                        $final_pemberat = $final_pemberat + $temp;
                        break;
                    case '> 5 kali setahun':
                        # code...
                        $json_value = json_decode($pemDict['banjir']['json']);
                        $temp = $json_value->option3 *  $pemDict['banjir']['pemberat'];
                        $final_pemberat = $final_pemberat + $temp;
                        break;
                    case '1 kali setahun':
                        # code...
                        $json_value = json_decode($pemDict['banjir']['json']);
                        $temp = $json_value->option1 *  $pemDict['banjir']['pemberat'];
                        $final_pemberat = $final_pemberat + $temp;
                        break;
                    default:
                        # code...
                        $final_pemberat = $final_pemberat + 0;
                        break;
                }
            }

            $project->pemberat = $final_pemberat;
            $project->save();


            return response()->json([
                'code' => '200',
                'status' => 'Success',
                'data' => $project,
            ]);
        } catch (\Throwable $th) {
            logger()->error($th->getMessage());

            //------------ error log store and email --------------------

            $body = [
                'application_name' => env('APP_NAME'),
                'application_type' => Agent::isPhone(),
                'url' => request()->fullUrl(),
                'error_log' => $th->getMessage(),
                'error_code' => $th->getCode(),
                'ip_address' =>  request()->ip(),
                'user_agent' => request()->userAgent(),
                'email' => env('ERROR_EMAIL'),
            ];

            CallApi($body);

            //------------- end of store and email -----------------------

            return response()->json([
                'code' => '500',
                'status' => 'Failed',
                'error' => $th,
            ]);
        }
    }

    public function listPejabatProject(Request $request)
    {
        try {
            //code...
            $result = PejabatProjek::where('row_status', 1)->where('IsActive', '=', 1)->get();


            return response()->json([
                'code' => '200',
                'status' => 'Success',
                'data' => $result
            ]);
        } catch (\Throwable $th) {
            logger()->error($th->getMessage());

            //------------ error log store and email --------------------

            $body = [
                'application_name' => env('APP_NAME'),
                'application_type' => Agent::isPhone(),
                'url' => request()->fullUrl(),
                'error_log' => $th->getMessage(),
                'error_code' => $th->getCode(),
                'ip_address' =>  request()->ip(),
                'user_agent' => request()->userAgent(),
                'email' => env('ERROR_EMAIL'),
            ];

            CallApi($body);

            //------------- end of store and email -----------------------

            return response()->json([
                'code' => '500',
                'status' => 'Failed',
                'error' => $th,
            ]);
        }
    }

    public function PejabatProject(Request $request)
    {
        try {
            //code...
            if ($request->id) {
                $result = PejabatProjek::with(['user'])->where('id', '=', $request->id)->where('row_status', 1)->first();
            } else {
                $result = PejabatProjek::with(['user'])->where('row_status', 1)->get();
            }


            return response()->json([
                'code' => '200',
                'status' => 'Success',
                'data' => $result
            ]);
        } catch (\Throwable $th) {
            logger()->error($th->getMessage());

            //------------ error log store and email --------------------

            $body = [
                'application_name' => env('APP_NAME'),
                'application_type' => Agent::isPhone(),
                'url' => request()->fullUrl(),
                'error_log' => $th->getMessage(),
                'error_code' => $th->getCode(),
                'ip_address' =>  request()->ip(),
                'user_agent' => request()->userAgent(),
                'email' => env('ERROR_EMAIL'),
            ];

            CallApi($body);

            //------------- end of store and email -----------------------

            return response()->json([
                'code' => '500',
                'status' => 'Failed',
                'error' => $th,
            ]);
        }
    }

    public function updatepejabat(Request $request)
    {
        try {
            $data = $request->toArray();
            // print_r($data);exit;
            $units = PejabatProjek::where('id', $data['id'])->first();
            $units->pajabat_projek = $data['nama_komponen'];
            $units->dikemaskini_oleh = $data['user_id'];
            $units->dikemaskini_pada = Carbon::now()->format('Y-m-d H:i:s');
            $units->update();
            if ($units->update() == 'true') {
                return response()->json([
                    'code' => '200',
                    'status' => 'Success',
                ]);
            }
        } catch (\Throwable $th) {
            logger()->error($th->getMessage());

            //------------ error log store and email --------------------

            $body = [
                'application_name' => env('APP_NAME'),
                'application_type' => Agent::isPhone(),
                'url' => request()->fullUrl(),
                'error_log' => $th->getMessage(),
                'error_code' => $th->getCode(),
                'ip_address' =>  request()->ip(),
                'user_agent' => request()->userAgent(),
                'email' => env('ERROR_EMAIL'),
            ];

            CallApi($body);

            //------------- end of store and email -----------------------

            return response()->json([
                'code' => '500',
                'status' => 'Failed',
                'error' => $th,
            ]);
        }
    }

    public function addpejabat(Request $request)
    {
        try {
            $data = $request->toArray();

            $units = PejabatProjek::create([
                'pajabat_projek' => $request->nama_komponen,
                'dibuat_pada' => Carbon::now()->format('Y-m-d H:i:s'),
                'dibuat_oleh' => $request->user_id,
                'dikemaskini_pada' => Carbon::now()->format('Y-m-d H:i:s'),
                'dikemaskini_oleh' => $request->user_id,
            ]);

            return response()->json([
                'code' => '200',
                'status' => 'Success',
            ]);
        } catch (\Throwable $th) {
            logger()->error($th->getMessage());

            //------------ error log store and email --------------------

            $body = [
                'application_name' => env('APP_NAME'),
                'application_type' => Agent::isPhone(),
                'url' => request()->fullUrl(),
                'error_log' => $th->getMessage(),
                'error_code' => $th->getCode(),
                'ip_address' =>  request()->ip(),
                'user_agent' => request()->userAgent(),
                'email' => env('ERROR_EMAIL'),
            ];

            CallApi($body);

            //------------- end of store and email -----------------------

            return response()->json([
                'code' => '500',
                'status' => 'Failed',
                'error' => $th,
            ]);
        }
    }

    public function updatePejabatStatus(Request $request)
    {
        try {
            $data = $request->toArray();

            $units = PejabatProjek::where('id', $data['id'])->first();
            $units->IsActive = $data['value'];
            $units->dikemaskini_oleh = $data['user_id'];
            $units->dikemaskini_pada = Carbon::now()->format('Y-m-d H:i:s');
            $units->update();
            if ($units->update() == 'true') {
                return response()->json([
                    'code' => '200',
                    'status' => 'Success',
                ]);
            }
        } catch (\Throwable $th) {
            logger()->error($th->getMessage());

            //------------ error log store and email --------------------

            $body = [
                'application_name' => env('APP_NAME'),
                'application_type' => Agent::isPhone(),
                'url' => request()->fullUrl(),
                'error_log' => $th->getMessage(),
                'error_code' => $th->getCode(),
                'ip_address' =>  request()->ip(),
                'user_agent' => request()->userAgent(),
                'email' => env('ERROR_EMAIL'),
            ];

            CallApi($body);

            //------------- end of store and email -----------------------

            return response()->json([
                'code' => '500',
                'status' => 'Failed',
                'error' => $th,
            ]);
        }
    }


    public function projectWithUserId(Request $request)
    {

        try {
            $user = Auth::user();
            if ($request->usertype == 1) {
                $result = \App\Models\Project::with(['bahagianPemilik', 'jenisKategori', 'createdBy', 'updatedBy', 'penyemak1', 'penyemak2', 'kewangan', 'negeri', 'rollingPlan', 'daerah', 'penyemak', 'pengesah', 'peraku', 'negerilist.negeri', 'negerilist.daerah'])
                    ->where('daerah_id', $request->daerah)
                    ->orderBy('updated_at', 'DESC')
                    ->get();
            } else if ($request->usertype == 2) {
                $result = \App\Models\Project::with(['bahagianPemilik', 'jenisKategori', 'createdBy', 'updatedBy', 'penyemak1', 'penyemak2', 'kewangan', 'negeri', 'rollingPlan', 'daerah', 'penyemak', 'pengesah', 'peraku', 'negerilist.negeri', 'negerilist.daerah'])
                    ->select('projects.*')
                    ->where('projects.negeri_id', $request->negeri)
                    ->where('projects.daerah_id', '=', NULL)
                    ->orderBy('updated_at', 'DESC')
                    ->get();
                $start = 2;
                $end = 4;
                // $result_daerah = \App\Models\Project::with(['bahagianPemilik', 'jenisKategori', 'createdBy', 'updatedBy', 'penyemak1', 'penyemak2', 'kewangan', 'negeri', 'rollingPlan', 'daerah', 'penyemak', 'pengesah', 'peraku', 'negerilist.negeri', 'negerilist.daerah'])
                //     ->select('projects.*')
                //     ->where('projects.negeri_id', $request->negeri)
                //     ->where('projects.daerah_id', '!=', NULL)
                //     ->where(function ($query) use ($start, $end) {
                //         $query->where('projects.workflow_status', $start)
                //             ->orwhere('projects.workflow_status', $end);
                //     })
                //     ->orderBy('updated_at', 'DESC')
                //     ->get();
                $result_daerah = \App\Models\Project::with(['bahagianPemilik', 'jenisKategori', 'createdBy', 'updatedBy', 'penyemak1', 'penyemak2', 'kewangan', 'negeri', 'rollingPlan', 'daerah', 'penyemak', 'pengesah', 'peraku', 'negerilist.negeri', 'negerilist.daerah'])
                    ->select('projects.*')
                    ->where('projects.negeri_id', $request->negeri)
                    ->where('projects.daerah_id', '!=', NULL)
                    ->Where('projects.workflow_status', '>=', $start)
                    ->orderBy('updated_at', 'DESC')
                    ->get();

                $result = $result->concat($result_daerah);
            } else if ($request->usertype == 3) {

                $result = \App\Models\Project::with(['bahagianPemilik', 'jenisKategori', 'createdBy', 'updatedBy', 'penyemak1', 'penyemak2', 'kewangan', 'negeri', 'rollingPlan', 'daerah', 'penyemak', 'pengesah', 'peraku', 'negerilist.negeri', 'negerilist.daerah'])
                    ->select('projects.*')
                    ->where('projects.bahagian_pemilik', $request->bahagian)
                    ->where('projects.negeri_id', '=', NULL)
                    ->orderBy('updated_at', 'DESC')
                    ->get();

                if ($request->pengesah == 1) {
                    $result_pengesah = \App\Models\Project::with(['bahagianPemilik', 'negeri', 'jenisKategori', 'createdBy', 'updatedBy', 'penyemak1', 'penyemak2', 'kewangan', 'rollingPlan', 'daerah', 'penyemak', 'pengesah', 'peraku', 'negerilist.negeri', 'negerilist.daerah'])
                        ->select('projects.*')
                        ->where('projects.bahagian_pemilik', $request->bahagian)
                        ->where('projects.negeri_id', '!=', NULL)
                        ->Where('projects.workflow_status', '>=', 11)
                        ->orderBy('updated_at', 'DESC')
                        ->get();

                    $result = $result->concat($result_pengesah);
                }

                $result_negeri = \App\Models\Project::with(['bahagianPemilik', 'negeri', 'jenisKategori', 'createdBy', 'updatedBy', 'penyemak1', 'penyemak2', 'kewangan', 'rollingPlan', 'daerah', 'penyemak', 'pengesah', 'peraku', 'negerilist.negeri', 'negerilist.daerah'])
                    ->select('projects.*')
                    ->where('projects.bahagian_pemilik', $request->bahagian)
                    ->where('projects.negeri_id', '!=', NULL)
                    ->Where('projects.workflow_status', '>=', 7)
                    ->orderBy('updated_at', 'DESC')
                    ->get();


                $result = $result->concat($result_negeri);
            } else if ($request->usertype == 4 && $request->userRole == 4) {
                $result = \App\Models\Project::with(['bahagianPemilik', 'jenisKategori', 'negeri', 'createdBy', 'updatedBy', 'penyemak1', 'penyemak2', 'kewangan', 'rollingPlan', 'daerah', 'penyemak', 'pengesah', 'peraku', 'negerilist.negeri', 'negerilist.daerah'])
                    ->where('workflow_status', '>=', 14)
                    ->where('dibuat_oleh', '!=', $request->id)
                    ->orderBy('updated_at', 'DESC')
                    ->get();

                $result_bkor = \App\Models\Project::with(['bahagianPemilik', 'negeri', 'jenisKategori', 'createdBy', 'updatedBy', 'penyemak1', 'penyemak2', 'kewangan', 'rollingPlan', 'daerah', 'penyemak', 'pengesah', 'peraku', 'negerilist.negeri', 'negerilist.daerah'])
                    ->where('projects.bahagian_pemilik', $request->bahagian)
                    ->where('projects.negeri_id', '=', NULL)
                    ->where('workflow_status', '<', 14)
                    ->orwhere('dibuat_oleh', $request->id)
                    ->orderBy('updated_at', 'DESC')
                    ->get();

                $result = $result->concat($result_bkor);
            } else {
                $result = \App\Models\Project::with(['bahagianPemilik', 'negeri', 'jenisKategori', 'createdBy', 'updatedBy', 'penyemak1', 'penyemak2', 'kewangan', 'rollingPlan', 'daerah', 'penyemak', 'pengesah', 'peraku', 'negerilist.negeri', 'negerilist.daerah'])
                    ->where('dibuat_oleh', $request->id)
                    ->orderBy('updated_at', 'DESC')
                    ->get();
            }

            $currentYear = date("Y");

            $bayangan = totalBayangan::where('year', $currentYear)->first();

            return response()->json([
                'code' => '200',
                'status' => 'Success',
                'data' => $result,
                'bayangan' => $bayangan
            ]);
        } catch (\Throwable $th) {
            logger()->error($th->getMessage());


            //------------ error log store and email --------------------

            $body = [
                'application_name' => env('APP_NAME'),
                'application_type' => Agent::isPhone(),
                'url' => request()->fullUrl(),
                'error_log' => $th->getMessage(),
                'error_code' => $th->getCode(),
                'ip_address' =>  request()->ip(),
                'user_agent' => request()->userAgent(),
                'email' => env('ERROR_EMAIL'),
            ];

            CallApi($body);

            //------------- end of store and email -----------------------

            return response()->json([
                'code' => '500',
                'status' => 'Failed',
                'error' => $th,
            ]);
        }
    }

    public function getSemakProjectList(Request $request)
    {

        //print_r($request->all());exit;
        try {
            $user = Auth::user();
            if ($request->usertype == 2 && $request->userRole == 2) {
                $result = \App\Models\Project::with(['bahagianPemilik', 'negeri', 'jenisKategori', 'createdBy', 'updatedBy', 'penyemak1', 'penyemak2', 'kewangan', 'rollingPlan', 'daerah', 'penyemak', 'pengesah', 'peraku'])
                    ->where('penyemak', $request->id)
                    ->where('workflow_status', 3)
                    ->orderBy('updated_at', 'DESC')
                    ->get();

                $result_penyemak_1 = \App\Models\Project::with(['bahagianPemilik', 'negeri', 'jenisKategori', 'createdBy', 'updatedBy', 'penyemak1', 'penyemak2', 'kewangan', 'rollingPlan', 'daerah', 'penyemak', 'pengesah', 'peraku'])
                    ->where('penyemak_1', $request->id)
                    ->where('workflow_status', 6)
                    ->orderBy('updated_at', 'DESC')
                    ->get();;

                $result = $result->concat($result_penyemak_1);
            } else if ($request->usertype == 3 && $request->userRole == 3) {
                $result = \App\Models\Project::with(['bahagianPemilik', 'negeri', 'jenisKategori', 'createdBy', 'updatedBy', 'penyemak1', 'penyemak2', 'kewangan', 'rollingPlan', 'daerah', 'penyemak', 'pengesah', 'peraku'])
                    ->where('penyemak', $request->id)
                    ->where('workflow_status', 3)
                    ->orderBy('updated_at', 'DESC')
                    ->get();

                $result_penyemak_2 = \App\Models\Project::with(['bahagianPemilik', 'negeri', 'jenisKategori', 'createdBy', 'updatedBy', 'penyemak1', 'penyemak2', 'kewangan', 'rollingPlan', 'daerah', 'penyemak', 'pengesah', 'peraku'])
                    ->where('penyemak_2', $request->id)
                    ->where('workflow_status', 10)
                    ->orderBy('updated_at', 'DESC')
                    ->get();

                $result = $result->concat($result_penyemak_2);

                if ($request->penyemak_1 == 1) {
                    $result_penyemak_1 = \App\Models\Project::with(['bahagianPemilik', 'negeri', 'jenisKategori', 'createdBy', 'updatedBy', 'penyemak1', 'penyemak2', 'kewangan', 'rollingPlan', 'daerah', 'penyemak', 'pengesah', 'peraku'])
                        ->where('penyemak_1', $request->id)
                        ->where('workflow_status', 6)
                        ->orderBy('updated_at', 'DESC')
                        ->get();;
                    $result = $result->concat($result_penyemak_1);
                }
                if ($request->pengesah == 1) {
                    // $result_pengesah = \App\Models\Project::with(['bahagianPemilik','negeri','jenisKategori','createdBy','updatedBy','penyemak1','penyemak2','kewangan','rollingPlan','daerah','penyemak','pengesah','peraku'])
                    //                                         ->where('pengesah',$request->id)
                    //                                         ->where('workflow_status',13)
                    //                                         ->orderBy('updated_at','DESC')
                    //                                         ->get();;
                    $result_pengesah = [];
                    $result = $result->concat($result_pengesah);
                }
            } else if ($request->usertype == 4 && $request->userRole == 4) {

                $result = \App\Models\Project::with(['bahagianPemilik', 'negeri', 'jenisKategori', 'createdBy', 'updatedBy', 'penyemak1', 'penyemak2', 'kewangan', 'rollingPlan', 'daerah', 'penyemak', 'pengesah', 'peraku'])
                    ->where('penyemak', $request->id)
                    ->where('workflow_status', 3)
                    //->orwhere('workflow_status','>=',14)
                    ->orderBy('updated_at', 'DESC')
                    ->get();


                if ($request->penyemak_1 == 1) {
                    $result_penyemak_1 = \App\Models\Project::with(['bahagianPemilik', 'negeri', 'jenisKategori', 'createdBy', 'updatedBy', 'penyemak1', 'penyemak2', 'kewangan', 'rollingPlan', 'daerah', 'penyemak', 'pengesah', 'peraku'])
                        ->where('penyemak_1', $request->id)
                        ->where('workflow_status', 6)
                        ->orderBy('updated_at', 'DESC')
                        ->get();;

                    $result = $result->concat($result_penyemak_1);
                }
                if ($request->penyemak_2 == 1) {
                    $result_penyemak_2 = \App\Models\Project::with(['bahagianPemilik', 'negeri', 'jenisKategori', 'createdBy', 'updatedBy', 'penyemak1', 'penyemak2', 'kewangan', 'rollingPlan', 'daerah', 'penyemak', 'pengesah', 'peraku'])
                        ->where('penyemak_2', $request->id)
                        ->where('workflow_status', 10)
                        ->orderBy('updated_at', 'DESC')
                        ->get();

                    $result = $result->concat($result_penyemak_2);
                }
                if ($request->pengesah == 1) {
                    // $result_pengesah = \App\Models\Project::with(['bahagianPemilik','negeri','jenisKategori','createdBy','updatedBy','penyemak1','penyemak2','kewangan','rollingPlan','daerah','penyemak','pengesah','peraku'])
                    //                                         ->where('pengesah',$request->id)
                    //                                         ->where('workflow_status',13)
                    //                                         ->orderBy('updated_at','DESC')
                    //                                         ->get();
                    $result_pengesah = [];
                    $result = $result->concat($result_pengesah);
                }
                if ($request->peraku == 1) {
                    // $result_pengesah = \App\Models\Project::with(['bahagianPemilik','negeri','jenisKategori','createdBy','updatedBy','penyemak1','penyemak2','kewangan','rollingPlan','daerah','penyemak','pengesah','peraku'])
                    //                                         ->where('pengesah',$request->id)
                    //                                         ->where('workflow_status',13)
                    //                                         ->orderBy('updated_at','DESC')
                    //                                         ->get();
                    $result_peraku = [];
                    $result = $result->concat($result_peraku);
                }
            } else {
                $result = [];
            }
            return response()->json([
                'code' => '200',
                'status' => 'Success',
                'data' => $result,
            ]);
        } catch (\Throwable $th) {
            logger()->error($th->getMessage());

            //------------ error log store and email --------------------

            $body = [
                'application_name' => env('APP_NAME'),
                'application_type' => Agent::isPhone(),
                'url' => request()->fullUrl(),
                'error_log' => $th->getMessage(),
                'error_code' => $th->getCode(),
                'ip_address' =>  request()->ip(),
                'user_agent' => request()->userAgent(),
                'email' => env('ERROR_EMAIL'),
            ];

            CallApi($body);

            //------------- end of store and email -----------------------

            return response()->json([
                'code' => '500',
                'status' => 'Failed',
                'error' => $th,
            ]);
        }
    }


    public function approveProject(Request $request)
    {
        try {
            $result = \App\Models\Project::where('id', $request->id)->first();

            if ($request->usertype == 1 && $request->userRole == 1) {
                if ($request->workflow == 5) {
                    $work_flow = 3;
                } else if ($request->workflow == 8) {
                    $work_flow = 6;
                } else if ($request->workflow == 12) {
                    $work_flow = 10;
                } else if ($request->workflow == 15) {
                    $work_flow = 13;
                } else if ($request->workflow == 18) {
                    $work_flow = 14;
                } else {
                    $work_flow = 2;
                }

                $result->workflow_status = $work_flow;

                $role = \App\Models\Role::where('name', 'PENYEMAK')->first();
                $user_result = \App\Models\User::select('users.email', 'users.id')
                    ->where('negeri_id', $request->negeri)
                    ->whereHas('userTypeRole', function ($query) use ($role) {
                        $query->where('role_id', $role->id);
                    })
                    ->get();


                $userData = [
                    'comment' => "Permohonan projek bagi No.Rujukan [" . $request->rojukan_code . "] telah diterima dan memerlukan semakan dan pengesahan dari pihak Tuan/Puan.",
                    'Url' => env('EMAIL_REDIRECT_URL') . 'project/daftar/' . $request->review_url . '/review'
                ];

                for ($i = 0; $i < count($user_result); $i++) {
                    $users = \App\Models\User::select('email')->where('email', $user_result[$i]['email'])->first(); //print_r($users);
                    $users->notify(new SubmitProjectNotification($userData));
                }

                if ($work_flow == 2) {
                    // -------------noitification start---------------------
                    $notification_sub_type = 'Submit_for_penyemak';
                    $notification = 'Permohonan baharu perlu disemak';
                    $this->setApproveNotification($result, $user_result, $notification_sub_type, $notification);
                    // -------------noitification end---------------------
                }
            } else if ($request->usertype == 2) {

                if ($request->workflow == 1) {
                    $result->workflow_status = 2;

                    $role = \App\Models\Role::where('name', 'PENYEMAK')->first();
                    $user_result = \App\Models\User::select('users.email', 'users.id')
                        ->where('negeri_id', $request->negeri)
                        ->whereHas('userTypeRole', function ($query) use ($role) {
                            $query->where('role_id', $role->id);
                        })
                        ->get();

                    // -------------noitification start---------------------
                    $notification_sub_type = 'Submit_for_penyemak';
                    $notification = 'Permohonan baharu perlu disemak';
                    $this->setApproveNotification($result, $user_result, $notification_sub_type, $notification);
                    // -------------noitification end---------------------
                } else if ($request->workflow == 3) {
                    $result->workflow_status = 4;
                    $result->penyemak = $request->user_id;
                    $result->penyemak_catatan = $request->catatn;
                    $result->penyemak_review_date = Carbon::now()->format('Y-m-d H:i:s');

                    $role = \App\Models\Role::where('name', 'PENYEMAK 1')->first();
                    $user_result = \App\Models\User::select('users.email', 'users.id')
                        ->where('negeri_id', $request->negeri)
                        ->whereHas('userTypeRole', function ($query) use ($role) {
                            $query->where('role_id', $role->id);
                        })
                        ->get();

                    // -------------noitification start---------------------
                    $notification_sub_type = 'Submit_for_penyemak1';
                    $notification = 'Permohonan baharu perlu disemak';
                    $this->setApproveNotification($result, $user_result, $notification_sub_type, $notification);
                    // -------------noitification end---------------------
                } else {
                    if ($request->workflow == 5) {
                        $work_flow = 3;
                    } else if ($request->workflow == 8) {
                        $work_flow = 6;
                    } else if ($request->workflow == 12) {
                        $work_flow = 10;
                    } else if ($request->workflow == 15) {
                        $work_flow = 13;
                    } else if ($request->workflow == 18) {
                        $work_flow = 14;
                    } else {
                        $work_flow = 7;
                    }

                    $result->workflow_status = $work_flow;
                    $result->penyemak_1 = $request->user_id;
                    $result->penyemak_1_catatan = $request->catatn;
                    $result->penyemak_1_review_date = Carbon::now()->format('Y-m-d H:i:s');

                    $role = \App\Models\Role::where('name', 'PENYEMAK 2')->first();
                    $user_result = \App\Models\User::select('users.email', 'users.id')
                        ->where('bahagian_id', $request->bahagian_pemilik)
                        ->whereHas('userTypeRole', function ($query) use ($role) {
                            $query->where('role_id', $role->id);
                        })
                        ->get();


                    if ($work_flow == 7) {
                        // -------------noitification start---------------------
                        $notification_sub_type = 'Submit_for_penyemak2';
                        $notification = 'Permohonan baharu perlu disemak';
                        $this->setApproveNotification($result, $user_result, $notification_sub_type, $notification);
                        // -------------noitification end------------------------
                    }
                }

                $userData = [
                    'comment' => "Permohonan projek bagi No.Rujukan [" . $request->rojukan_code . "] telah diterima dan memerlukan semakan dan pengesahan dari pihak Tuan/Puan.",
                    'Url' => env('EMAIL_REDIRECT_URL') . 'project/daftar/' . $request->review_url . '/review'
                ];

                for ($i = 0; $i < count($user_result); $i++) {
                    $users = \App\Models\User::select('email')->where('email', $user_result[$i]['email'])->first(); //print_r($users);
                    $users->notify(new SubmitProjectNotification($userData));
                }
            } else if ($request->usertype == 3 || $request->usertype == 4) {
                // print_r($request->workflow);exit;
                if ($request->workflow == 1) {
                    $result->workflow_status = 2;

                    $role = \App\Models\Role::where('name', 'PENYEMAK')->first();
                    $user_result = \App\Models\User::select('users.email', 'users.id')
                        ->where('bahagian_id', $request->bahagian_pemilik)
                        ->whereHas('userTypeRole', function ($query) use ($role) {
                            $query->where('role_id', $role->id);
                        })
                        ->get();

                    // -------------noitification start---------------------
                    $notification_sub_type = 'Submit_for_penyemak';
                    $notification = 'Permohonan baharu perlu disemak';
                    $this->setApproveNotification($result, $user_result, $notification_sub_type, $notification);
                    // -------------noitification end------------------------
                } else if ($request->workflow == 3) {
                    $result->workflow_status = 4;
                    $result->penyemak = $request->user_id;
                    $result->penyemak_catatan = $request->catatn;
                    $result->penyemak_review_date = Carbon::now()->format('Y-m-d H:i:s');

                    $role = \App\Models\Role::where('name', 'PENYEMAK 1')->first();
                    $user_result = \App\Models\User::select('users.email', 'users.id')
                        ->where('bahagian_id', $request->bahagian_pemilik)
                        ->whereHas('userTypeRole', function ($query) use ($role) {
                            $query->where('role_id', $role->id);
                        })
                        ->get();

                    // -------------noitification start---------------------
                    $notification_sub_type = 'Submit_for_penyemak1';
                    $notification = 'Permohonan baharu perlu disemak';
                    $this->setApproveNotification($result, $user_result, $notification_sub_type, $notification);
                    // -------------noitification end------------------------

                } else if ($request->workflow == 6) {
                    $result->workflow_status = 7;
                    $result->penyemak_1 = $request->user_id;
                    $result->penyemak_1_catatan = $request->catatn;
                    $result->penyemak_1_review_date = Carbon::now()->format('Y-m-d H:i:s');

                    $role = \App\Models\Role::where('name', 'PENYEMAK 2')->first();
                    $user_result = \App\Models\User::select('users.email', 'users.id')
                        ->where('bahagian_id', $request->bahagian_pemilik)
                        ->whereHas('userTypeRole', function ($query) use ($role) {
                            $query->where('role_id', $role->id);
                        })
                        ->get();

                    // -------------noitification start---------------------
                    $notification_sub_type = 'Submit_for_penyemak2';
                    $notification = 'Permohonan baharu perlu disemak';
                    $this->setApproveNotification($result, $user_result, $notification_sub_type, $notification);
                    // -------------noitification end------------------------
                } else if ($request->workflow == 10) {
                    $result->workflow_status = 11;
                    $result->penyemak_2 = $request->user_id;
                    $result->penyemak_2_catatan = $request->catatn;
                    $result->penyemak_2_review_date = Carbon::now()->format('Y-m-d H:i:s');

                    $role = \App\Models\Role::where('name', 'PENGESAH')->first();
                    $user_result = \App\Models\User::select('users.email', 'users.id')
                        ->where('bahagian_id', $request->bahagian_pemilik)
                        ->whereHas('userTypeRole', function ($query) use ($role) {
                            $query->where('role_id', $role->id);
                        })
                        ->get();

                    // -------------noitification start---------------------
                    $notification_sub_type = 'Submit_for_pengesah';
                    $notification = 'Permohonan baharu perlu disahkan';
                    $this->setApproveNotification($result, $user_result, $notification_sub_type, $notification);
                    // -------------noitification end------------------------
                } else {
                    if ($request->workflow == 5) {
                        $work_flow = 3;
                    } else if ($request->workflow == 8) {
                        $work_flow = 6;
                    } else if ($request->workflow == 12) {
                        $work_flow = 10;
                    } else if ($request->workflow == 15) {
                        $work_flow = 13;
                    } else if ($request->workflow == 13 || $request->workflow == 18) {
                        $work_flow = 14;
                    } else {
                        $work_flow = 7;
                    }

                    $result->workflow_status = $work_flow;
                    $result->pengesah = $request->user_id;
                    $result->pengesah_catatan = $request->catatn;
                    $result->pengesah_review_date = Carbon::now()->format('Y-m-d H:i:s');

                    $result_bkor = DB::table('ref_bahagian')->select('id')->where('acym', 'BKOR')->first();


                    $user_result = \App\Models\User::select('users.email', 'users.id')
                        ->where('bahagian_id', $result_bkor->id)->get();



                    if ($work_flow == 14) {
                        // -------------noitification start---------------------
                        $notification_sub_type = 'Submit_for_peraku';
                        $notification = 'Permohonan baharu perlu diperakui';
                        $this->setApproveNotification($result, $user_result, $notification_sub_type, $notification);
                        // -------------noitification end------------------------
                    }
                }

                $userData = [
                    'comment' => "Permohonan projek bagi No.Rujukan [" . $request->rojukan_code . "] telah diterima dan memerlukan semakan dan pengesahan dari pihak Tuan/Puan.",
                    'Url' => env('EMAIL_REDIRECT_URL') . 'project/daftar/' . $request->review_url . '/review'
                ];

                for ($i = 0; $i < count($user_result); $i++) {
                    $users = \App\Models\User::select('email')->where('email', $user_result[$i]['email'])->first(); //print_r($users);
                    $users->notify(new SubmitProjectNotification($userData));
                }
            } else {
            }

            $result->update();

            // approve data data store

            $user_data = DB::table('users')
                ->join('ref_jawatan', 'ref_jawatan.id', '=', 'users.jawatan_id')
                ->select('users.*', 'ref_jawatan.nama_jawatan')->where('users.id', $request->user_id)->first();
            $no_rojukan_data = DB::table('projects')->select('no_rujukan')->where('id', $request->id)->first();
            $logData = [
                'user_id' => $request->user_id,
                'section_name' => 'PERAKUAN_submit',
                'projek_id' => $request->id,
                'modul' => 'Permohonan Projek',
                'user_ic_no' => $user_data->no_ic,
                'user_jawatan' => $user_data->nama_jawatan,
                'user_name' => $user_data->name,
                'no_rujukan' => $no_rojukan_data->no_rujukan,
            ];
            DB::connection(env('DB_CONNECTION_AUDIT'))->table('projek_log')->insert($logData);

            return response()->json([
                'code' => '200',
                'status' => 'submitted'
            ]);
        } catch (\Throwable $th) {
            logger()->error($th->getMessage());

            //------------ error log store and email --------------------

            $body = [
                'application_name' => env('APP_NAME'),
                'application_type' => Agent::isPhone(),
                'url' => request()->fullUrl(),
                'error_log' => $th->getMessage(),
                'error_code' => $th->getCode(),
                'ip_address' =>  request()->ip(),
                'user_agent' => request()->userAgent(),
                'email' => env('ERROR_EMAIL'),
            ];

            CallApi($body);

            //------------- end of store and email -----------------------

            return response()->json([
                'code' => '500',
                'status' => 'Failed',
                'error' => $th,
            ]);
        }
    }

    public function setApproveNotification($result, $user_result, $notification_sub_type, $notification)
    {
        for ($i = 0; $i < count($user_result); $i++) {
            // -------------noitification start---------------------

            $notification_data = [
                'user_id' => $user_result[$i]['id'],
                'notification_type' => 2,
                'notification_sub_type' => $notification_sub_type,
                'notification' => $notification,
                'dibuat_pada' => Carbon::now()->format('Y-m-d H:i:s'),
                'dibuat_oleh' => $user_result[$i]['id'],
                'dikemaskini_oleh' => $user_result[$i]['id'],
                'negeri_id' => $result['negeri_id'],
                'bahagian_id' => $result['bahagian_pemilik'],
                'dikemaskini_pada' => Carbon::now()->format('Y-m-d H:i:s'),
            ];

            DB::connection(env('DB_CONNECTION'))->table('notification')->insert($notification_data);
            // -------------noitification end---------------------
        }
    }

    // public function sendSubmitMail(Request $request)
    // {
    //     try {

    //         if($request->usertype==1 && $request->userRole==1)
    //         {
    //             $user_result = \App\Models\User::select('users.email')
    //                 ->join('user_peranan', 'user_peranan.user_id', '=', 'users.id')
    //                 ->join('master_peranan', 'master_peranan.id', '=', 'user_peranan.peranan_id')
    //                 ->where('negeri_id', $request->negeri)
    //                 ->where('master_peranan.penyedia',1)->get();

    //                 $userData = [
    //                     'comment' => "Permohonan projek bagi No.Rujukan [".$request->rojukan_code."] telah diterima dan memerlukan semakan dan pengesahan dari pihak Tuan/Puan.",
    //                     'Url' => env('EMAIL_REDIRECT_URL').'project/daftar/'.$request->review_url.'/review'
    //                 ];

    //                 for($i=0;$i<count($user_result);$i++)
    //                 {
    //                     $users=\App\Models\User::select('email')->where('email', $user_result[$i]['email'])->first(); //print_r($users);
    //                     $users->notify(new SubmitProjectNotification($userData));
    //                 }
    //         }
    //         else if($request->usertype==2 )
    //         { 

    //             if($request->workflow==1)
    //            {
    //                 $user_result = \App\Models\User::select('users.email')
    //                 ->join('user_peranan', 'user_peranan.user_id', '=', 'users.id')
    //                 ->join('master_peranan', 'master_peranan.id', '=', 'user_peranan.peranan_id')
    //                 ->where('negeri_id', $request->negeri)
    //                 ->where('master_peranan.penyedia',1)->get();
    //            }
    //            else if($request->workflow==3)
    //            {
    //                 $user_result = \App\Models\User::select('users.email')
    //                 ->join('user_peranan', 'user_peranan.user_id', '=', 'users.id')
    //                 ->join('master_peranan', 'master_peranan.id', '=', 'user_peranan.peranan_id')
    //                 ->where('negeri_id', $request->negeri)
    //                 ->where('master_peranan.penyedia',1)->get();
    //            }
    //            else
    //            {
    //                 $user_result = \App\Models\User::select('users.email')
    //                 ->join('user_peranan', 'user_peranan.user_id', '=', 'users.id')
    //                 ->join('master_peranan', 'master_peranan.id', '=', 'user_peranan.peranan_id')
    //                 ->where('bahagian_id', $request->bahagian_pemilik)
    //                 ->orwhere('master_peranan.penyemak_1',1)
    //                 ->where('master_peranan.penyemak_2',1)->get();
    //            }

    //            $userData = [
    //             'comment' => "Permohonan projek bagi No.Rujukan [".$request->rojukan_code."] telah diterima dan memerlukan semakan dan pengesahan dari pihak Tuan/Puan.",
    //             'Url' => env('EMAIL_REDIRECT_URL').'project/daftar/'.$request->review_url.'/review'
    //             ];

    //             for($i=0;$i<count($user_result);$i++)
    //             {
    //                 $users=\App\Models\User::select('email')->where('email', $user_result[$i]['email'])->first(); //print_r($users);
    //                 $users->notify(new SubmitProjectNotification($userData));
    //             }
    //         }
    //         else if($request->usertype==3 || $request->usertype==4 )
    //         { 
    //            // print_r($request->workflow);exit;
    //            if($request->workflow==1)
    //            {
    //               $user_result = \App\Models\User::select('users.email')
    //                 ->join('user_peranan', 'user_peranan.user_id', '=', 'users.id')
    //                 ->join('master_peranan', 'master_peranan.id', '=', 'user_peranan.peranan_id')
    //                 ->where('bahagian_id', $request->bahagian_pemilik)
    //                 ->where('master_peranan.penyemak_1',1)->get();
    //            }
    //             else if($request->workflow==3)
    //             {
    //                $user_result = \App\Models\User::select('users.email')
    //                ->join('user_peranan', 'user_peranan.user_id', '=', 'users.id')
    //                ->join('master_peranan', 'master_peranan.id', '=', 'user_peranan.peranan_id')
    //                ->where('bahagian_id', $request->bahagian_pemilik)
    //                ->where('master_peranan.penyemak_2',1)->get();
    //             }
    //             else if($request->workflow==6)
    //             {
    //                $user_result = \App\Models\User::select('users.email')
    //                ->join('user_peranan', 'user_peranan.user_id', '=', 'users.id')
    //                ->join('master_peranan', 'master_peranan.id', '=', 'user_peranan.peranan_id')
    //                ->where('bahagian_id', $request->bahagian_pemilik)
    //                ->where('master_peranan.pengesah',1)->get();
    //             }
    //             else if($request->workflow==10)
    //             {
    //                $user_result = \App\Models\User::select('users.email')
    //                ->join('user_peranan', 'user_peranan.user_id', '=', 'users.id')
    //                ->join('master_peranan', 'master_peranan.id', '=', 'user_peranan.peranan_id')
    //                ->where('bahagian_id', $request->bahagian_pemilik)
    //                ->where('master_peranan.pengesah',1)->get();
    //             }
    //             else
    //             {
    //                 $result_bkor = DB::table('ref_bahagian')->select('id')->where('kod_bahagian','BKOR')->first();
    //                 $user_result = \App\Models\User::select('users.email')
    //                              ->where('bahagian_id', $result_bkor->id)->get();
    //             }

    //             $userData = [
    //                 'comment' => "Permohonan projek bagi No.Rujukan [".$request->rojukan_code."] telah diterima dan memerlukan semakan dan pengesahan dari pihak Tuan/Puan.",
    //                 'Url' => env('EMAIL_REDIRECT_URL').'project/daftar/'.$request->review_url.'/review'
    //             ];

    //             for($i=0;$i<count($user_result);$i++)
    //             {
    //                 $users=\App\Models\User::select('email')->where('email', $user_result[$i]['email'])->first(); //print_r($users);
    //                 $users->notify(new SubmitProjectNotification($userData));
    //             }

    //         }
    //         else
    //         { 

    //         }
    //             return response()->json([
    //                 'code' => '200',
    //                 'status' => 'submitted'
    //             ]);

    //     } catch (\Throwable $th) {
    //         logger()->error($th->getMessage());

    //         return response()->json([
    //             'code' => '500',
    //             'status' => 'Failed',
    //             'error' => $th,
    //         ]);
    //     }
    // }

    public function setApprove(Request $request)
    {
        //print_r($request->all());exit;
        try {

            $result = \App\Models\Project::where('id', $request->id)->first();

            if ($request->usertype == 2) {
                if ($request->workflow == 2) {
                    $result->workflow_status = 3;
                    $result->penyemak = $request->user_id;
                    $result->penyemak_review_date = Carbon::now()->format('Y-m-d H:i:s');
                } else {
                    $result->workflow_status = 6;
                    $result->penyemak_1 = $request->user_id;
                    $result->penyemak_1_review_date = Carbon::now()->format('Y-m-d H:i:s');
                }
            } else if ($request->usertype == 3 || $request->usertype == 4) {
                if ($request->workflow == 2) {
                    $result->workflow_status = 3;
                    $result->penyemak = $request->user_id;
                    $result->penyemak_review_date = Carbon::now()->format('Y-m-d H:i:s');
                } else if ($request->workflow == 4) {
                    $result->workflow_status = 6;
                    $result->penyemak_1 = $request->user_id;
                    $result->penyemak_1_review_date = Carbon::now()->format('Y-m-d H:i:s');
                } else if ($request->workflow == 7) {
                    $result->workflow_status = 10;
                    $result->penyemak_2 = $request->user_id;
                    $result->penyemak_2_review_date = Carbon::now()->format('Y-m-d H:i:s');
                } else {
                    $result->workflow_status = 13;
                    $result->pengesah = $request->user_id;
                    $result->pengesah_review_date = Carbon::now()->format('Y-m-d H:i:s');
                }
            } else {
            }
            $result->update();

            $user_data = DB::table('users')
                ->join('ref_jawatan', 'ref_jawatan.id', '=', 'users.jawatan_id')
                ->select('users.*', 'ref_jawatan.nama_jawatan')->where('users.id', $request->user_id)->first();
            $no_rojukan_data = DB::table('projects')->select('no_rujukan')->where('id', $request->id)->first();

            $logData = [
                'user_id' => $request->user_id,
                'section_name' => 'Brif_Assignment',
                'projek_id' => $request->id,
                'modul' => 'Permohonan Projek',
                'user_ic_no' => $user_data->no_ic,
                'user_jawatan' => $user_data->nama_jawatan,
                'user_name' => $user_data->name,
                'no_rujukan' => $no_rojukan_data->no_rujukan,
            ];
            DB::connection(env('DB_CONNECTION_AUDIT'))->table('projek_log')->insert($logData);
        } catch (\Throwable $th) {
            logger()->error($th->getMessage());


            //------------ error log store and email --------------------

            $body = [
                'application_name' => env('APP_NAME'),
                'application_type' => Agent::isPhone(),
                'url' => request()->fullUrl(),
                'error_log' => $th->getMessage(),
                'error_code' => $th->getCode(),
                'ip_address' =>  request()->ip(),
                'user_agent' => request()->userAgent(),
                'email' => env('ERROR_EMAIL'),
            ];

            CallApi($body);

            //------------- end of store and email -----------------------

            return response()->json([
                'code' => '500',
                'status' => 'Failed',
                'error' => $th,
            ]);
        }
    }

    public function setSusunanStatus(Request $request)
    {
        //Log::info($request->all());
        try {

            $result = \App\Models\Project::where('id', $request->id)->first();

            if ($request->workflow == 6) {
                $result->susunan_status = 1;
                $result->penyemak_1_catatan = $request->catatn;
            } else if ($request->workflow == 13) {
                $result->susunan_status = 2;
                $result->pengesah_catatan = $request->catatn;
            } else {
                $result->susunan_status = 3;
                $result->peraku_catatan = $request->catatn;
            }

            $result->dikemaskini_oleh = $request->user_id;
            $result->dikemaskini_pada = Carbon::now()->format('Y-m-d H:i:s');
            $result->update();

            $user_data = DB::table('users')
                ->join('ref_jawatan', 'ref_jawatan.id', '=', 'users.jawatan_id')
                ->select('users.*', 'ref_jawatan.nama_jawatan')->where('users.id', $request->user_id)->first();
            $no_rojukan_data = DB::table('projects')->select('no_rujukan')->where('id', $request->id)->first();

            $logData = [
                'user_id' => $request->user_id,
                'section_name' => 'Susunan_status',
                'projek_id' => $request->id,
                'modul' => 'Permohonan Projek',
                'user_ic_no' => $user_data->no_ic,
                'user_jawatan' => $user_data->nama_jawatan,
                'user_name' => $user_data->name,
                'no_rujukan' => $no_rojukan_data->no_rujukan,
            ];
            DB::connection(env('DB_CONNECTION_AUDIT'))->table('projek_log')->insert($logData);
        } catch (\Throwable $th) {
            logger()->error($th->getMessage());


            //------------ error log store and email --------------------

            $body = [
                'application_name' => env('APP_NAME'),
                'application_type' => Agent::isPhone(),
                'url' => request()->fullUrl(),
                'error_log' => $th->getMessage(),
                'error_code' => $th->getCode(),
                'ip_address' =>  request()->ip(),
                'user_agent' => request()->userAgent(),
                'email' => env('ERROR_EMAIL'),
            ];

            CallApi($body);

            //------------- end of store and email -----------------------

            return response()->json([
                'code' => '500',
                'status' => 'Failed',
                'error' => $th,
            ]);
        }
    }

    public function HanterProjectData(Request $request)
    {
        //Log::info($request->all());
        try {

            if ($request->susunan_text) {

                foreach ($request->susunan_text as $susunandata) {
                    $sub_json = json_decode($susunandata, TRUE); //print_r($sub_json);
                    if ($sub_json['susunan'] > 0 && $sub_json['susunan'] != '') {
                       // $this->approveSusunanProject($sub_json);
                    }
                }

                return response()->json([
                    'code' => '200',
                    'status' => 'Success'
                ]);
            }
        } catch (\Throwable $th) {
            logger()->error($th->getMessage());
            //------------ error log store and email --------------------
            $body = [
                'application_name' => env('APP_NAME'),
                'application_type' => Agent::isPhone(),
                'url' => request()->fullUrl(),
                'error_log' => $th->getMessage(),
                'error_code' => $th->getCode(),
                'ip_address' =>  request()->ip(),
                'user_agent' => request()->userAgent(),
                'email' => env('ERROR_EMAIL'),
            ];
            CallApi($body);
            //------------- end of store and email -----------------------
            return response()->json([
                'code' => '500',
                'status' => 'Failed',
                'error' => $th,
            ]);
        }
    }

    public function approveSusunanProject($data)
    {

        try {

            $result = \App\Models\Project::where('id', $data['id'])->first();

            if ($data['status'] == 6) {
                $result->workflow_status = 7;
                $result->penyemak_1 = $data['user_id'];
                $result->penyemak_1_review_date = Carbon::now()->format('Y-m-d H:i:s');
                $review_url = $data['id'] . '/' . '7' . '/' . $data['user_id'];

                $user_result = \App\Models\User::select('users.email', 'users.id')
                    ->join('user_peranan', 'user_peranan.user_id', '=', 'users.id')
                    ->join('master_peranan', 'master_peranan.id', '=', 'user_peranan.peranan_id')
                    ->where('bahagian_id', $data['bahagian'])
                    ->where('master_peranan.penyemak_2', 1)->get();

                // -------------noitification start---------------------
                $notification_sub_type = 'Submit_for_penyemak2';
                $notification = 'Permohonan baharu perlu disemak';
                $this->setApproveNotification($result, $user_result, $notification_sub_type, $notification);
                // -------------noitification end------------------------

                $userData = [
                    'comment' => "Permohonan projek bagi No.Rujukan [" . $data['rujukan'] . "] telah diterima dan memerlukan semakan dan pengesahan dari pihak Tuan/Puan.",
                    'Url' => env('EMAIL_REDIRECT_URL') . 'project/daftar/' . $review_url . '/review'
                ];

                for ($i = 0; $i < count($user_result); $i++) {
                    $users = \App\Models\User::select('email')->where('email', $user_result[$i]['email'])->first(); //print_r($users);
                    $users->notify(new SubmitProjectNotification($userData));
                }
            } else if ($data['status'] == 13) {
                $result->workflow_status = 14;
                $result->pengesah = $data['user_id'];
                $result->pengesah_review_date = Carbon::now()->format('Y-m-d H:i:s');
                $review_url = $data['id'] . '/' . '7' . '/' . $data['user_id'];
                $result_bkor = DB::table('ref_bahagian')->select('id')->where('acym', 'BKOR')->first();

                $user_result = \App\Models\User::select('users.email', 'users.id')
                    ->where('bahagian_id', $result_bkor->id)->get();

                // -------------noitification start---------------------
                $notification_sub_type = 'Submit_for_peraku';
                $notification = 'Permohonan baharu perlu diperakui';
                $this->setApproveNotification($result, $user_result, $notification_sub_type, $notification);
                // -------------noitification end------------------------

                $userData = [
                    'comment' => "Permohonan projek bagi No.Rujukan [" . $data['rujukan'] . "] telah diterima dan memerlukan semakan dan pengesahan dari pihak Tuan/Puan.",
                    'Url' => env('EMAIL_REDIRECT_URL') . 'project/daftar/' . $review_url . '/review'
                ];

                for ($i = 0; $i < count($user_result); $i++) {
                    $users = \App\Models\User::select('email')->where('email', $user_result[$i]['email'])->first(); //print_r($users);
                    $users->notify(new SubmitProjectNotification($userData));
                }
            } else {
                $result->workflow_status = 17;
                $result->peraku = $data['user_id'];
                $result->peraku_review_date = Carbon::now()->format('Y-m-d H:i:s');

                $section_name = 'PERAKUAN_approve';
                $notification_status = 'Approve_project';
                $notification = 'Permohonan anda telah Diluluskan';

                // -------------noitification start---------------------
                $userDetails = \App\Models\User::where('id', $result['dibuat_oleh'])->with(['jawatan', 'jabatan', 'bahagian', 'kementerian'])->first();
                $notification_data = [
                    'user_id' => $userDetails['id'],
                    'notification_type' => 2,
                    'notification_sub_type' => $notification_status,
                    'notification' => $notification,
                    'dibuat_pada' => Carbon::now()->format('Y-m-d H:i:s'),
                    'dibuat_oleh' => $data['user_id'],
                    'dikemaskini_oleh' => $data['user_id'],
                    'negeri_id' => $result['negeri_id'],
                    'bahagian_id' => $result['bahagian_pemilik'],
                    'dikemaskini_pada' => Carbon::now()->format('Y-m-d H:i:s'),
                ];

                DB::connection(env('DB_CONNECTION'))->table('notification')->insert($notification_data);

                // -------------noitification end---------------------

            }

            $result->update();

            // approve data data store

            $user_data = DB::table('users')
                ->join('ref_jawatan', 'ref_jawatan.id', '=', 'users.jawatan_id')
                ->select('users.*', 'ref_jawatan.nama_jawatan')->where('users.id', $data['user_id'])->first();
            $no_rojukan_data = DB::table('projects')->select('no_rujukan')->where('id', $data['id'])->first();
            $logData = [
                'user_id' => $data['user_id'],
                'section_name' => 'PERAKUAN_submit',
                'projek_id' => $data['id'],
                'modul' => 'Permohonan Projek',
                'user_ic_no' => $user_data->no_ic,
                'user_jawatan' => $user_data->nama_jawatan,
                'user_name' => $user_data->name,
                'no_rujukan' => $no_rojukan_data->no_rujukan,
            ];
            DB::connection(env('DB_CONNECTION_AUDIT'))->table('projek_log')->insert($logData);

            return response()->json([
                'code' => '200',
                'status' => 'submitted'
            ]);
        } catch (\Throwable $th) {
            logger()->error($th->getMessage());

            //------------ error log store and email --------------------

            $body = [
                'application_name' => env('APP_NAME'),
                'application_type' => Agent::isPhone(),
                'url' => request()->fullUrl(),
                'error_log' => $th->getMessage(),
                'error_code' => $th->getCode(),
                'ip_address' =>  request()->ip(),
                'user_agent' => request()->userAgent(),
                'email' => env('ERROR_EMAIL'),
            ];

            CallApi($body);

            //------------- end of store and email -----------------------

            return response()->json([
                'code' => '500',
                'status' => 'Failed',
                'error' => $th,
            ]);
        }
    }


    public function cancelProject(Request $request)
    {
        //print_r($request->all());exit;
        try {
            $user = Auth::user();
            $result = \App\Models\Project::where('id', $request->id)->first();
            //print_r($data);exit;


            $result->penyemak = $request->user_id;
            $result->penyemak_catatan = $request->catatn;
            $result->penyemak_review_date = Carbon::now()->format('Y-m-d H:i:s');

            $result->workflow_status = 20;
            $result->dikemaskini_oleh = $request->user_id;
            $result->dikemaskini_pada = Carbon::now()->format('Y-m-d H:i:s');
            $result->update();

            $user_data = DB::table('users')
                ->join('ref_jawatan', 'ref_jawatan.id', '=', 'users.jawatan_id')
                ->select('users.*', 'ref_jawatan.nama_jawatan')->where('users.id', $request->user_id)->first();
            $no_rojukan_data = DB::table('projects')->select('no_rujukan')->where('id', $request->id)->first();
            $logData = [
                'user_id' => $request->user_id,
                'section_name' => 'PERAKUAN_cancel',
                'projek_id' => $request->id,
                'modul' => 'Permohonan Projek',
                'user_ic_no' => $user_data->no_ic,
                'user_jawatan' => $user_data->nama_jawatan,
                'user_name' => $user_data->name,
                'no_rujukan' => $no_rojukan_data->no_rujukan,
            ];
            DB::connection(env('DB_CONNECTION_AUDIT'))->table('projek_log')->insert($logData);


            return response()->json([
                'code' => '200',
                'status' => 'Removed'
            ]);
        } catch (\Throwable $th) {
            logger()->error($th->getMessage());

            //------------ error log store and email --------------------

            $body = [
                'application_name' => env('APP_NAME'),
                'application_type' => Agent::isPhone(),
                'url' => request()->fullUrl(),
                'error_log' => $th->getMessage(),
                'error_code' => $th->getCode(),
                'ip_address' =>  request()->ip(),
                'user_agent' => request()->userAgent(),
                'email' => env('ERROR_EMAIL'),
            ];

            CallApi($body);

            //------------- end of store and email -----------------------

            return response()->json([
                'code' => '500',
                'status' => 'Failed',
                'error' => $th,
            ]);
        }
    }

    public function updateProjectStatus(Request $request)
    {
        //print_r($request->all());exit;
        try {
            $result = \App\Models\Project::where('id', $request->id)->first();

            if ($request->type == 1) {
                $kod_projek = generate_project_number($request->id);

                $result->kod_projeck = $kod_projek['kod'];
                $result->kod_asal = $kod_projek['kod_baharu'];
                $result->kod_baharu = $kod_projek['kod_baharu'];

                $result->susunan_status = 3;
                $result->peraku = $request->user_id;
                $result->peraku_catatan = $request->catatan;
                $result->peraku_review_date = Carbon::now()->format('Y-m-d H:i:s');

                $section_name = 'PERAKUAN_approve';

                // $notification_status='Approve_project';
                // $notification='Permohonan anda telah Diluluskan';

            } else {
                if ($request->workflow == 6) {
                    $result->workflow_status = 9;
                    $result->penyemak_1 = $request->user_id;
                    $result->penyemak_1_catatan = $request->catatan;
                    $result->penyemak_1_review_date = Carbon::now()->format('Y-m-d H:i:s');
                    $section_name = 'PERAKUAN_Reject(Penyemak1)';
                } else if ($request->workflow == 13) {
                    $result->workflow_status = 16;
                    $result->pengesah = $request->user_id;
                    $result->pengesah_catatan = $request->catatan;
                    $result->pengesah_review_date = Carbon::now()->format('Y-m-d H:i:s');
                    $section_name = 'PERAKUAN_Reject(Pengasah)';
                } else {
                    $result->workflow_status = 19;
                    $result->peraku = $request->user_id;
                    $result->peraku_catatan = $request->catatan;
                    $result->peraku_review_date = Carbon::now()->format('Y-m-d H:i:s');
                    $section_name = 'PERAKUAN_Reject(BKOR)';
                }

                $notification_status = 'Reject_project';
                $notification = 'Permohonan anda telah ditolak';

                // -------------noitification start---------------------

                $userDetails = \App\Models\User::where('id', $result['dibuat_oleh'])->with(['jawatan', 'jabatan', 'bahagian', 'kementerian'])->first();
                $notification_data = [
                    'user_id' => $userDetails['id'],
                    'notification_type' => 2,
                    'notification_sub_type' => $notification_status,
                    'notification' => $notification,
                    'dibuat_pada' => Carbon::now()->format('Y-m-d H:i:s'),
                    'dibuat_oleh' => $request->user_id,
                    'dikemaskini_oleh' => $request->user_id,
                    'negeri_id' => $result['negeri_id'],
                    'bahagian_id' => $result['bahagian_pemilik'],
                    'dikemaskini_pada' => Carbon::now()->format('Y-m-d H:i:s'),
                ];
                DB::connection(env('DB_CONNECTION'))->table('notification')->insert($notification_data);

                // -------------noitification end---------------------
            }

            $result->update();

            $user_data = DB::table('users')
                ->join('ref_jawatan', 'ref_jawatan.id', '=', 'users.jawatan_id')
                ->select('users.*', 'ref_jawatan.nama_jawatan')->where('users.id', $request->user_id)->first();
            $no_rojukan_data = DB::table('projects')->select('no_rujukan')->where('id', $request->id)->first();
            $logData = [
                'user_id' => $request->user_id,
                'section_name' => $section_name,
                'projek_id' => $request->id,
                'modul' => 'Permohonan Projek',
                'user_ic_no' => $user_data->no_ic,
                'user_jawatan' => $user_data->nama_jawatan,
                'user_name' => $user_data->name,
                'no_rujukan' => $no_rojukan_data->no_rujukan,
            ];
            DB::connection(env('DB_CONNECTION_AUDIT'))->table('projek_log')->insert($logData);

            return response()->json([
                'code' => '200',
                'status' => 'Success',
                'message' => 'Approved/Rejected',
                'data' => $result,
            ]);
        } catch (\Throwable $th) {
            logger()->error($th->getMessage());

            //------------ error log store and email --------------------

            $body = [
                'application_name' => env('APP_NAME'),
                'application_type' => Agent::isPhone(),
                'url' => request()->fullUrl(),
                'error_log' => $th->getMessage(),
                'error_code' => $th->getCode(),
                'ip_address' =>  request()->ip(),
                'user_agent' => request()->userAgent(),
                'email' => env('ERROR_EMAIL'),
            ];

            CallApi($body);

            //------------- end of store and email -----------------------

            return response()->json([
                'code' => '500',
                'status' => 'Failed',
                'error' => $th,
            ]);
        }
    }

    public function getPerakuan(Request $request)
    {

        //print_r($request->all());exit;
        try {
            //code...
            $user = Auth::user();

            $data['project'] = \App\Models\Project::where('id', $request->id)
                ->first();

            $data['penyedia'] = \App\Models\Project::with(['bahagianPemilik', 'jenisKategori', 'createdBy'])
                ->where('id', $request->id)
                ->first();
            $data['penyedia_pejabat'] = $this->getPejabatData($data['penyedia']);

            $data['penyemak'] = DB::table('projects')
                ->join('users', 'users.id', '=', 'projects.penyemak')
                ->select('users.*', 'projects.penyemak_review_date', 'projects.penyemak_catatan')
                ->where('projects.id', '=', $request->id)->first();

            $data['penyemak_pejabat'] = $this->getPejabatDetails($data['penyemak']);


            $data['penyemak1'] = DB::table('projects')
                ->join('users', 'users.id', '=', 'projects.penyemak_1')
                ->select('users.*', 'projects.penyemak_1_review_date', 'projects.penyemak_1_catatan')
                ->where('projects.id', '=', $request->id)->first();

            $data['penyemak1_pejabat'] = $this->getPejabatDetails($data['penyemak1']);


            $data['penyemak2'] = DB::table('projects')
                ->join('users', 'users.id', '=', 'projects.penyemak_2')
                ->select('users.*', 'projects.penyemak_2_review_date', 'projects.penyemak_2_catatan')
                ->where('projects.id', '=', $request->id)->first();

            $data['penyemak2_pejabat'] = $this->getPejabatDetails($data['penyemak2']);

            $data['pengesah'] = DB::table('projects')
                ->join('users', 'users.id', '=', 'projects.pengesah')
                ->select('users.*', 'projects.pengesah_review_date', 'projects.pengesah_catatan')
                ->where('projects.id', '=', $request->id)->first();

            $data['pengesah_pejabat'] = $this->getPejabatDetails($data['pengesah']);

            $data['peraku'] = DB::table('projects')
                ->join('users', 'users.id', '=', 'projects.peraku')
                ->select('users.*', 'projects.peraku_review_date', 'projects.peraku_catatan')
                ->where('projects.id', '=', $request->id)->first();

            $data['peraku_pejabat'] = $this->getPejabatDetails($data['peraku']);

            // print_r($penyemak);exit; 

            return response()->json([
                'code' => '200',
                'status' => 'Success',
                'data' => $data,
            ]);
        } catch (\Throwable $th) {
            logger()->error($th->getMessage());

            //------------ error log store and email --------------------

            $body = [
                'application_name' => env('APP_NAME'),
                'application_type' => Agent::isPhone(),
                'url' => request()->fullUrl(),
                'error_log' => $th->getMessage(),
                'error_code' => $th->getCode(),
                'ip_address' =>  request()->ip(),
                'user_agent' => request()->userAgent(),
                'email' => env('ERROR_EMAIL'),
            ];

            CallApi($body);

            //------------- end of store and email -----------------------

            return response()->json([
                'code' => '500',
                'status' => 'Failed',
                'error' => $th,
            ]);
        }
    }

    public function getPejabatData($data)
    {

        if ($data) {
            $user_type = $this->getUserType($data['createdBy']);
            return $user_type;
        } else {
        }
    }

    public function getPejabatDetails($data)
    {
        $array = json_decode(json_encode($data), true);

        // print "new"; print_r($array); print "<br>";

        if ($array) {
            $user_type = $this->getUserType($array);
            return $user_type;
        } else {
            return "-";
        }
        // exit;
    }

    public function getUserType($data)
    {
        if ($data['daerah_id'] != '') {
            $daerah_data = \App\Models\refDaerah::select('nama_daerah')->where('id', $data['daerah_id'])->get();
            return "JPS" . " " . $daerah_data[0]['nama_daerah'];
        } else if ($data['daerah_id'] == '' && $data['negeri_id'] != '') {
            $negeri_data = \App\Models\refNegeri::select('nama_negeri')->where('id', $data['negeri_id'])->get();
            return "JPS" . " " . $negeri_data[0]['nama_negeri'];
        } else if ($data['bahagian_id'] != '') {
            // return "JPS HQ";
            $bahagian_data = \App\Models\refBahagian::select('nama_bahagian')->where('id', $data['bahagian_id'])->get();
            return "JPS HQ" . " " . $bahagian_data[0]['nama_bahagian'];
        } else if ($data['pajabat_id'] != '') {
            $pejabat_data = PejabatProjek::select('nama_negeri')->where('id', $data['pajabat_id'])->get();
            return $pejabat_data[0]['pejabat_projek'];
        } else {
            return "no";
        }
    }

    public function rmkDataList()
    {
        try {

            $data = RollingPlan::groupBy('rmk')->selectRaw('count(*) as total, rmk')->where('is_active', 1)->get();
            $data1 = RollingPlan::where('is_active', '=', 1)->get();
            $data2 = DB::table('ref_bahagian')->get();
            $kategori_projeck = lookupOption('kategori_project');
            return response()->json([
                'code' => '200',
                'status' => 'Success',
                'data' => $data,
                'data1' => $data1,
                'data2' => $data2,
                'kategory' => $kategori_projeck,
            ]);
        } catch (\Throwable $th) {
            logger()->error($th->getMessage());

            //------------ error log store and email --------------------

            $body = [
                'application_name' => env('APP_NAME'),
                'application_type' => Agent::isPhone(),
                'url' => request()->fullUrl(),
                'error_log' => $th->getMessage(),
                'error_code' => $th->getCode(),
                'ip_address' =>  request()->ip(),
                'user_agent' => request()->userAgent(),
                'email' => env('ERROR_EMAIL'),
            ];

            CallApi($body);

            //------------- end of store and email -----------------------

            return response()->json([
                'code' => '500',
                'status' => 'Failed',
                'error' => $th,
            ]);
        }
    }


    public function FilterprojectOfDaerah(Request $request)
    {
        try {
            $query = \App\Models\Project::query();

            $query->where('daerah_id', $request->daerah);

            if ($request->rmk_value) {
                $query->where('rmk', '=', $request->rmk_value);
            }
            if ($request->rolling_plan) {
                $query->where('rolling_plan_code', '=', $request->rolling_plan);
            }
            if ($request->nama_project) {
                $query->where('nama_projek', 'like', '%' . $request->nama_project . '%');
            }
            if ($request->kod_project) {
                $query->where('kod_projeck', '=', $request->kod_project);
            }
            if ($request->tahun) {
                $query->where('tahun', '=', $request->tahun);
            }
            if ($request->bahagian) {
                $query->where('bahagian_pemilik', '=', $request->bahagian);
            }
            if ($request->no_rajukan) {
                $query->where('no_rujukan', '=', $request->no_rajukan);
            }
            // if($request->negeri)
            // {
            //     $query->where('negeri_id','=',$request->negeri);
            // }
            if ($request->projek_category) {
                $query->where('kategori_Projek', '=', $request->projek_category);
            }

            $query->with(['bahagianPemilik', 'negeri', 'jenisKategori', 'createdBy', 'updatedBy', 'penyemak1', 'penyemak2', 'kewangan', 'rollingPlan', 'daerah', 'penyemak', 'pengesah', 'peraku']);
            $result = $query->get();

            if ($request->tahun) {
                $currentYear = $request->tahun;
            } else {
                $currentYear = date("Y");
            }
            $bayangan = totalBayangan::where('year', $currentYear)->first();

            return response()->json([
                'code' => '200',
                'status' => 'Success',
                'data' => $result,
                'bayangan' => $bayangan
            ]);
        } catch (\Throwable $th) {
            logger()->error($th->getMessage());

            //------------ error log store and email --------------------

            $body = [
                'application_name' => env('APP_NAME'),
                'application_type' => Agent::isPhone(),
                'url' => request()->fullUrl(),
                'error_log' => $th->getMessage(),
                'error_code' => $th->getCode(),
                'ip_address' =>  request()->ip(),
                'user_agent' => request()->userAgent(),
                'email' => env('ERROR_EMAIL'),
            ];

            CallApi($body);

            //------------- end of store and email -----------------------

            return response()->json([
                'code' => '500',
                'status' => 'Failed',
                'error' => $th,
            ]);
        }
    }

    public function FilterprojectOfNegeri(Request $request)
    {
        try {
            $query = \App\Models\Project::query();
            $query->where('negeri_id', $request->negeri);
            $query->where('daerah_id', '=', NULL);

            if ($request->rmk_value) {
                $query->where('rmk', '=', $request->rmk_value);
            }
            if ($request->rolling_plan) {
                $query->where('rolling_plan_code', '=', $request->rolling_plan);
            }
            if ($request->nama_project) {
                $query->where('nama_projek', 'like', '%' . $request->nama_project . '%');
            }
            if ($request->kod_project) {
                $query->where('kod_projeck', '=', $request->kod_project);
            }
            if ($request->tahun) {
                $query->where('tahun', '=', $request->tahun);
            }
            if ($request->bahagian) {
                $query->where('bahagian_pemilik', '=', $request->bahagian);
            }
            if ($request->no_rajukan) {
                $query->where('no_rujukan', '=', $request->no_rajukan);
            }
            // if($request->negeri)
            // {
            //     $query->where('negeri_id','=',$request->negeri);
            // }
            if ($request->projek_category) {
                $query->where('kategori_Projek', '=', $request->projek_category);
            }
            $query->with(['bahagianPemilik', 'negeri', 'jenisKategori', 'createdBy', 'updatedBy', 'penyemak1', 'penyemak2', 'kewangan', 'rollingPlan', 'daerah', 'penyemak', 'pengesah', 'peraku']);
            $result = $query->get();


            $start = 2;
            $end = 4;
            $query1 = \App\Models\Project::query();
            $query1->where('projects.negeri_id', $request->negeri);
            $query1->where('projects.daerah_id', '!=', NULL);
            $query1->where(function ($query) use ($start, $end) {
                $query->where('projects.workflow_status', $start);
                $query->orwhere('projects.workflow_status', $end);
            });

            if ($request->rmk_value) {
                $query1->where('rmk', '=', $request->rmk_value);
            }
            if ($request->rolling_plan) {
                $query1->where('rolling_plan_code', '=', $request->rolling_plan);
            }
            if ($request->nama_project) {
                $query1->where('nama_projek', 'like', '%' . $request->nama_project . '%');
            }
            if ($request->kod_project) {
                $query1->where('kod_projeck', '=', $request->kod_project);
            }
            if ($request->tahun) {
                $query1->where('tahun', '=', $request->tahun);
            }
            if ($request->bahagian) {
                $query1->where('bahagian_pemilik', '=', $request->bahagian);
            }
            if ($request->no_rajukan) {
                $query->where('no_rujukan', '=', $request->no_rajukan);
            }
            // if($request->negeri)
            // {
            //     $query1->where('negeri_id','=',$request->negeri);
            // }
            if ($request->projek_category) {
                $query1->where('kategori_Projek', '=', $request->projek_category);
            }
            $query1->with(['bahagianPemilik', 'negeri', 'jenisKategori', 'createdBy', 'updatedBy', 'penyemak1', 'penyemak2', 'kewangan', 'rollingPlan', 'daerah', 'penyemak', 'pengesah', 'peraku']);
            $result_daerah = $query1->get();

            $result = $result->concat($result_daerah);


            if ($request->tahun) {
                $currentYear = $request->tahun;
            } else {
                $currentYear = date("Y");
            }
            $bayangan = totalBayangan::where('year', $currentYear)->first();

            return response()->json([
                'code' => '200',
                'status' => 'Success',
                'data' => $result,
                'bayangan' => $bayangan
            ]);
        } catch (\Throwable $th) {
            logger()->error($th->getMessage());

            //------------ error log store and email --------------------

            $body = [
                'application_name' => env('APP_NAME'),
                'application_type' => Agent::isPhone(),
                'url' => request()->fullUrl(),
                'error_log' => $th->getMessage(),
                'error_code' => $th->getCode(),
                'ip_address' =>  request()->ip(),
                'user_agent' => request()->userAgent(),
                'email' => env('ERROR_EMAIL'),
            ];

            CallApi($body);

            //------------- end of store and email -----------------------

            return response()->json([
                'code' => '500',
                'status' => 'Failed',
                'error' => $th,
            ]);
        }
    }


    public function FilterprojectOfBahagian(Request $request)
    {
        try {

            $query = \App\Models\Project::query();
            $query->where('projects.bahagian_pemilik', $request->bahagian);
            $query->where('projects.negeri_id', '=', NULL);

            if ($request->rmk_value) {
                $query->where('rmk', '=', $request->rmk_value);
            }
            if ($request->rolling_plan) {
                $query->where('rolling_plan_code', '=', $request->rolling_plan);
            }
            if ($request->nama_project) {
                $query->where('nama_projek', 'like', '%' . $request->nama_project . '%');
            }
            if ($request->kod_project) {
                $query->where('kod_projeck', '=', $request->kod_project);
            }
            if ($request->tahun) {
                $query->where('tahun', '=', $request->tahun);
            }
            if ($request->bahagian) {
                $query->where('bahagian_pemilik', '=', $request->bahagian);
            }
            if ($request->no_rajukan) {
                $query->where('no_rujukan', '=', $request->no_rajukan);
            }
            // if($request->negeri)
            // {
            //     $query->where('negeri_id','=',$request->negeri);
            // }
            if ($request->projek_category) {
                $query->where('kategori_Projek', '=', $request->projek_category);
            }
            $query->with(['bahagianPemilik', 'negeri', 'jenisKategori', 'createdBy', 'updatedBy', 'penyemak1', 'penyemak2', 'kewangan', 'rollingPlan', 'daerah', 'penyemak', 'pengesah', 'peraku']);
            $result = $query->get();

            if ($request->pengesah == 1) {
                $query_peng = \App\Models\Project::query();
                $query_peng->where('projects.bahagian_pemilik', $request->bahagian);
                $query_peng->where('projects.negeri_id', '!=', NULL);
                $query_peng->where('projects.workflow_status', 11);

                if ($request->rmk_value) {
                    $query_peng->where('rmk', '=', $request->rmk_value);
                }
                if ($request->rolling_plan) {
                    $query_peng->where('rolling_plan_code', '=', $request->rolling_plan);
                }
                if ($request->nama_project) {
                    $query_peng->where('nama_projek', 'like', '%' . $request->nama_project . '%');
                }
                if ($request->kod_project) {
                    $query_peng->where('kod_projeck', '=', $request->kod_project);
                }
                if ($request->tahun) {
                    $query_peng->where('tahun', '=', $request->tahun);
                }
                if ($request->bahagian) {
                    $query_peng->where('bahagian_pemilik', '=', $request->bahagian);
                }
                if ($request->no_rajukan) {
                    $query->where('no_rujukan', '=', $request->no_rajukan);
                }
                // if($request->negeri)
                // {
                //     $query_peng->where('negeri_id','=',$request->negeri);
                // }
                if ($request->projek_category) {
                    $query_peng->where('kategori_Projek', '=', $request->projek_category);
                }
                $query_peng->with(['bahagianPemilik', 'negeri', 'jenisKategori', 'createdBy', 'updatedBy', 'penyemak1', 'penyemak2', 'kewangan', 'rollingPlan', 'daerah', 'penyemak', 'pengesah', 'peraku']);
                $result_pengesah = $query_peng->get();
                $result = $result->concat($result_pengesah);
            }

            $query_neg = \App\Models\Project::query();
            $query_neg->where('projects.bahagian_pemilik', $request->bahagian);
            $query_neg->where('projects.negeri_id', '!=', NULL);
            $query_neg->where('projects.workflow_status', 7);

            if ($request->rmk_value) {
                $query_neg->where('rmk', '=', $request->rmk_value);
            }
            if ($request->rolling_plan) {
                $query_neg->where('rolling_plan_code', '=', $request->rolling_plan);
            }
            if ($request->nama_project) {
                $query_neg->where('nama_projek', 'like', '%' . $request->nama_project . '%');
            }
            if ($request->kod_project) {
                $query_neg->where('kod_projeck', '=', $request->kod_project);
            }
            if ($request->tahun) {
                $query_neg->where('tahun', '=', $request->tahun);
            }
            if ($request->bahagian) {
                $query_neg->where('bahagian_pemilik', '=', $request->bahagian);
            }
            if ($request->no_rajukan) {
                $query->where('no_rujukan', '=', $request->no_rajukan);
            }
            // if($request->negeri)
            // {
            //     $query_neg->where('negeri_id','=',$request->negeri);
            // }
            if ($request->projek_category) {
                $query_neg->where('kategori_Projek', '=', $request->projek_category);
            }
            if ($request->no_rajukan) {
                $query_neg->where('kategori_Projek', '=', $request->no_rujukan);
            }
            $query_neg->with(['bahagianPemilik', 'negeri', 'jenisKategori', 'createdBy', 'updatedBy', 'penyemak1', 'penyemak2', 'kewangan', 'rollingPlan', 'daerah', 'penyemak', 'pengesah', 'peraku']);
            $result_negeri = $query_neg->get();

            $result = $result->concat($result_negeri);


            if ($request->tahun) {
                $currentYear = $request->tahun;
            } else {
                $currentYear = date("Y");
            }
            $bayangan = totalBayangan::where('year', $currentYear)->first();

            return response()->json([
                'code' => '200',
                'status' => 'Success',
                'data' => $result,
                'bayangan' => $bayangan
            ]);
        } catch (\Throwable $th) {
            logger()->error($th->getMessage());

            //------------ error log store and email --------------------

            $body = [
                'application_name' => env('APP_NAME'),
                'application_type' => Agent::isPhone(),
                'url' => request()->fullUrl(),
                'error_log' => $th->getMessage(),
                'error_code' => $th->getCode(),
                'ip_address' =>  request()->ip(),
                'user_agent' => request()->userAgent(),
                'email' => env('ERROR_EMAIL'),
            ];

            CallApi($body);

            //------------- end of store and email -----------------------

            return response()->json([
                'code' => '500',
                'status' => 'Failed',
                'error' => $th,
            ]);
        }
    }

    public function FilterprojectOfBkor(Request $request)
    {
        try {
            $query = \App\Models\Project::query();

            if ($request->type == "bkor") {
                $query->where('workflow_status', '>=', 14);
            } else {
                $query->where('dibuat_oleh', $request->id);
            }

            if ($request->rmk_value) {
                $query->where('rmk', '=', $request->rmk_value);
            }
            if ($request->rolling_plan) {
                $query->where('rolling_plan_code', '=', $request->rolling_plan);
            }
            if ($request->nama_project) {
                $query->where('nama_projek', 'like', '%' . $request->nama_project . '%');
            }
            if ($request->kod_project) {
                $query->where('kod_projeck', '=', $request->kod_project);
            }
            if ($request->tahun) {
                $query->where('tahun', '=', $request->tahun);
            }
            if ($request->bahagian) {
                $query->where('bahagian_pemilik', '=', $request->bahagian);
            }
            if ($request->no_rajukan) {
                $query->where('no_rujukan', '=', $request->no_rajukan);
            }
            // if($request->negeri)
            // {
            //     $query->where('negeri_id','=',$request->negeri);
            // }
            if ($request->projek_category) {
                $query->where('kategori_Projek', '=', $request->projek_category);
            }

            $query->with(['bahagianPemilik', 'negeri', 'jenisKategori', 'createdBy', 'updatedBy', 'penyemak1', 'penyemak2', 'kewangan', 'rollingPlan', 'daerah', 'penyemak', 'pengesah', 'peraku']);
            $result = $query->get();

            if ($request->tahun) {
                $currentYear = $request->tahun;
            } else {
                $currentYear = date("Y");
            }
            $bayangan = totalBayangan::where('year', $currentYear)->first();

            return response()->json([
                'code' => '200',
                'status' => 'Success',
                'data' => $result,
                'bayangan' => $bayangan
            ]);
        } catch (\Throwable $th) {
            logger()->error($th->getMessage());


            //------------ error log store and email --------------------

            $body = [
                'application_name' => env('APP_NAME'),
                'application_type' => Agent::isPhone(),
                'url' => request()->fullUrl(),
                'error_log' => $th->getMessage(),
                'error_code' => $th->getCode(),
                'ip_address' =>  request()->ip(),
                'user_agent' => request()->userAgent(),
                'email' => env('ERROR_EMAIL'),
            ];

            CallApi($body);

            //------------- end of store and email -----------------------

            return response()->json([
                'code' => '500',
                'status' => 'Failed',
                'error' => $th,
            ]);
        }
    }

    public function getDashboardData(Request $request)
    {
        try {
            //code...
            $query = \App\Models\Project::query();
            if ($request->type == "daerah") {
                $query->where('daerah_id', '=', $request->search_data);
            }
            if ($request->type == "negeri") {
                $query->where('negeri_id', '=', $request->search_data);
            }
            if ($request->type == "bahagian") {
                $query->where('bahagian_pemilik', '=', $request->search_data);
            }
            $jumlah_count = $query->count();

            $query->where('kategori_Projek', '=', 1);
            $jenis_baharu_count = $query->count();


            $query_sambungan = \App\Models\Project::query();
            if ($request->type == "daerah") {
                $query_sambungan->where('daerah_id', '=', $request->search_data);
            }
            if ($request->type == "negeri") {
                $query_sambungan->where('negeri_id', '=', $request->search_data);
            }
            if ($request->type == "bahagian") {
                $query_sambungan->where('bahagian_pemilik', '=', $request->search_data);
            }
            $query_sambungan->where('kategori_Projek', '=', 2);
            $jenis_sambungan_count = $query_sambungan->count();


            return response()->json([
                'code' => '200',
                'status' => 'Success',
                'jumlah_count' => $jumlah_count,
                'jenis_count' => $jenis_baharu_count,
                'jenis_sambungan_count' => $jenis_sambungan_count,
            ]);
        } catch (\Throwable $th) {
            logger()->error($th->getMessage());

            //------------ error log store and email --------------------

            $body = [
                'application_name' => env('APP_NAME'),
                'application_type' => Agent::isPhone(),
                'url' => request()->fullUrl(),
                'error_log' => $th->getMessage(),
                'error_code' => $th->getCode(),
                'ip_address' =>  request()->ip(),
                'user_agent' => request()->userAgent(),
                'email' => env('ERROR_EMAIL'),
            ];

            CallApi($body);

            //------------- end of store and email -----------------------

            return response()->json([
                'code' => '500',
                'status' => 'Failed',
                'error' => $th,
            ]);
        }
    }

    public function getProjectLog(Request $request)
    {
        try {
            //code...
            if ($request->start && $request->end) {

                $results = DB::connection(env('DB_CONNECTION_AUDIT'))->table('projek_log')
                    ->whereDate('Created_on', '>=', $request->start)
                    ->whereDate('Created_on', '<=', $request->end)
                    ->orderBy('projek_log.id', 'DESC')
                    ->get();
            } else if ($request->selected) {
                if ($request->selected == 1) { // today
                    $today = Carbon::now()->toDateString();
                    $results = DB::connection(env('DB_CONNECTION_AUDIT'))->table('projek_log')
                        ->whereDate('Created_on', '=', $today)
                        ->orderBy('projek_log.id', 'DESC')
                        ->get();
                }
                if ($request->selected == 2) { //seven days ago
                    $sevenDaysAgo = Carbon::now()->subDays(7)->toDateString();
                    $results = DB::connection(env('DB_CONNECTION_AUDIT'))->table('projek_log')
                        ->whereDate('Created_on', '>=', $sevenDaysAgo)
                        ->orderBy('projek_log.id', 'DESC')
                        ->get();
                }
                if ($request->selected == 3) { //30 days ago
                    $thirtyDaysAgo = Carbon::now()->subDays(30)->toDateString();
                    $results = DB::connection(env('DB_CONNECTION_AUDIT'))->table('projek_log')
                        ->whereDate('Created_on', '>=', $thirtyDaysAgo)
                        ->orderBy('projek_log.id', 'DESC')
                        ->get();
                }
                if ($request->selected == 4) { //current month
                    $results = DB::connection(env('DB_CONNECTION_AUDIT'))->table('projek_log')
                        ->whereMonth('Created_on', Carbon::now()->month)
                        ->orderBy('projek_log.id', 'DESC')
                        ->get();
                }
                if ($request->selected == 5) { //previous month
                    $lastMonthStart = Carbon::now()->subMonth()->startOfMonth();
                    $lastMonthEnd = Carbon::now()->subMonth()->endOfMonth();
                    $results = DB::connection(env('DB_CONNECTION_AUDIT'))->table('projek_log')
                        ->whereBetween('Created_on', [$lastMonthStart, $lastMonthEnd])
                        ->orderBy('projek_log.id', 'DESC')
                        ->get();
                }
                if ($request->selected == 6) { //current year
                    $results = DB::connection(env('DB_CONNECTION_AUDIT'))->table('projek_log')
                        ->whereYear('Created_on', Carbon::now()->year)
                        ->orderBy('projek_log.id', 'DESC')
                        ->get();
                }
                if ($request->selected == 7) { //previous year
                    $previous_year = Carbon::now()->year - 1;
                    $results = DB::connection(env('DB_CONNECTION_AUDIT'))->table('projek_log')
                        ->whereYear('Created_on', $previous_year)
                        ->orderBy('projek_log.id', 'DESC')
                        ->get();
                }
            } else {
                $results = DB::connection(env('DB_CONNECTION_AUDIT'))->table('projek_log')
                    ->orderBy('projek_log.id', 'DESC')
                    ->get();
            }


            return response()->json([
                'code' => '200',
                'status' => 'Success',
                'data' => $results,
            ]);
        } catch (\Throwable $th) {
            logger()->error($th->getMessage());

            //------------ error log store and email --------------------

            $body = [
                'application_name' => env('APP_NAME'),
                'application_type' => Agent::isPhone(),
                'url' => request()->fullUrl(),
                'error_log' => $th->getMessage(),
                'error_code' => $th->getCode(),
                'ip_address' =>  request()->ip(),
                'user_agent' => request()->userAgent(),
                'email' => env('ERROR_EMAIL'),
            ];

            CallApi($body);

            //------------- end of store and email -----------------------

            return response()->json([
                'code' => '500',
                'status' => 'Failed',
                'error' => $th,
            ]);
        }
    }


    public function getLoginLog(Request $request)
    {
        try {
            //code...
            if ($request->start && $request->end) {

                $results = DB::connection(env('DB_CONNECTION_AUDIT'))->table('user_logging_audit')
                    ->whereDate('user_logging_audit.created_at', '>=', $request->start)
                    ->whereDate('user_logging_audit.created_at', '<=', $request->end)
                    ->orderBy('user_logging_audit.id', 'DESC')
                    ->get();
            } else if ($request->selected) {
                if ($request->selected == 1) { // today
                    $today = Carbon::now()->toDateString();
                    $results = DB::connection(env('DB_CONNECTION_AUDIT'))->table('user_logging_audit')
                        ->whereDate('user_logging_audit.created_at', '=', $today)
                        ->orderBy('user_logging_audit.id', 'DESC')
                        ->get();
                }
                if ($request->selected == 2) { //seven days ago
                    $sevenDaysAgo = Carbon::now()->subDays(7)->toDateString();
                    $results = DB::connection(env('DB_CONNECTION_AUDIT'))->table('user_logging_audit')
                        ->whereDate('user_logging_audit.created_at', '>=', $sevenDaysAgo)
                        ->orderBy('user_logging_audit.id', 'DESC')
                        ->get();
                }
                if ($request->selected == 3) { //30 days ago
                    $thirtyDaysAgo = Carbon::now()->subDays(30)->toDateString();
                    $results = DB::connection(env('DB_CONNECTION_AUDIT'))->table('user_logging_audit')
                        ->whereDate('user_logging_audit.created_at', '>=', $thirtyDaysAgo)
                        ->orderBy('user_logging_audit.id', 'DESC')
                        ->get();
                }
                if ($request->selected == 4) { //current month
                    $results = DB::connection(env('DB_CONNECTION_AUDIT'))->table('user_logging_audit')
                        ->whereMonth('user_logging_audit.created_at', '=', Carbon::now()->month)
                        ->orderBy('user_logging_audit.id', 'DESC')
                        ->get();
                }
                if ($request->selected == 5) { //previous month
                    $lastMonthStart = Carbon::now()->subMonth()->startOfMonth();
                    $lastMonthEnd = Carbon::now()->subMonth()->endOfMonth();
                    $results = DB::connection(env('DB_CONNECTION_AUDIT'))->table('user_logging_audit')
                        ->whereBetween('user_logging_audit.created_at', [$lastMonthStart, $lastMonthEnd])
                        ->orderBy('user_logging_audit.id', 'DESC')
                        ->get();
                }
                if ($request->selected == 6) { //current year
                    $results = DB::connection(env('DB_CONNECTION_AUDIT'))->table('user_logging_audit')
                        ->whereYear('user_logging_audit.created_at', Carbon::now()->year)
                        ->orderBy('user_logging_audit.id', 'DESC')
                        ->get();
                }
                if ($request->selected == 7) { //previous year
                    $previous_year = Carbon::now()->year - 1;
                    $results = DB::connection(env('DB_CONNECTION_AUDIT'))->table('user_logging_audit')
                        ->whereYear('user_logging_audit.created_at', $previous_year)
                        ->orderBy('user_logging_audit.id', 'DESC')
                        ->get();
                }
            } else {
                $results = DB::connection(env('DB_CONNECTION_AUDIT'))->table('user_logging_audit')
                    ->orderBy('user_logging_audit.id', 'DESC')
                    ->get();
            }


            return response()->json([
                'code' => '200',
                'status' => 'Success',
                'data' => $results,
            ]);
        } catch (\Throwable $th) {
            logger()->error($th->getMessage());

            //------------ error log store and email --------------------

            $body = [
                'application_name' => env('APP_NAME'),
                'application_type' => Agent::isPhone(),
                'url' => request()->fullUrl(),
                'error_log' => $th->getMessage(),
                'error_code' => $th->getCode(),
                'ip_address' =>  request()->ip(),
                'user_agent' => request()->userAgent(),
                'email' => env('ERROR_EMAIL'),
            ];

            CallApi($body);

            //------------- end of store and email -----------------------

            return response()->json([
                'code' => '500',
                'status' => 'Failed',
                'error' => $th,
            ]);
        }
    }

    public function sendUpdateRequest(Request $request)
    {
        try {

            $result = \App\Models\Project::where('id', $request->id)->first();

            if ($request->usertype == 2) {
                if ($request->workflow == 1) {
                } else if ($request->workflow == 3) {
                    $result->penyemak = $request->user_id;
                    $result->penyemak_catatan = $request->comment;
                    $result->penyemak_review_date = Carbon::now()->format('Y-m-d H:i:s');
                } else {
                    $result->penyemak_1 = $request->user_id;
                    $result->penyemak_1_catatan = $request->comment;
                    $result->penyemak_1_review_date = Carbon::now()->format('Y-m-d H:i:s');
                }
            } else if ($request->usertype == 3 || $request->usertype == 4) {
                if ($request->workflow == 1) {
                } else if ($request->workflow == 3) {
                    $result->penyemak = $request->user_id;
                    $result->penyemak_catatan = $request->comment;
                    $result->penyemak_review_date = Carbon::now()->format('Y-m-d H:i:s');
                } else if ($request->workflow == 6) {
                    $result->penyemak_1 = $request->user_id;
                    $result->penyemak_1_catatan = $request->comment;
                    $result->penyemak_1_review_date = Carbon::now()->format('Y-m-d H:i:s');
                } else if ($request->workflow == 10) {
                    $result->penyemak_2 = $request->user_id;
                    $result->penyemak_2_catatan = $request->comment;
                    $result->penyemak_2_review_date = Carbon::now()->format('Y-m-d H:i:s');
                } else {
                    $result->pengesah = $request->user_id;
                    $result->pengesah_catatan = $request->comment;
                    $result->pengesah_review_date = Carbon::now()->format('Y-m-d H:i:s');
                }
            }

            $result->workflow_status = $request->workflow + 2;
            $result->dikemaskini_oleh = $request->user_id;
            $result->dikemaskini_pada = Carbon::now()->format('Y-m-d H:i:s');
            $result->update();

            $users = \App\Models\User::where('id', $result->dibuat_oleh)->first(); // Hantar email kepada Penyedia projek
            $userData = [
                'comment' => $request->comment,
                'user' => $users,
                'project' => $result,
            ];
            $users->notify(new SendUpdationRequest($userData));

            // -------------noitification start---------------------

            $userDetails = \App\Models\User::where('id', $result['dibuat_oleh'])->with(['jawatan', 'jabatan', 'bahagian', 'kementerian'])->first();
            $notification_data = [
                'user_id' => $userDetails['id'],
                'notification_type' => 2,
                'notification_sub_type' => 'Update_project',
                'notification' => ' Permohonan anda perlu dikemaskini',
                'dibuat_pada' => Carbon::now()->format('Y-m-d H:i:s'),
                'dibuat_oleh' => $request->user_id,
                'dikemaskini_oleh' => $request->user_id,
                'negeri_id' => $result['negeri_id'],
                'bahagian_id' => $result['bahagian_pemilik'],
                'dikemaskini_pada' => Carbon::now()->format('Y-m-d H:i:s'),
            ];

            DB::connection(env('DB_CONNECTION'))->table('notification')->insert($notification_data);

            // -------------noitification end---------------------



            $user_data = DB::table('users')
                ->join('ref_jawatan', 'ref_jawatan.id', '=', 'users.jawatan_id')
                ->select('users.*', 'ref_jawatan.nama_jawatan')->where('users.id', $request->user_id)->first();
            $no_rojukan_data = DB::table('projects')->select('no_rujukan')->where('id', $request->id)->first();
            $logData = [
                'user_id' => $request->user_id,
                'section_name' => "PERAKUAN_update_request",
                'projek_id' => $request->id,
                'modul' => 'Permohonan Projek',
                'user_ic_no' => $user_data->no_ic,
                'user_jawatan' => $user_data->nama_jawatan,
                'user_name' => $user_data->name,
                'no_rujukan' => $no_rojukan_data->no_rujukan,
            ];
            DB::connection(env('DB_CONNECTION_AUDIT'))->table('projek_log')->insert($logData);


            $logData = ProjectRequestUpdateTracker::create([
                'project_id' => $request->id,
                'requested_by' => $request->user_id,
                'requested_on' => Carbon::now()->format('Y-m-d H:i:s'),
                'catatan' => $request->catatan,
            ]);
        } catch (\Throwable $th) {
            logger()->error($th->getMessage());

            //------------ error log store and email --------------------

            $body = [
                'application_name' => env('APP_NAME'),
                'application_type' => Agent::isPhone(),
                'url' => request()->fullUrl(),
                'error_log' => $th->getMessage(),
                'error_code' => $th->getCode(),
                'ip_address' =>  request()->ip(),
                'user_agent' => request()->userAgent(),
                'email' => env('ERROR_EMAIL'),
            ];

            CallApi($body);

            //------------- end of store and email -----------------------

            return response()->json([
                'code' => '500',
                'status' => 'Failed',
                'error' => $th,
            ]);
        }
    }

    public function setPriority(Request $request)
    {
        // print_r($request->all());exit;
        try {

            $result = \App\Models\Project::where('id', $request->id)->first();

            if ($request->item == 1) {
                if ($request->negeri) {
                    $result_data = \App\Models\Project::where('negeri_id', $request->negeri)
                        ->where('id', '!=', $request->id)
                        ->where('tahun', $request->tahun)
                        ->where('penyemak1_priority_order', $request->value)->get();
                } else {
                    $result_data = \App\Models\Project::where('bahagian_pemilik', $request->bahagian)
                        ->where('id', '!=', $request->id)
                        ->where('tahun', $request->tahun)
                        ->where('penyemak1_priority_order', $request->value)->get();
                }
                if (count($result_data) > 0) {
                    return response()->json([
                        'code' => '300',
                        'status' => 'Error',
                        'data' => $request->id,
                    ]);
                } else {
                    if ($request->value == 0) {
                        $result->Is_submitted_by_penyemak1 = 0;
                    } else {
                        $result->Is_submitted_by_penyemak1 = 1;
                    }
                    $result->penyemak1_priority_order = $request->value;
                }
            } else  if ($request->item == 2) {
                if ($request->negeri) {
                    $result_data_new = \App\Models\Project::where('negeri_id', $request->negeri)
                        ->where('id', '!=', $request->id)
                        ->where('tahun', $request->tahun)
                        ->where('pengesha_priority_order', $request->value)->get();
                } else {
                    $result_data_new = \App\Models\Project::where('bahagian_pemilik', $request->bahagian)
                        ->where('id', '!=', $request->id)
                        ->where('tahun', $request->tahun)
                        ->where('pengesha_priority_order', $request->value)->get();
                }
                if (count($result_data_new) > 0) {
                    return response()->json([
                        'code' => '300',
                        'status' => 'Error',
                        'data' => count($result_data_new),
                    ]);
                } else {
                    if ($request->value == 0) {
                        $result->Is_submitted_by_pengesha = 0;
                    } else {
                        $result->Is_submitted_by_pengesha = 1;
                    }
                    $result->pengesha_priority_order = $request->value;
                }
            } else {

                if ($request->negeri) {
                    $result_data_new = \App\Models\Project::where('negeri_id', $request->negeri)
                        ->where('id', '!=', $request->id)
                        ->where('tahun', $request->tahun)
                        ->where('peraku_priority_order', $request->value)->get();
                } else {
                    $result_data_new = \App\Models\Project::where('bahagian_pemilik', $request->bahagian)
                        ->where('id', '!=', $request->id)
                        ->where('tahun', $request->tahun)
                        ->where('peraku_priority_order', $request->value)->get();
                }

                if (count($result_data_new) > 0) {
                    return response()->json([
                        'code' => '300',
                        'status' => 'Error',
                        'data' => count($result_data_new),
                    ]);
                } else {
                    if ($request->value == 0) {
                        $result->Is_submitted_by_peraku = 0;
                    } else {
                        $result->Is_submitted_by_peraku = 1;
                    }
                    $result->peraku_priority_order = $request->value;
                }
            }
            $result->update();

            $user_data = DB::table('users')
                ->join('ref_jawatan', 'ref_jawatan.id', '=', 'users.jawatan_id')
                ->select('users.*', 'ref_jawatan.nama_jawatan')->where('users.id', $request->user_id)->first();
            $no_rojukan_data = DB::table('projects')->select('no_rujukan')->where('id', $request->id)->first();
            $logData = [
                'user_id' => $request->user_id,
                'section_name' => "PERAKUAN_set_priority",
                'projek_id' => $request->id,
                'modul' => 'Permohonan Projek',
                'user_ic_no' => $user_data->no_ic,
                'user_jawatan' => $user_data->nama_jawatan,
                'user_name' => $user_data->name,
                'no_rujukan' => $no_rojukan_data->no_rujukan,
            ];
            DB::connection(env('DB_CONNECTION_AUDIT'))->table('projek_log')->insert($logData);

            return response()->json([
                'code' => '200',
                'status' => 'Success',
                'data' => $result,
            ]);
        } catch (\Throwable $th) {
            logger()->error($th->getMessage());

            //------------ error log store and email --------------------

            $body = [
                'application_name' => env('APP_NAME'),
                'application_type' => Agent::isPhone(),
                'url' => request()->fullUrl(),
                'error_log' => $th->getMessage(),
                'error_code' => $th->getCode(),
                'ip_address' =>  request()->ip(),
                'user_agent' => request()->userAgent(),
                'email' => env('ERROR_EMAIL'),
            ];

            CallApi($body);

            //------------- end of store and email -----------------------

            return response()->json([
                'code' => '500',
                'status' => 'Failed',
                'error' => $th,
            ]);
        }
    }



    public function sectionCompleted(Request $request)
    {
        try {
            //code...
            $project = Project::whereId($request->id)->with(['RmkObbSdg', 'outcomeProjects', 'kewangan', 'lokasi', 'vae', 'documenLampiran', 'bahagianPemilik'])->first();
            $BahgainData = Project::whereId($project->bahagian_pemilik)->with(['Bahagian'])->first();
            // dd($project->bahagian_pemilik);
            return response()->json([
                'code' => '200',
                'status' => 'Success',
                'data' => $project,
                'data2' => $BahgainData
            ]);
        } catch (\Throwable $th) {
            logger()->error($th->getMessage());

            //------------ error log store and email --------------------

            $body = [
                'application_name' => env('APP_NAME'),
                'application_type' => Agent::isPhone(),
                'url' => request()->fullUrl(),
                'error_log' => $th->getMessage(),
                'error_code' => $th->getCode(),
                'ip_address' =>  request()->ip(),
                'user_agent' => request()->userAgent(),
                'email' => env('ERROR_EMAIL'),
            ];

            CallApi($body);

            //------------- end of store and email -----------------------

            return response()->json([
                'code' => '500',
                'status' => 'Failed',
                'error' => $th,
            ]);
        }
    }

    public function getSalinProjectList(Request $request)
    {
        try {
            $user = Auth::user();
            $status1 = 9;
            $status2 = 16;
            $status3 = 19;
            $status4 = 20;
            if ($request->usertype == 1) {
                $result = \App\Models\Project::with(['bahagianPemilik', 'jenisKategori', 'createdBy', 'updatedBy', 'penyemak1', 'penyemak2', 'kewangan', 'negeri', 'rollingPlan', 'daerah', 'penyemak', 'pengesah', 'peraku'])
                    ->where('daerah_id', $request->daerah)
                    ->where(function ($query) use ($status1, $status2, $status3, $status4) {
                        $query->where('projects.workflow_status', $status1)
                            ->orwhere('projects.workflow_status', $status2)
                            ->orwhere('projects.workflow_status', $status3)
                            ->orwhere('projects.workflow_status', $status4);
                    })
                    ->orderBy('updated_at', 'DESC')
                    ->get();
            } else if ($request->usertype == 2) {
                $result = \App\Models\Project::with(['bahagianPemilik', 'jenisKategori', 'createdBy', 'updatedBy', 'penyemak1', 'penyemak2', 'kewangan', 'negeri', 'rollingPlan', 'daerah', 'penyemak', 'pengesah', 'peraku'])
                    ->select('projects.*')
                    ->where('projects.negeri_id', $request->negeri)
                    ->where(function ($query) use ($status1, $status2, $status3, $status4) {
                        $query->where('projects.workflow_status', $status1)
                            ->orwhere('projects.workflow_status', $status2)
                            ->orwhere('projects.workflow_status', $status3)
                            ->orwhere('projects.workflow_status', $status4);
                    })
                    ->orderBy('updated_at', 'DESC')
                    ->get();
            } else if ($request->usertype == 3 || $request->usertype == 4) {
                $result = \App\Models\Project::with(['bahagianPemilik', 'jenisKategori', 'createdBy', 'updatedBy', 'penyemak1', 'penyemak2', 'kewangan', 'negeri', 'rollingPlan', 'daerah', 'penyemak', 'pengesah', 'peraku'])
                    ->select('projects.*')
                    ->where('projects.bahagian_pemilik', $request->bahagian)
                    ->where(function ($query) use ($status1, $status2, $status3, $status4) {
                        $query->where('projects.workflow_status', $status1)
                            ->orwhere('projects.workflow_status', $status2)
                            ->orwhere('projects.workflow_status', $status3)
                            ->orwhere('projects.workflow_status', $status4);
                    })
                    ->orderBy('updated_at', 'DESC')
                    ->get();
            } else {
                $result = [];
            }
            return response()->json([
                'code' => '200',
                'status' => 'Success',
                'data' => $result,
            ]);
        } catch (\Throwable $th) {
            logger()->error($th->getMessage());

            //------------ error log store and email --------------------

            $body = [
                'application_name' => env('APP_NAME'),
                'application_type' => Agent::isPhone(),
                'url' => request()->fullUrl(),
                'error_log' => $th->getMessage(),
                'error_code' => $th->getCode(),
                'ip_address' =>  request()->ip(),
                'user_agent' => request()->userAgent(),
                'email' => env('ERROR_EMAIL'),
            ];

            CallApi($body);

            //------------- end of store and email -----------------------

            return response()->json([
                'code' => '500',
                'status' => 'Failed',
                'error' => $th,
            ]);
        }
    }

    public function getPengesahanProjectList(Request $request)
    {
        try {
            $result = \App\Models\Project::with(['bahagianPemilik', 'negeri', 'jenisKategori', 'createdBy', 'updatedBy', 'penyemak1', 'penyemak2', 'kewangan', 'rollingPlan', 'daerah', 'penyemak', 'pengesah', 'peraku'])
                ->where('pengesah', $request->id)
                ->where('workflow_status', 13)
                ->orderBy('updated_at', 'DESC')
                ->get();;
            return response()->json([
                'code' => '200',
                'status' => 'Success',
                'data' => $result,
            ]);
        } catch (\Throwable $th) {
            logger()->error($th->getMessage());

            //------------ error log store and email --------------------

            $body = [
                'application_name' => env('APP_NAME'),
                'application_type' => Agent::isPhone(),
                'url' => request()->fullUrl(),
                'error_log' => $th->getMessage(),
                'error_code' => $th->getCode(),
                'ip_address' =>  request()->ip(),
                'user_agent' => request()->userAgent(),
                'email' => env('ERROR_EMAIL'),
            ];

            CallApi($body);

            //------------- end of store and email -----------------------

            return response()->json([
                'code' => '500',
                'status' => 'Failed',
                'error' => $th,
            ]);
        }
    }

    public function getPerakuProjectList(Request $request)
    {

        //print_r($request->all());exit;
        try {

            $result = \App\Models\Project::with(['bahagianPemilik', 'negeri', 'jenisKategori', 'createdBy', 'updatedBy', 'penyemak1', 'penyemak2', 'kewangan', 'rollingPlan', 'daerah', 'penyemak', 'pengesah', 'peraku'])
                ->where('workflow_status', '>=', 14)
                ->orderBy('updated_at', 'DESC')
                ->get();
            return response()->json([
                'code' => '200',
                'status' => 'Success',
                'data' => $result,
            ]);
        } catch (\Throwable $th) {
            logger()->error($th->getMessage());

            //------------ error log store and email --------------------

            $body = [
                'application_name' => env('APP_NAME'),
                'application_type' => Agent::isPhone(),
                'url' => request()->fullUrl(),
                'error_log' => $th->getMessage(),
                'error_code' => $th->getCode(),
                'ip_address' =>  request()->ip(),
                'user_agent' => request()->userAgent(),
                'email' => env('ERROR_EMAIL'),
            ];

            CallApi($body);

            //------------- end of store and email -----------------------

            return response()->json([
                'code' => '500',
                'status' => 'Failed',
                'error' => $th,
            ]);
        }
    }

    public function updateBayanganData(Request $request)
    {
        try {
            $data = \App\Models\KewanganProjekDetails::where('permohonan_projek_id', $request->id)->first();
            if ($data) {
                $data->Siling_Bayangan = $request->bilangan_value;
                $data->update();

                return response()->json([
                    'code' => '200',
                    'status' => 'Success',
                    'data' => $data,
                ]);
            } else {

                return response()->json([
                    'code' => '500',
                    'status' => 'Error',
                    'data' => 'Not Found',
                ]);
            }
        } catch (\Throwable $th) {
            logger()->error($th->getMessage());

            //------------ error log store and email --------------------

            $body = [
                'application_name' => env('APP_NAME'),
                'application_type' => Agent::isPhone(),
                'url' => request()->fullUrl(),
                'error_log' => $th->getMessage(),
                'error_code' => $th->getCode(),
                'ip_address' =>  request()->ip(),
                'user_agent' => request()->userAgent(),
                'email' => env('ERROR_EMAIL'),
            ];

            CallApi($body);

            //------------- end of store and email -----------------------

            return response()->json([
                'code' => '500',
                'status' => 'Failed',
                'error' => $th,
            ]);
        }
    }

    public function updateTotalBayangan(Request $request)
    {

        Log::info($request->all());

        try {
            $data_update = totalBayangan::where('year', $request->year)->first();
            if ($data_update) {
                $data_update->siling_bayangan = $request->siling_bayangan;
                $data_update->dikemaskini_pada = Carbon::now()->format('Y-m-d H:i:s');
                $data_update->dikemaskini_oleh = $request->user_id;
                $data_update->bkor_id = $request->user_id;
                $data_update->update();

                return response()->json([
                    'code' => '200',
                    'status' => 'Update Success',
                    'data' => $data_update,
                ]);
            } else {
                $data_save = new totalBayangan;
                $data_save->year = $request->year;
                $data_save->siling_bayangan = $request->siling_bayangan;
                $data_save->bkor_id = $request->user_id;
                $data_save->dibuat_oleh = $request->user_id;
                $data_save->dibuat_pada = Carbon::now()->format('Y-m-d H:i:s');
                $data_save->save();

                return response()->json([
                    'code' => '200',
                    'status' => 'Save Success',
                    'data' => $data_save,
                ]);
            }
        } catch (\Throwable $th) {
            logger()->error($th->getMessage());

            //------------ error log store and email --------------------

            $body = [
                'application_name' => env('APP_NAME'),
                'application_type' => Agent::isPhone(),
                'url' => request()->fullUrl(),
                'error_log' => $th->getMessage(),
                'error_code' => $th->getCode(),
                'ip_address' =>  request()->ip(),
                'user_agent' => request()->userAgent(),
                'email' => env('ERROR_EMAIL'),
            ];

            CallApi($body);

            //------------- end of store and email -----------------------

            return response()->json([
                'code' => '500',
                'status' => 'Failed',
                'error' => $th,
            ]);
        }
    }
}
