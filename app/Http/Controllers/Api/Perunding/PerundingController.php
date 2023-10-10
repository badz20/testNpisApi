<?php

namespace App\Http\Controllers\Api\Perunding;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use \App\Models\PemantauanProject;
use Illuminate\Support\Facades\Log;
use App\Models\Perunding\PerundingKewanganPerkara;
use App\Models\Perunding\PerundingKewanganSubPerkara;
use App\Models\Perunding\PerundingKewanganSubSubPerkaraModel;
use App\Models\Perunding\PerundingRekodBayaranModel;
use App\Models\Perunding\PerundingMaklumat;
use App\Models\Perunding\PerundingMaklumatEocp;
use App\Models\Perunding\PerundingMaklumatPerlindungan;
use App\Models\Perunding\PerundingMaklumatSa;
use App\Models\Perunding\PerundingYuran;
use App\Models\Perunding\PerundingYuranSupplimetaries;
use App\Models\Perunding\YuranPerundingPreModel;
use App\Models\Perunding\YuranPerundingTotalModel;
use App\Models\Perunding\PerundingKewanganHistoryModel;
use App\Models\Perunding\PerundingLejjar;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use \App\Models\Units;
use \App\Models\LookupOption;
use \App\Models\PerundingSubSubPerkaraModel;
use \App\Models\PerundingSubPerkaraModel;
use Jenssegers\Agent\Facades\Agent;
use \App\Models\User;
use App\Models\Perunding\PemantauanPerolehan;



class PerundingController extends Controller
{
    //
     /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        try {
            
            $projects =   PemantauanProject::with(['negeri','daerah','bahagianPemilik','perolehan'])->get();

            $data['negeri'] = getAllNegeri();
            $data['bahagian'] = getAllBahagian();
            $data['kod_projects'] = PemantauanProject::distinct()->pluck('kod_projeck');
            $data['jenis_perkhidmatan'] = PemantauanPerolehan::distinct()->pluck('jenis_perkhidmatan');
            $data['kaedah_perolehan'] = PemantauanPerolehan::distinct()->pluck('kaedah_perolehan');
            $data['nama_perunding'] = PemantauanPerolehan::distinct()->pluck('nama_peruding');
            // $data['status_pelaksanaan'] = PemantauanPerolehan::distinct()->pluck('status_pelaksanaan');
            $data['status_pelaksanaan'] = ['Lewat Jadual','Dahulu Jadual','Ikut Jadual'];
            $data['jenis_pembiayaan'] = [];
            $data['kementerian'] = [];
            $data['projects'] = $projects;

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

    public function filteredIndex(Request $request)
    {
        try {

            Log::info($request);
            
            $tempQuery = PemantauanProject::whereHas('perolehan', function ($query) use ($request) {

                if($request->query_kaedah != null) {
                    $query->where('kaedah_perolehan', $request->query_kaedah);
                }

                if($request->query_jenis != null) {
                    $query->where('jenis_perkhidmatan', $request->query_jenis);
                }

                if($request->query_jenis_pembiayaan != null) {
                    $query->where('jenis_pembiayaan', $request->query_jenis_pembiayaan);
                }

                if($request->query_nama_perunding != null) {
                    $query->where('nama_peruding', $request->query_nama_perunding);
                }

                if($request->query_status != null) {
                    $query->where('status_pelaksanaan', $request->query_status);
                }
            });

            if($request->query_nama_projek != null) {
                $tempQuery->where('nama_projek', 'like', $request->query_nama_projek . '%');
            }

            if($request->query_kod_projek != null) {
                $tempQuery->where('kod_projeck', $request->query_kod_projek);
            }

            if($request->query_negeri != null) {
                $tempQuery->where('negeri_id', $request->query_negeri);
            }

            if($request->query_bahagian != null) {
                $tempQuery->where('bahagian_pemilik', $request->query_bahagian);
            }

            $projects = $tempQuery->with(['negeri','daerah','bahagianPemilik','perolehan'])->get();

            return response()->json([
                'code' => '200',
                'status' => 'Success',
                'data' => $projects,
                
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

    public function GetPerkara($id,$perolehan,$bayaran)
    {
        try {
            

            $data['perkara'] = PerundingKewanganPerkara::query()->with(
                                                        [
                                                            'subperkara' => function ($query) use ($bayaran) {
                                                                $query->where('no_bayaran', '=', $bayaran);
                                                            },
                                                            'subsubperkara' => function ($query) use ($bayaran) {
                                                                $query->where('no_bayaran', '=', $bayaran);
                                                            }
                                                        ])
                                                        ->where('pemantauan_id',$id)
                                                        ->where('perolehan',$perolehan)
                                                        ->get(); 
            $new_bayaran = $bayaran-1;    //print_r($new_bayaran);exit;
            $data['pre_sub_perkara'] = [];


            if($new_bayaran>0)
            {
                $data['pre_sub_perkara'] = PerundingKewanganPerkara::query()->with(
                                                                            [
                                                                                'subperkara' => function ($query) use ($new_bayaran) {
                                                                                    $query->where('no_bayaran', '=', $new_bayaran);
                                                                                },
                                                                                'subsubperkara' => function ($query) use ($new_bayaran) {
                                                                                    $query->where('no_bayaran', '=', $new_bayaran);
                                                                                }
                                                                            ])
                                                                            ->where('pemantauan_id',$id)
                                                                            ->where('perolehan',$perolehan)
                                                                            ->get(); 
            }
            
                                                      
                                                        
            $data['units'] = Units::where('IsActive','=',1)->get();
            
            $data['project'] = PerundingMaklumat::where('pemantauan_id',$id)
                                                ->where('perolehan_id',$perolehan)
                                                ->with(['sa','perolehanProject','pemantauanProject'])
                                                ->first();

            $data['perolehan'] = PemantauanPerolehan::where('pemantauan_id',$id)
                            ->where('id',$perolehan)
                            ->with(['pemantauanProject','pemantauanProject.negeri','pemantauanProject.bahagianPemilik'])
                            ->first();
            
            $data['bayaran'] = PerundingRekodBayaranModel::where('pemantauan_id',$id)->where('perolehan',$perolehan)->where('no_bayaran',$bayaran)->first();        


            return response()->json([
                'code' => '200',
                'status' => 'Success',
                'data' => $data                
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

    public function addPerkara(Request $request)
    {
        try {
            
            PerundingKewanganSubPerkara::where('pemantauan_id',$request->project_id)->where('perolehan',$request->perolehan)->delete();
            PerundingKewanganPerkara::where('pemantauan_id',$request->project_id)->where('perolehan',$request->perolehan)->delete();


            foreach($request->perkara_project_details as $skop_project){
                 $skop_json = json_decode($skop_project,TRUE); 
                    $skop_project = PerundingKewanganPerkara::create([
                        'pemantauan_id' => $request->project_id,
                        'perkara' => $skop_json['perkara']['value'],
                        'no_bayaran' => $request->no_bayaran,
                        'perolehan' => $request->perolehan,
                        'dibuat_pada' => Carbon::now()->format('Y-m-d H:i:s'),
                        'dibuat_oleh' => $request->user_id,
                        'dikemaskini_oleh' => $request->user_id,
                        'dikemaskini_pada' => Carbon::now()->format('Y-m-d H:i:s'),
                        'row_status' => 1,
                    ]);

                    if($skop_json['sub_perkara']) {
                         //print_r($skop_json['sub_perkara']);exit;
                        $sub_skop_array=$skop_json['sub_perkara'];
                        foreach($sub_skop_array as $sub_skop){
                            $sub_skop_json = json_decode($sub_skop,TRUE); 

                            PerundingKewanganSubPerkara::create([
                                'pemantauan_id' => $request->project_id,
                                'perkara_id' => $skop_project->id,
                                'no_bayaran' => $request->no_bayaran,
                                'perolehan' => $request->perolehan,
                                'sub_perkara' => $sub_skop_json['value'],
                                'dibuat_pada' => Carbon::now()->format('Y-m-d H:i:s'),
                                'dibuat_oleh' => $request->user_id,
                                'dikemaskini_oleh' => $request->user_id,
                                'dikemaskini_pada' => Carbon::now()->format('Y-m-d H:i:s'),
                                'row_status' => 1,
                            ]); 
                        }
                        
                    }
            }

            $this->updateHistory($request);

            return response()->json([
                'code' => '200',
                'status' => 'Success'                
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

    public function getDeliverables(Request $request)
    {
        try {
            
            $data = lookupOption('perunding_deliverable');
            
            return response()->json([
                'code' => '200',
                'status' => 'Success',
                'data' => $data
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


    public function updatePerkara(Request $request)
    {
        //print_r($request->all());exit;
        try {

            $data=$request->toArray();

            if($request->perkaratext){ 

                // foreach ($request->perkaratext as $perkara) {  
                //     $sub_data = json_decode($perkara,TRUE); 
                //     PerundingSubPerkaraModel::where('pemantauan_id',$request->project_id)
                //                             ->where('perolehan',$request->perolehan)
                //                             ->delete();
                // }

                foreach ($request->perkaratext as $perkaratext) {  
                    $sub_json = json_decode($perkaratext,TRUE); 

                    $sub_perkera = PerundingKewanganSubPerkara::where('id',$sub_json['id'])->where('no_bayaran',$request->no_bayaran)->first();
                        $sub_perkera->unit = $sub_json['unit'];
                        $sub_perkera->no_bayaran = $request->no_bayaran;
                        $sub_perkera->perolehan = $request->perolehan;
                        $sub_perkera->kelulusan_quantity = $sub_json['kelulusan_quantity'];
                        $sub_perkera->kelulusan_kadar = $sub_json['kelulusan_kadar'];
                        $sub_perkera->kelulusan_jumlah = $sub_json['kelulusan_jumlah'];
                        $sub_perkera->terdah_quantity = $sub_json['terdah_quantity'];
                        $sub_perkera->terdah_jumlah = $sub_json['terdah_jumlah'];
                        $sub_perkera->semasa_quantity = $sub_json['semasa_quantity'];
                        $sub_perkera->semasa_jumlah = $sub_json['semasa_jumlah'];
                        $sub_perkera->kumulatif_quantity = $sub_json['kumulatif_quantity'];
                        $sub_perkera->kumulatif_jumlah = $sub_json['kumulatif_jumlah'];
                        $sub_perkera->baki = $sub_json['baki'];
                        $sub_perkera->update();

                    
                    // $terda_data=new PerundingSubPerkaraModel;
                    // $terda_data->pemantauan_id = $request->project_id;
                    // $terda_data->perkara_id = $sub_json['perkara_id'];
                    // $terda_data->perolehan = $request->perolehan;
                    // $terda_data->terdah_quantity = $sub_json['semasa_quantity'];
                    // $terda_data->terdah_jumlah =  $sub_json['semasa_jumlah'];
                    // $terda_data->kumulatif_quantity = $sub_json['kumulatif_quantity'];
                    // $terda_data->kumulatif_jumlah = $sub_json['kumulatif_jumlah'];
                    // $terda_data->save();
                }
            }


            if($request->sub_perkaratext){

                // foreach ($request->sub_perkaratext as $subperkara) { 
                //         $json_data = json_decode($subperkara,TRUE); 
                //         PerundingKewanganSubSubPerkaraModel::where('perkara_id',$json_data['perkara_id'])
                //                                         ->where('sub_perkara_id',$json_data['id'])
                //                                         ->delete();
                // }

                PerundingKewanganSubSubPerkaraModel::where('pemantauan_id',$request->project_id)
                                                    ->where('perolehan',$request->perolehan)
                                                    ->where('no_bayaran',$request->no_bayaran)
                                                    ->delete();

                foreach ($request->sub_perkaratext as $subperkaratext) {  
                    $sub_sub_json = json_decode($subperkaratext,TRUE); 

                    $sub_sub_perkera =new PerundingKewanganSubSubPerkaraModel;
                    $sub_sub_perkera->perkara_id = $sub_sub_json['perkara_id'];
                    $sub_sub_perkera->sub_perkara_id = $sub_sub_json['id'];
                    $sub_sub_perkera->no_bayaran = $request->no_bayaran;
                    $sub_sub_perkera->sub_sub_perkara = $sub_sub_json['sub_subname'];
                    $sub_sub_perkera->pemantauan_id = $request->project_id;
                    $sub_sub_perkera->perolehan = $request->perolehan;
                    $sub_sub_perkera->unit = $sub_sub_json['unit'];
                    $sub_sub_perkera->kelulusan_quantity = $sub_sub_json['kelulusan_quantity'];
                    $sub_sub_perkera->kelulusan_kadar = $sub_sub_json['kelulusan_kadar'];
                    $sub_sub_perkera->kelulusan_jumlah = $sub_sub_json['kelulusan_jumlah'];
                    if($request->no_bayaran>1)
                    {
                        $sub_sub_perkera->terdah_quantity =$sub_sub_json['terdah_quantity'];
                        $sub_sub_perkera->terdah_jumlah = $sub_sub_json['terdah_jumlah'];
                    }else{
                        $sub_sub_perkera->terdah_quantity ='0.00';
                        $sub_sub_perkera->terdah_jumlah = '0.00';
                    }
                    $sub_sub_perkera->semasa_quantity = $sub_sub_json['semasa_quantity'];
                    $sub_sub_perkera->semasa_jumlah = $sub_sub_json['semasa_jumlah'];
                    $sub_sub_perkera->kumulatif_quantity = $sub_sub_json['kumulatif_quantity'];
                    $sub_sub_perkera->kumulatif_jumlah = $sub_sub_json['kumulatif_jumlah'];
                    $sub_sub_perkera->baki = $sub_sub_json['baki'];
                    $sub_sub_perkera->save();

                    //     $sub_sub_terda =new PerundingSubSubPerkaraModel;
                    //     $sub_sub_terda->perkara_id = $sub_sub_json['perkara_id'];
                    //     $sub_sub_terda->sub_perkara_id = $sub_sub_json['id'];
                    //     $sub_sub_terda->pemantauan_id = $request->project_id;
                    //     $sub_sub_terda->perolehan = $request->perolehan;

                    // if($request->no_bayaran>1)
                    // {
                    //     $sub_sub_terda->terdah_quantity =$sub_sub_json['kumulatif_quantity'];
                    //     $sub_sub_terda->terdah_jumlah = $sub_sub_json['kumulatif_jumlah'];
                    // }else{
                    //     $sub_sub_terda->terdah_quantity =$sub_sub_json['semasa_quantity'];
                    //     $sub_sub_terda->terdah_jumlah = $sub_sub_json['semasa_jumlah'];
                    // }

                    //     $sub_sub_terda->kumulatif_quantity = $sub_sub_json['kumulatif_quantity'];
                    //     $sub_sub_terda->kumulatif_jumlah = $sub_sub_json['kumulatif_jumlah'];
                    //     $sub_sub_terda->save();


                }
            }

            $data= PerundingRekodBayaranModel::where('pemantauan_id',$request->project_id)
                                            ->where('no_bayaran',$request->no_bayaran)
                                            ->where('perolehan',$request->perolehan)
                                            ->first();
            $data->inbuhan_balik = $request->semasa_tot;
            $data->update();

            
            $this->updateHistory($request);


            return response()->json([
                'code' => '200',
                'status' => 'Success'                
            ]);

        } catch (\Throwable $th) {

            logger()->error($th->getMessage());

             //------------ error log storef and email --------------------
            
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

    public function ListBayaran($id,$perolehan)
    {
        try {

            $result['bayaran'] = PerundingRekodBayaranModel::where('pemantauan_id',$id)->where('perolehan',$perolehan)->get();     
            $result['perolehan']    = PemantauanPerolehan::whereId($perolehan)->first();


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

    public function addBayaran(Request $request)
    {
        try {

            $bayaran_no = $request->no_bayaran+1;
            if($request->no_bayaran==0)
            {
                $bayaran = new PerundingRekodBayaranModel;   
                $bayaran->no_bayaran =  $bayaran_no;    
                $bayaran->pemantauan_id =  $request->project_id;
                $bayaran->perolehan = $request->perolehan;    
                $bayaran->save();

                $this->updateHistory($request);
                $status='200';

            }
            else
            {
                $bayaran_data=PerundingRekodBayaranModel::select('yuran_perunding','inbuhan_balik')
                            ->where('pemantauan_id',$request->project_id)
                            ->where('perolehan',$request->perolehan)
                            ->where('no_bayaran',$request->no_bayaran)
                            ->get();

                if($bayaran_data[0]['yuran_perunding']!='.00' && $bayaran_data[0]['inbuhan_balik']!='.00')
                {
                        $bayaran = new PerundingRekodBayaranModel;   
                        $bayaran->no_bayaran =  $bayaran_no;    
                        $bayaran->pemantauan_id =  $request->project_id;
                        $bayaran->perolehan = $request->perolehan;    
                        $bayaran->save();

                        if($bayaran_no>1){
                            $this->CreateSubPerkara($request,$bayaran_no);
                            $this->CreatePerkara($request,$bayaran_no);
                        }
                        $this->updateHistory($request);
                        $status='200';
                }
                else
                {
                        $bayaran=[];
                        $status='500';
                }

            }
                                        
            

            return response()->json([
                'code' => $status,
                'status' => 'Success',
                'data' => $bayaran                
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

    public function rekordSelesai(Request $request)
    {
        try {
            Log::info($request);
            $perolehan = PemantauanPerolehan::whereId($request->perolehan_id)->first();
            $maklumat = PerundingMaklumat::where('pemantauan_id',$request->pemantauan_id)
                            ->where('perolehan_id',$request->perolehan_id)
                            ->first();
            Log::info($maklumat);
            if($request->selesai == 'true') {
                $perolehan->selesai_status = TRUE;
                $totalBayaran = PerundingRekodBayaranModel::sum('jumlah_bayaran');
                Log::info($totalBayaran);
                if($maklumat) {
                    $maklumat->nilai_bayaran_akhir_selesai = $totalBayaran;
                    $maklumat->penjimatan_selesai = $perolehan->kos_perolehan - $totalBayaran;
                    $maklumat->save();
                    Log::info('done');
                }
                
            }else {
                $perolehan->selesai_status = FALSE;
                if($maklumat) {
                    $maklumat->nilai_bayaran_akhir_selesai = 0;
                    $maklumat->penjimatan_selesai = 0;
                    $maklumat->save();
                }
            }

            
            $perolehan->save();

            return response()->json([
                'code' => 200,
                'status' => 'Success',
                'data' => 'done'                
            ]);

        } catch (\Throwable $th) {
            return response()->json([
                'code' => '500',
                'status' => 'Failed',
                'error' => $th,
            ]);
        }
    }
    public function updateBayaran(Request $request)
    {
        try {

            if($request->type=='borang')
            {
                $bayaran = PerundingRekodBayaranModel::where('pemantauan_id',$request->project_id)
                                                     ->where('no_bayaran',$request->no_bayaran)
                                                     ->where('perolehan',$request->perolehan)
                                                     ->first();
                $bayaran->jumlah_bayaran = $request->jumlah_bayaran;
                $bayaran->lad_value = $request->lad;
                $bayaran->update();

                $this->updateHistory($request);

            }
            else
            {

                if($request->otherdata)
                {
                    PerundingLejjar::where('pemantauan_id',$request->project_id)
                                        ->where('perolehan',$request->perolehan)
                                        ->delete();
                                              
                    foreach ($request->otherdata as $otherdata) {  
                        $sub_json = json_decode($otherdata,TRUE); //print_r($sub_json);

        
                        $bayaran = new PerundingLejjar;
                        $bayaran->pemantauan_id = $request->project_id;
                        $bayaran->perolehan = $request->perolehan;
                        $bayaran->no_bayaran = $request->no_bayaran;
                        $bayaran->yuran_perunding = $sub_json['yuran_perunding'];
                        $bayaran->inbuhan_balik = $sub_json['inbuhan_balik'];
                        $bayaran->jps_yuran_perunding = $sub_json['jps_yuran_perunding'];
                        $bayaran->jps_inbuhan_balik = $sub_json['jps_inbuhan_balik'];
                        $bayaran->dibuat_pada = Carbon::now()->format('Y-m-d H:i:s');
                        $bayaran->dibuat_oleh = $request->user_id;
                        $bayaran->dikemaskini_pada = Carbon::now()->format('Y-m-d H:i:s');
                        $bayaran->dikemaskini_oleh  = $request->user_id;
                        $bayaran->row_status = 1;
                        $bayaran->save();
                    }

                }

                foreach ($request->bayarandata as $bayarandata) {  
                    $sub_json = json_decode($bayarandata,TRUE); //print_r($sub_json);
    
                    $bayaran = PerundingRekodBayaranModel::where('id',$sub_json['id'])->first();
                        $bayaran->no_baucer = $sub_json['no_baucer'];
                        if($sub_json['tarikh'])
                        {
                            $bayaran->tarik_baucer =  $sub_json['tarikh'];
                        }
                        $bayaran->cukai_perkhidmatan =  $sub_json['cukai_perkhidmatan'];
                        $bayaran->update();
                }

                $this->updateHistory($request);

            }
           
            
            return response()->json([
                'code' => '200',
                'status' => 'Success'             
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

    protected function CreateSubPerkara($data,$bayaran_no)
    {

        $subperkera = PerundingKewanganSubSubPerkaraModel::where('pemantauan_id',$data->project_id)
                                                     ->where('no_bayaran','1')
                                                     ->where('perolehan',$data->perolehan)->get();

       foreach ($subperkera as $subperk) {  

                    $sub_sub_perkera =new PerundingKewanganSubSubPerkaraModel;
                    $sub_sub_perkera->perkara_id = $subperk['perkara_id'];
                    $sub_sub_perkera->sub_perkara_id = $subperk['sub_perkara_id'];
                    $sub_sub_perkera->no_bayaran = $bayaran_no;
                    $sub_sub_perkera->sub_sub_perkara = $subperk['sub_sub_perkara'];
                    $sub_sub_perkera->pemantauan_id = $data->project_id;
                    $sub_sub_perkera->perolehan = $data->perolehan;
                    $sub_sub_perkera->unit = $subperk['unit'];
                    $sub_sub_perkera->kelulusan_quantity = $subperk['kelulusan_quantity'];
                    $sub_sub_perkera->kelulusan_kadar = $subperk['kelulusan_kadar'];
                    $sub_sub_perkera->kelulusan_jumlah = $subperk['kelulusan_jumlah'];
                    $sub_sub_perkera->terdah_quantity = '0.00';
                    $sub_sub_perkera->terdah_jumlah = '0.00';
                    $sub_sub_perkera->semasa_quantity = '0.00';
                    $sub_sub_perkera->semasa_jumlah = '0.00';
                    $sub_sub_perkera->kumulatif_quantity = '0.00';
                    $sub_sub_perkera->kumulatif_jumlah = '0.00';
                    $sub_sub_perkera->baki = '0.00';
                    $sub_sub_perkera->save();
        }

    }

    protected function CreatePerkara($data,$bayaran_no)
    {

        $perkera =PerundingKewanganSubPerkara::where('pemantauan_id',$data->project_id)
                                    ->where('no_bayaran','1')
                                    ->where('perolehan',$data->perolehan)->get(); 
        foreach($perkera as $perka)
        {
                $newperkera = new PerundingKewanganSubPerkara;
                    $newperkera->perkara_id = $perka['perkara_id'];
                    $newperkera->sub_perkara = $perka['sub_perkara'];
                    $newperkera->pemantauan_id = $perka['pemantauan_id'];
                    $newperkera->unit = $perka['unit'];
                    $newperkera->no_bayaran = $bayaran_no;
                    $newperkera->perolehan = $perka['perolehan'];
                    $newperkera->kelulusan_quantity = $perka['kelulusan_quantity'];
                    $newperkera->kelulusan_kadar = $perka['kelulusan_kadar'];
                    $newperkera->kelulusan_jumlah = $perka['kelulusan_jumlah'];
                    $newperkera->terdah_quantity = '0.00';
                    $newperkera->terdah_jumlah = '0.00';
                    $newperkera->semasa_quantity = '0.00';
                    $newperkera->semasa_jumlah = '0.00';
                    $newperkera->kumulatif_quantity = '0.00';
                    $newperkera->kumulatif_jumlah = '0.00';
                    $newperkera->baki = '0.00';
                $newperkera->save();

                PerundingKewanganSubSubPerkaraModel::where('pemantauan_id',$data->project_id)
                                                                ->where('no_bayaran',$bayaran_no)
                                                                ->where('sub_perkara_id',$perka['id'])
                                                                ->where('perolehan',$data->perolehan)
                                                                ->update(['sub_perkara_id' => $newperkera['id']]);
        }
    }

    public function getYuranPerunding($id,$perolehan,$bayaran)
    {
        try {

            $data['perkara'] = PerundingKewanganPerkara::query()->with(
                                                        [
                                                            'subperkara' => function ($query) use ($bayaran) {
                                                                $query->where('no_bayaran', '=', $bayaran);
                                                            },
                                                            'subsubperkara' => function ($query) use ($bayaran) {
                                                                $query->where('no_bayaran', '=', $bayaran);
                                                            }
                                                        ])
                                                        ->where('pemantauan_id',$id)
                                                        ->where('perolehan',$perolehan)
                                                        ->get(); 
            $data['sub_perkara'] = [];

            $new_bayaran=$bayaran-1; 

            if($new_bayaran>0)
            {
                $data['sub_perkara'] = PerundingKewanganPerkara::query()->with(
                                                                            [
                                                                                'subperkara' => function ($query) use ($new_bayaran) {
                                                                                    $query->where('no_bayaran', '=', $new_bayaran);
                                                                                },
                                                                                'subsubperkara' => function ($query) use ($new_bayaran) {
                                                                                    $query->where('no_bayaran', '=', $new_bayaran);
                                                                                }
                                                                            ])
                                                                            ->where('pemantauan_id',$id)
                                                                            ->where('perolehan',$perolehan)
                                                                            ->get(); 
            }  

            $data['project'] = PerundingMaklumat::where('pemantauan_id',$id)
                                                ->where('perolehan_id',$perolehan)
                                                ->with(['eocp','sa','perlindugan','perolehanProject','pemantauanProject','pemantauanProject.negeri','pemantauanProject.bahagianPemilik'])
                                                ->first();    

            $data['perolehan'] = PemantauanPerolehan::where('pemantauan_id',$id)
                            ->where('id',$perolehan)
                            ->with(['pemantauanProject','pemantauanProject.negeri','pemantauanProject.bahagianPemilik'])
                            ->first();
            
            $data['bayaran'] = PerundingRekodBayaranModel::where('pemantauan_id',$id)->where('perolehan',$perolehan)->where('no_bayaran',$bayaran)->first();       
            
            $data['yuran_perunding'] = PerundingYuran::where('pemantauan_id',$id)
                                                        ->where('perolehan',$perolehan)
                                                        ->where('no_bayaran',$new_bayaran)->get();  
            
            $data['yuran'] = PerundingYuran::query()->with(
                                                            [
                                                                'suppliment'
                                                            ])
                                                            ->where('pemantauan_id',$id)
                                                            ->where('perolehan',$perolehan)
                                                            ->where('no_bayaran',$bayaran)->get(); 

            $data['yuran_supply'] = PerundingYuranSupplimetaries::where('pemantauan_id',$id)
                                                         ->where('perolehan',$perolehan)
                                                         ->where('no_bayaran',$bayaran)->get();       

            return response()->json([
                'code' => '200',
                'status' => 'Success',
                'data' => $data                
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

    public function getLejarBayaran($id,$perolehan)
    {
        try {

            $data['perkara'] = PerundingKewanganPerkara::query()->with(['subperkara','subsubperkara'])
                                                        ->where('pemantauan_id',$id)
                                                        ->where('perolehan',$perolehan)
                                                        ->get();            
            // $data['project'] = PerundingMaklumat::where('pemantauan_id',$id)
            //                                     ->where('perolehan_id',$perolehan)
            //                                     ->with(['eocp','sa','perlindugan','perolehanProject','pemantauanProject','pemantauanProject.negeri','pemantauanProject.bahagianPemilik'])
            //                                     ->first(); 

            $data['lejjar'] = PerundingLejjar::where('pemantauan_id',$id)
                                              ->where('perolehan',$perolehan)
                                              ->get(); 
                                                
            $data['perolehan'] = PemantauanPerolehan::where('pemantauan_id',$id)
                            ->where('id',$perolehan)
                            ->with(['pemantauanProject','pemantauanProject.negeri','pemantauanProject.bahagianPemilik'])
                            ->first();

            $data['bayaran'] = PerundingRekodBayaranModel::where('pemantauan_id',$id)->where('perolehan',$perolehan)->get();        


            return response()->json([
                'code' => '200',
                'status' => 'Success',
                'data' => $data                
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

    public function getBorangdata($id,$perolehan,$bayaran)
    {
        try {

            $data['perkara'] = PerundingKewanganPerkara::query()->with(
                                                        [
                                                            'subperkara' => function ($query) use ($bayaran) {
                                                                $query->where('no_bayaran', '=', $bayaran);
                                                            },
                                                            'subsubperkara' => function ($query) use ($bayaran) {
                                                                $query->where('no_bayaran', '=', $bayaran);
                                                            }
                                                        ])
                                                        ->where('pemantauan_id',$id)
                                                        ->where('perolehan',$perolehan)
                                                        ->get();            
            $data['project'] = PerundingMaklumat::where('pemantauan_id',$id)
                                                ->where('perolehan_id',$perolehan)
                                                ->with(['eocp','sa','perlindugan','perolehanProject','pemantauanProject','pemantauanProject.negeri','pemantauanProject.bahagianPemilik'])
                                                ->first();     
            $data['perolehan'] = PemantauanPerolehan::where('pemantauan_id',$id)
                            ->where('id',$perolehan)
                            ->with(['pemantauanProject','pemantauanProject.negeri','pemantauanProject.bahagianPemilik'])
                            ->first();

            $data['bayaran'] = PerundingRekodBayaranModel::where('pemantauan_id',$id)->where('perolehan',$perolehan)->where('no_bayaran','<=',$bayaran)->get();   
            $data['bayaran_data'] = PerundingRekodBayaranModel::select('penjanjian_asal')->where('pemantauan_id',$id)->where('perolehan',$perolehan)->where('no_bayaran',$bayaran)->first();   
            $data['yuran'] = YuranPerundingTotalModel::where('pemantauan_id',$id)
                                                        ->where('perolehan',$perolehan)
                                                        ->where('no_bayaran',$bayaran)->get();  
                 


            return response()->json([
                'code' => '200',
                'status' => 'Success',
                'data' => $data                
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

    public function updateYuran(Request $request)
    {
        try {

            // if($request->bayarandata){ 

                // YuranPerundingPreModel::where('pemantauan_id',$request->project_id)
                //                         ->where('perolehan',$request->perolehan)
                //                         ->delete();

                YuranPerundingTotalModel::where('pemantauan_id',$request->project_id)
                                        ->where('perolehan',$request->perolehan)
                                        ->where('no_bayaran',$request->no_bayaran)
                                        ->delete();

                PerundingYuranSupplimetaries::where('pemantauan_id',$request->project_id)
                                        ->where('perolehan',$request->perolehan)
                                        ->where('no_bayaran',$request->no_bayaran)
                                        ->delete();
                
                //foreach ($request->bayarandata as $bayarandata) {  
                    PerundingYuran::where('pemantauan_id',$request->project_id)
                                            ->where('perolehan',$request->perolehan)
                                            ->where('no_bayaran',$request->no_bayaran)
                                            ->delete();
                    
                    
                //}

            if($request->bayarandata){ 
                foreach ($request->bayarandata as $bayarandata) {  
                    $sub_json = json_decode($bayarandata,TRUE);
                    
                    $terda_data=new PerundingYuran;
                    $terda_data->pemantauan_id = $request->project_id;
                    $terda_data->no_bayaran = $request->no_bayaran;
                    $terda_data->perolehan = $request->perolehan;
                    $terda_data->perjanjian = $sub_json['perjanjian_asal'];
                    $terda_data->perjanjian_text = $sub_json['perjanjian_text'];
                    $terda_data->bayaran_terdhulu =  $sub_json['yuran_tardahulu'];
                    $terda_data->tututan_terkini = $sub_json['yuran_terkini'];
                    $terda_data->kumulatif = $sub_json['yuran_kumulatif'];
                    $terda_data->cukai_tamba = $sub_json['yuran_tambah'];
                    $terda_data->save();

                    if($sub_json['supply'])
                    {
                        foreach ($sub_json['supply'] as $supply) {   //print "supply"; print_r($supply);
                            $supply_data=new PerundingYuranSupplimetaries;
                            $supply_data->yuran_id = $terda_data['id'];
                            $supply_data->pemantauan_id = $request->project_id;
                            $supply_data->no_bayaran = $request->no_bayaran;
                            $supply_data->perolehan = $request->perolehan;
                            $supply_data->supply_value = $supply;
                            $supply_data->save();
                        }

                    }
                    
                }
                if($request->total_suply_data)
                {
                    foreach ($request->total_suply_data as $total_suply_data) {  

                        $supplytotdata=new YuranPerundingTotalModel;
                        $supplytotdata->pemantauan_id = $request->project_id;
                        $supplytotdata->no_bayaran = $request->no_bayaran;
                        $supplytotdata->perolehan = $request->perolehan;
                        $supplytotdata->supplier_data = $total_suply_data;
                        $supplytotdata->save();
                    }     
                }           

                $bayaran = PerundingRekodBayaranModel::where('pemantauan_id',$request->project_id)
                                                     ->where('no_bayaran',$request->no_bayaran)
                                                     ->where('perolehan',$request->perolehan)
                                                     ->first();
                $bayaran->yuran_perunding = $request->yuran_perunding;
                $bayaran->penjanjian_asal = $request->suply_perjanjian;
                $bayaran->update();
            }

            $this->updateHistory($request);

            return response()->json([
                'code' => '200',
                'status' => 'Success'             
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

    protected function updateHistory($data)
    {
        try{
                $userdata=User::with(['bahagian'])->where('id',$data->user_id)->first(); //print_r($userdata);exit;
                $history_data=new PerundingKewanganHistoryModel;
                $history_data->tindakan         = $data->action;
                $history_data->no_bayaran       = $data->no_bayaran;
                $history_data->perolehan        = $data->perolehan;
                $history_data->pemantauan_id    = $data->project_id;
                $history_data->tarikh           = Carbon::now()->format('Y-m-d');
                $history_data->bahagian_kod     = $userdata['bahagian']['kod_bahagian'];
                $history_data->bahagian_id      = $userdata['bahagian_id'];
                $history_data->nama             = $userdata['name'];
                $history_data->dibuat_pada      = Carbon::now()->format('Y-m-d H:i:s');
                $history_data->dibuat_oleh      = $data->user_id;
                $history_data->dikemaskini_oleh = $data->user_id;
                $history_data->dikemaskini_pada = Carbon::now()->format('Y-m-d H:i:s');
                $history_data->row_status       = 1;
                $history_data->save();

                return response()->json([
                    'code' => '200',
                    'status' => 'Success'             
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

    public function getHistory($id,$perolehan,$bayaran){

        try{
            $data= PerundingKewanganHistoryModel::with('user')->where('pemantauan_id',$id)
                                                        ->where('perolehan',$perolehan)->get();

            return response()->json([
                'code' => '200',
                'status' => 'Success',
                'data' => $data             
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

}