<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Jenssegers\Agent\Facades\Agent;
use Illuminate\Http\Request;
use \App\Models\noc_projectModel;
use Illuminate\Support\Facades\DB;
use \App\Models\NOCKementerianEconomi;
use \App\Models\NOCKementerian;
use \App\Models\NOCPeruntukan;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Illuminate\Support\Facades\Validator;
use \App\Models\projectLog;
use \App\Models\noc_negeri;
use Illuminate\Support\Facades\Log;
use App\Models\VM\VmSkop;
use App\Models\VM\VmObjektif;
use App\Models\VM\VmOutput;
use App\Models\VM\VmOutcome;
use \App\Models\PemantauanProject;
use \App\Models\PemantauanKewanganSkop;
use \App\Models\PemantauanSkopProjects;
use \App\Models\nocCheckedStatus;
use \App\Models\noc_OutcomeModel;
use \App\Models\noc_OutputModel;
use \App\Models\NocKpiModule;
use \App\Models\nocSelectedProjeck;
use App\Models\CmsPengumuman;
use \App\Models\SkopOption;
use \App\Models\NocSkop;
use \App\Models\NamaAgensi;
use \App\Models\nocKementerianSilling;
use \App\Models\nocKementerianEconomiSilling;


class NOC_Controller extends Controller
{
    public function list($id){
        try {
            
            $user = \App\Models\User::whereId($id)->with('bahagian')->first(); 
            
            //Log::info($user);

            $query = DB::table('pemantauan_project')
                            ->where('noc_status','!=','')
                            ->select('id','no_rujukan','kod_projeck','nama_projek');
                            if($user->bahagian->acym == 'BKOR' || $user->is_superadmin==1)
                            {
                            }
                            else
                            {
                                $query->where('pemantauan_project.bahagian_pemilik','=',$user->bahagian_id);
                            }
                
                            $data = $query->get();

            $data2 = DB::table('pemantauan_project')->where('pemantauan_project.bahagian_pemilik','=',$user->bahagian_id)->get();

            $data1=noc_projectModel::select('pp_id')->get();
            // $data2 = DB::table('pemantauan_project')->get();
            $skop_project = SkopOption::with('subskop')->get();
            $agensi = NamaAgensi::where('row_status',1)->get();
            $unit =  \App\Models\OutputUnit::where('IsActive','=',1)->get();



            //$data = \App\Models\NocModule::where('noc_status','!=','')->get(['id','no_rujukan','kod_projeck','nama_projek']);
            //$data = refNegeri::with('updatedBy')->get();
            return response()->json([
                'code' => '200',
                'status' => 'Success',
                'data' => $data,
                'noc_data' => $data1,
                'all_lists' => $data2,
                'skop' => $skop_project,
                'agensi' => $agensi,
                'unit_data' => $unit
            ]);

        } catch (\Throwable $th) {
            logger()->error($th->getMessage());


            return response()->json([
                'code' => '500',
                'status' => 'Failed',
                'error' => $th,
            ]);
        }
        
    }

    public function negeriDetails_pementuan($id)
    {
        try {
            $negeri_lokas = \App\Models\Pementuan_negeri_lokasi::with(['negeri','daerah','parlimen','dun'])->where('pp_id', $id)       
            ->where('row_status', 1)
            ->orderBy('negeri_id')
            ->get();

            $data['negeri'] = $negeri_lokas;
            // $data['documents'] = \App\Models\ProjectNegeriDokumen::select('id','projek_negeri_dokumen_name','keterangan')
            // ->where('permohonan_Projek_id', $id)        
            // ->where('row_status', 1)
            // ->orderBy('id','DESC')->get();

            // $data['negeriselection'] = \App\Models\Project::select('negeri_selection_type','koordinat_latitude','koordinat_longitude')
            // ->where('id', $id)        
            // ->where('row_status', 1)
            // ->get();
            
           // print_r($data);exit;

           
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

            //CallApi($body);

            //------------- end of store and email -----------------------

            return response()->json([
                'code' => '500',
                'status' => 'Failed',
                'error' => $th->getMessage(),
            ]);
        }
    }

    public function negeriDetails_noc($id)
    {
        try {
            $negeri_lokas_pem = \App\Models\Pementuan_negeri_lokasi::with(['negeri','daerah','parlimen','dun'])->where('pp_id', $id)       
            ->where('row_status', 1)
            ->orderBy('negeri_id')
            ->get();

            $data['negeri_pem'] = $negeri_lokas_pem;
            
            $negeri_lokas = \App\Models\noc_negeri::with(['negeri','daerah','parlimen','dun'])->where('pp_id', $id)       
            ->where('row_status', 1)
            ->orderBy('negeri_id')
            ->get();

            $data['negeri'] = $negeri_lokas;
            // $data['documents'] = \App\Models\ProjectNegeriDokumen::select('id','projek_negeri_dokumen_name','keterangan')
            // ->where('permohonan_Projek_id', $id)        
            // ->where('row_status', 1)
            // ->orderBy('id','DESC')->get();

            // $data['negeriselection'] = \App\Models\Project::select('negeri_selection_type','koordinat_latitude','koordinat_longitude')
            // ->where('id', $id)        
            // ->where('row_status', 1)
            // ->get();
            
           // print_r($data);exit;

           
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

            //CallApi($body);

            //------------- end of store and email -----------------------

            return response()->json([
                'code' => '500',
                'status' => 'Failed',
                'error' => $th->getMessage(),
            ]);
        }
    }

    public function updateNegeriNOC(Request $request)
    {
        // dd($request->toArray());

        try {
            $data=$request->toArray();

            
            // $validator = Validator::make($request->all(),[
            //     // 'mukim_id' => ['required', 'integer'],
            //     'dun_id' => ['required', 'integer'],
            //     'parlimen_id' => ['required', 'integer'],
            //     'negeri_id' => ['required', 'integer'],
            //     'daerah_id' => ['required', 'integer']
            // ]);
            if(true) {  
                
                $negeri_data = \App\Models\noc_negeri::where('pp_id', $data['ppid'])->first();     
                    if($negeri_data)
                    {
                        $negeri_lokas = $this->updateNegeriDataNOC($request->all(), $data['ppid']);
                    }
                        //$negeri_lokas = $this->createNegeriData($request->all());

                        
                    
                    //$project_id= $request->id;


                        // \App\Models\Project::where('id', $project_id)->update([ 
                        //    'koordinat_latitude' => $request->koordinat_latitude,
                        //     'koordinat_longitude' => $request->koordinat_longitude,
                        //     'negeri_name'=> $request->NegeriName,
                        // ]);
                                                
                    if($request->negeritext){

                        
                        $AlreadyExist = \App\Models\noc_projectModel::where('pp_id', $data['ppid'])->where('row_status', 1)->first();

                        
                        
                        if($AlreadyExist){

                            $noc_id = \App\Models\noc_projectModel::where('pp_id', $data['ppid'])->where('row_status', 1)->first()["id"];

                            
                            foreach ($request->negeritext as $negeritextitem) {  
                                $datanegeri = json_decode($negeritextitem, TRUE);               
                                $negerilokascomponen = \App\Models\noc_negeri::create([  
                                    'negeri_id' => $datanegeri['negeri_id'],
                                    'noc_id'=> $noc_id,
                                    'daerah_id' => $datanegeri['daerah_id'],            
                                    'mukim_id' => $datanegeri['mukim_id'],            
                                    'parlimen_id' => $datanegeri['parlimen_id'],
                                    'dun_id' => $datanegeri['dun_id'],
                                    'pp_id'=> $data['ppid'],
                                    'dibuat_oleh' => $request->userid,
                                    'dikemaskini_oleh' => $request->userid,
                                    'dibuat_pada' => Carbon::now()->format('Y-m-d H:i:s'),
                                    'dikemaskini_pada' => Carbon::now()->format('Y-m-d H:i:s'),
                                    'row_status'=> 1,
                                    'status_id' => 40                                
                                ]);
                            }  
                        }
                        else{

                            $data2 = \App\Models\noc_projectModel::create([                       
                                'pp_id' => $data['ppid'],
                                'skop' => '',
                                'keterangan' => '',
                                'komponen' => '',
                                'nama_projek'=>'',
                                'kod_projek'=>null,
                                'objektif'=>'',
                                'kos_projek'=>'',
                                'row_status' => 1,                  
                                'created_at' =>  Carbon::now()->format('Y-m-d H:i:s'),            
                                'dibuat_pada' => Carbon::now()->format('Y-m-d H:i:s'),
                                'dibuat_oleh' => $request->userid,
                                'dikemaskini_oleh' => $request->userid,
                                'dikemaskini_pada' => Carbon::now()->format('Y-m-d H:i:s'),
                            ]);

                            foreach ($request->negeritext as $negeritextitem) {  
                                $datanegeri = json_decode($negeritextitem, TRUE);               
                                $negerilokascomponen = \App\Models\noc_negeri::create([  
                                    'noc_id'=>\App\Models\noc_projectModel::latest()->first()["id"],
                                    //'noc_id'=>250,
                                    'negeri_id' => $datanegeri['negeri_id'],
                                    'daerah_id' => $datanegeri['daerah_id'],            
                                    'mukim_id' => $datanegeri['mukim_id'],            
                                    'parlimen_id' => $datanegeri['parlimen_id'],
                                    'dun_id' => $datanegeri['dun_id'],
                                    'pp_id'=>$data['ppid'],
                                    'dibuat_oleh' => $request->userid,
                                    'dikemaskini_oleh' => $request->userid,
                                    'dibuat_pada' => Carbon::now()->format('Y-m-d H:i:s'),
                                    'dikemaskini_pada' => Carbon::now()->format('Y-m-d H:i:s'),
                                    'row_status'=> 1,
                                    'status_id' => 40                               
                                ]);
                            }  
                        }
        
                        return response()->json([
                            'code' => '200',
                            'status' => 'Success',
                            'data' => $negerilokascomponen,
                            
                            
                        ]);
                    
                        
                        
                        try{
                            //$negeri_selection = $this->updateNegeriSelection($request->all(),$request->id);
                            
                        }catch (\Throwable $th) {
                            logger()->error($th->getMessage());
                
                            return response()->json([
                                'code' => '500',
                                'status' => 'Failed in inner',
                                'error' => $th->getMessage(),
                            ]);
                        }
                        
                        
                    }    
                     
                    // $section_name='NOC Negeri lokasi';
                    // $user_data = DB::table('users')
                    //            ->join('ref_jawatan','ref_jawatan.id', '=','users.jawatan_id')
                    //            ->select('users.*','ref_jawatan.nama_jawatan')->where('users.id',$request->userid)->first();
                    // $no_rojukan_data = DB::table('projects')->select('no_rujukan')->where('id',$request->id)->first();
                    // $logData=[
                    //             'user_id' =>$request->userid, 
                    //             'section_name'=>$section_name,   
                    //             'projek_id'=>$request->id,
                    //             'modul' => 'Permohonan Projek',
                    //             'user_ic_no' => $user_data->no_ic,
                    //             'user_jawatan' => $user_data->nama_jawatan,
                    //             'user_name' => $user_data->name,
                    //             'no_rujukan' => $no_rojukan_data-> no_rujukan,
                    //       ];
                
                    // DB::connection(env('DB_CONNECTION_AUDIT'))->table('projek_log')->insert($logData);

                    return response()->json([
                        'code' => '200',
                        'status' => 'Success',
                        'data' => $negeri_lokas,
                    ]);
            } else {                
                return response()->json([
                    'code' => '422',
                    'status' => 'Unprocessable Entity',
                    'data' => $validator->errors(),
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

            //CallApi($body);

            //------------- end of store and email -----------------------

            return response()->json([
                'code' => '500',
                'status' => 'Failed in outer',
                'error' => $th->getMessage(),
            ]);
        }
    }

    protected function updateNegeriDataNOC(array $data, $id)
    {        
        // print_r($data);exit;

        return noc_negeri::where('pp_id', $id)->update([ 
            'dikemaskini_pada' => Carbon::now()->format('Y-m-d H:i:s'),
            'row_status' => 0
        ]);
    }

    public function store(Request $request){
        try {
                // dd($request);
            $kod_projek=NULL;
            if($request->valueThree)
            {
                $kod_projek=$request->valueOne.$request->valueTwo.$request->valueThree.$request->valueFour;
            }
            $AlreadyExist = noc_projectModel::where('pp_id', $request->project_id)->first();

            if($request->file('lampiran_file_name')) {
                $lampiran_file_name=$request->file('lampiran_file_name')->getClientOriginalName();
            }else {
                $lampiran_file_name=NULL;
            }

            if($request->file('memo_file_name')) {
                $memo_file_name=$request->file('memo_file_name')->getClientOriginalName();
            }else {
                $memo_file_name=NULL;
            }
            
            if($AlreadyExist){
                $data = noc_projectModel::where('pp_id', $request->project_id)->update([                    
                                                    'skop' => $request->SkopData,
                                                    'keterangan' => $request->KeteranganData,
                                                    'komponen' => $request->KomponenData,
                                                    'nama_projek'=>$request->nameBaharu,
                                                    'kod_projek'=>$kod_projek,
                                                    'kos_projek'=>$request->kosValue,
                                                    'objektif'=>$request->objektifVal,
                                                    'row_status' => 1,   
                                                    'created_at' =>  Carbon::now()->format('Y-m-d H:i:s'),            
                                                    'dibuat_pada' => Carbon::now()->format('Y-m-d H:i:s'),
                                                    'dibuat_oleh' => $request->user_id,
                                                    'dikemaskini_oleh' => $request->user_id,
                                                    'dikemaskini_pada' => Carbon::now()->format('Y-m-d H:i:s'),
                                                    'lampiran_file_name' => $lampiran_file_name,
                                                    'memo_file_name' => $memo_file_name,
                                                ]);

                $noc_id=$AlreadyExist['id'];
            }
            else{
                $data = noc_projectModel::create([
                                                    // 'noc_id'=>$request->noc_id,                    
                                                    'pp_id' => $request->project_id,
                                                    'skop' => $request->SkopData,
                                                    'keterangan' => $request->KeteranganData,
                                                    'komponen' => $request->KomponenData,
                                                    'nama_projek'=>$request->nameBaharu,
                                                    'kod_projek'=>$kod_projek,
                                                    'objektif'=>$request->objektifVal,
                                                    'kos_projek'=>$request->kosValue,
                                                    'row_status' => 1, 
                                                    'status_id' => 40,                 
                                                    'created_at' =>  Carbon::now()->format('Y-m-d H:i:s'),            
                                                    'dibuat_pada' => Carbon::now()->format('Y-m-d H:i:s'),
                                                    'dibuat_oleh' => $request->user_id,
                                                    'dikemaskini_oleh' => $request->user_id,
                                                    'dikemaskini_pada' => Carbon::now()->format('Y-m-d H:i:s'),
                                                    'lampiran_file_name' => $lampiran_file_name,
                                                    'memo_file_name' => $memo_file_name,
                                                ]);

                $noc_id=$data['id'];
            }

            Log::info($noc_id);

            $result_data = noc_projectModel::with('media')->where('id',$noc_id)->first();

            if($request->file('lampiran_file_name')) {
                $result_data->clearMediaCollection('lampiran_file_name');
                $result_data
                ->addMedia($request->file('lampiran_file_name'))
                ->toMediaCollection('lampiran_file_name');
            }

            if($request->file('memo_file_name')) {
                $result_data->clearMediaCollection('memo_file_name');
                $result_data
                ->addMedia($request->file('memo_file_name'))
                ->toMediaCollection('memo_file_name');
            }

            

              nocCheckedStatus::where('noc_id',$noc_id)->delete();
              $noc_data=new nocCheckedStatus();
              $noc_data->noc_id = $noc_id;
              $noc_data->pp_id  = $request->project_id;
              $noc_data->skop_status = $request->inlineCheckbox1;
              $noc_data->kos_status = $request->inlineCheckbox4;
              $noc_data->butiran_status = $request->inlineCheckbox7;
              $noc_data->semula_status  = $request->inlineCheckbox10;
              $noc_data->nama_status    = $request->inlineCheckbox2;
              $noc_data->lokasi_status  = $request->inlineCheckbox5;
              $noc_data->kpi_status     = $request->inlineCheckbox8;
              $noc_data->outcome_status = $request->inlineCheckbox11;
              $noc_data->kod_status     = $request->inlineCheckbox3;
              $noc_data->objectif_status = $request->inlineCheckbox6;
              $noc_data->output_status   = $request->inlineCheckbox9;
              $noc_data->dibuat_oleh     = $request->user_id;
              $noc_data->dibuat_pada     =  Carbon::now()->format('Y-m-d H:i:s');
              $noc_data->dikemaskini_oleh = $request->user_id;
              $noc_data->dikemaskini_pada =  Carbon::now()->format('Y-m-d H:i:s');
              $noc_data->save();

              NocSkop::where('noc_id',$noc_id)->delete();
              
              if($request->skop_list_data){ 
                foreach ($request->skop_list_data as $skop_list_data) {  
                    $this->storeSkopdata($skop_list_data,$noc_id);
                }
              }

              if($request->sub_skop_list_data){ 
                foreach ($request->sub_skop_list_data as $sub_skop_list_data) {  
                    $this->storeSkopdata($sub_skop_list_data,$noc_id);
                }
              }



            return response()->json([
                'code' => '200',
                'status' => 'Success',
                'data' => $data,
            ]);

        } catch (\Throwable $th) {
            logger()->error($th->getMessage());


            return response()->json([
                'code' => '500',
                'status' => 'Failed',
                'error' => $th->getMessage(),
            ]);
        }

    }

    protected function storeSkopdata($data,$noc_id)
    {

        //Log::info($data);
        try{
                $sub_json = json_decode($data,TRUE);

                $noc_data=new NocSkop;
                $noc_data->noc_id           = $noc_id;
                $noc_data->skop_id          = $sub_json['id'];
                $noc_data->sub_skop_id      = $sub_json['sub_id'];
                $noc_data->skop_kos         = $sub_json['kos'];
                $noc_data->dibuat_pada      = Carbon::now()->format('Y-m-d H:i:s');
                $noc_data->dibuat_oleh      = $sub_json['user_id'];
                $noc_data->dikemaskini_oleh = $sub_json['user_id'];
                $noc_data->dikemaskini_pada = Carbon::now()->format('Y-m-d H:i:s');
                $noc_data->row_status       = 1;
                $noc_data->save();

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

    public function StoreNocKpi(Request $request){
        try {

            $AlreadyExist = \App\Models\noc_projectModel::where('pp_id',$request->pp_id)->first();
            if($AlreadyExist){
                // dd($AlreadyExist);
                $AlreadyExistNoc = \App\Models\NocKpiModule::where('noc_id',$AlreadyExist->id)->first();
                if($AlreadyExistNoc){
                // dd($AlreadyExist->id);
                    $data = \App\Models\NocKpiModule::where('noc_id',$AlreadyExist->id)->update([                    
                        'pp_id' => $request->pp_id,
                        'noc_id'=>$AlreadyExist->id,
                        'no_rujukan'=>$request->noRujukan,
                        'project_id'=>$request->pp_id,
                        'kuantiti'=>$request->kbval,
                        'unit'=>$request->unit,
                        'penerangan'=>$request->PeneranganText,
                        'yr_1'=>$request->yearVal0,
                        'yr_2'=>$request->yearVal1,
                        'yr_3'=>$request->yearVal2,
                        'yr_4'=>$request->yearVal3,
                        'yr_5'=>$request->yearVal4,
                        'yr_6'=>$request->yearVal5,
                        'yr_7'=>$request->yearVal6,
                        'yr_8'=>$request->yearVal7,
                        'yr_9'=>$request->yearVal8,
                        'yr_10'=>$request->yearVal9,
                        'row_status' => 1,                  
                        'created_at' =>  Carbon::now()->format('Y-m-d H:i:s'),            
                        'dibuat_pada' => Carbon::now()->format('Y-m-d H:i:s'),
                        'dibuat_oleh' => $request->user_id,
                        'dikemaskini_oleh' => $request->user_id,
                        'dikemaskini_pada' => Carbon::now()->format('Y-m-d H:i:s'),
                    ]);
                }else{
                    $data = \App\Models\NocKpiModule::create([                    
                        'pp_id' => $request->pp_id,
                        'noc_id'=>$AlreadyExist->id,
                        'no_rujukan'=>$request->noRujukan,
                        'project_id'=>$request->pp_id,
                        'kuantiti'=>$request->kbval,
                        'unit'=>$request->unit,
                        'penerangan'=>$request->PeneranganText,
                        'yr_1'=>$request->yearVal0,
                        'yr_2'=>$request->yearVal1,
                        'yr_3'=>$request->yearVal2,
                        'yr_4'=>$request->yearVal3,
                        'yr_5'=>$request->yearVal4,
                        'yr_6'=>$request->yearVal5,
                        'yr_7'=>$request->yearVal6,
                        'yr_8'=>$request->yearVal7,
                        'yr_9'=>$request->yearVal8,
                        'yr_10'=>$request->yearVal9,
                        'row_status' => 1,                  
                        'created_at' =>  Carbon::now()->format('Y-m-d H:i:s'),            
                        'dibuat_pada' => Carbon::now()->format('Y-m-d H:i:s'),
                        'dibuat_oleh' => $request->user_id,
                        'dikemaskini_oleh' => $request->user_id,
                        'dikemaskini_pada' => Carbon::now()->format('Y-m-d H:i:s'),
                    ]);

                }
                return response()->json([
                    'code' => '200',
                    'status' => 'Success',
                    'data' => $data,
                ]);
                
                }
                else{
                    $data2 = \App\Models\noc_projectModel::create([                       
                        'pp_id' => $request->pp_id,
                        'skop' => '',
                        'keterangan' => '',
                        'komponen' => '',
                        'nama_projek'=>'',
                        'kod_projek'=>null,
                        'objektif'=>'',
                        'kos_projek'=>'',
                        'row_status' => 1,                  
                        'created_at' =>  Carbon::now()->format('Y-m-d H:i:s'),            
                        'dibuat_pada' => Carbon::now()->format('Y-m-d H:i:s'),
                        'dibuat_oleh' => $request->user_id,
                        'dikemaskini_oleh' => $request->user_id,
                        'dikemaskini_pada' => Carbon::now()->format('Y-m-d H:i:s'),
                    ]);
                    $data = \App\Models\NocKpiModule::create([                    
                        'pp_id' => $request->pp_id,
                        'noc_id'=>\App\Models\noc_projectModel::latest()->first()["id"],
                        'no_rujukan'=>$request->noRujukan,
                        'project_id'=>$request->pp_id,
                        'kuantiti'=>$request->kbval,
                        'unit'=>$request->unit,
                        'penerangan'=>$request->PeneranganText,
                        'yr_1'=>$request->yearVal0,
                        'yr_2'=>$request->yearVal1,
                        'yr_3'=>$request->yearVal2,
                        'yr_4'=>$request->yearVal3,
                        'yr_5'=>$request->yearVal4,
                        'yr_6'=>$request->yearVal5,
                        'yr_7'=>$request->yearVal6,
                        'yr_8'=>$request->yearVal7,
                        'yr_9'=>$request->yearVal8,
                        'yr_10'=>$request->yearVal9,
                        'row_status' => 1,                  
                        'created_at' =>  Carbon::now()->format('Y-m-d H:i:s'),            
                        'dibuat_pada' => Carbon::now()->format('Y-m-d H:i:s'),
                        'dibuat_oleh' => $request->user_id,
                        'dikemaskini_oleh' => $request->user_id,
                        'dikemaskini_pada' => Carbon::now()->format('Y-m-d H:i:s'),
                    ]);
                    
                    

                    return response()->json([
                        'code' => '200',
                        'status' => 'Success',
                        'data' => $data,
                        'data2' => $data2,

                    ]);
            }


        } catch (\Throwable $th) {
            logger()->error($th->getMessage());


            return response()->json([
                'code' => '500',
                'status' => 'Failed',
                'error' => $th->getMessage(),
            ]);
        }

    }

    public function StoreNocOutput(Request $request){
        try {
                    $noc_data = noc_projectModel::where('pp_id', $request->project_id)->first();
                    $noc_id=$noc_data['id'];


                    $output=noc_OutputModel::where('noc_id',$noc_id)->where('pp_id',$request->project_id)->delete();
                    foreach ($request->output as $outputdetails) {  
                        $data = json_decode($outputdetails, TRUE);
                        $data_noc = noc_OutputModel::create([   
                                                                'pp_id' => $request->project_id,
                                                                'noc_id'=> $noc_id,
                                                                'Permohonan_Projek_id' => $request->project_id,
                                                                'unit_id' => $data['unit_id'],
                                                                'output_proj' => $data['output_proj'],
                                                                'Kuantiti' => $data['Kuantiti'],
                                                                'dibuat_oleh' => $request->user_id,
                                                                'no_rujukan'=>$request->noRujukan,
                                                                'row_status' => 1,                  
                                                                'created_at' =>  Carbon::now()->format('Y-m-d H:i:s'),            
                                                                'dibuat_pada' => Carbon::now()->format('Y-m-d H:i:s'),
                                                                'dibuat_oleh' => $request->user_id,
                                                                'dikemaskini_oleh' => $request->user_id,
                                                                'dikemaskini_pada' => Carbon::now()->format('Y-m-d H:i:s'),
                                                            ]);
                    }
                
                return response()->json([
                    'code' => '200',
                    'status' => 'Success',
                    'data' => $data_noc,
                ]);


            // dd($data);
            
            // $AlreadyExist = \App\Models\noc_projectModel::where('pp_id',$request->project_id)->first();
            // if($AlreadyExist){
            //     // dd($AlreadyExist);
            //     $AlreadyExistNoc = \App\Models\noc_OutputModel::where('noc_id',$AlreadyExist->id)->get();
            //     // dd($AlreadyExistNoc->toArray());
            //     // dd(count($AlreadyExistNoc->toArray()));
            //     $updateId='';
            //     for($i=0;$i<count($AlreadyExistNoc->toArray());$i++){
            //         $updateId=$AlreadyExistNoc->toArray()[$i]["id"];
            //     }
            //     if($AlreadyExistNoc){
            //         foreach ($request->output as $outputdetails) {  
            //             $data = json_decode($outputdetails, TRUE);
            //             $data = \App\Models\noc_OutputModel::where('id',$updateId)->update([ 
            //                 'pp_id'=>$request->project_id,                   
            //                 'Permohonan_Projek_id' => $request->project_id,
            //                 'unit_id' => $data['unit_id'],
            //                 'output_proj' => $data['output_proj'],
            //                 'Kuantiti' => $data['Kuantiti'],
            //                 'dibuat_oleh' => $request->user_id,
            //                 'no_rujukan'=>$request->noRujukan,
            //                 'row_status' => 1,                  
            //                 'created_at' =>  Carbon::now()->format('Y-m-d H:i:s'),            
            //                 'dibuat_pada' => Carbon::now()->format('Y-m-d H:i:s'),
            //                 'dibuat_oleh' => $request->user_id,
            //                 'dikemaskini_oleh' => $request->user_id,
            //                 'dikemaskini_pada' => Carbon::now()->format('Y-m-d H:i:s'),
            //             ]);
            //         }
            //         return response()->json([
            //             'code' => '200',
            //             'status' => 'Success',
            //             'data' => $data,
            //         ]);
            //     }
            //     else{
                    // foreach ($request->output as $outputdetails) {  
                    //     $data = json_decode($outputdetails, TRUE); 
                    //     // dd(\App\Models\noc_projectModel::latest()->first()["id"]); 
                    //     $data = \App\Models\noc_OutputModel::create([   
                    //         'pp_id' => $request->project_id,
                    //         'noc_id'=>\App\Models\noc_projectModel::latest()->first()["id"],
                    //         'Permohonan_Projek_id' => $request->project_id,
                    //         'unit_id' => $data['unit_id'],
                    //         'output_proj' => $data['output_proj'],
                    //         'Kuantiti' => $data['Kuantiti'],
                    //         'dibuat_oleh' => $request->user_id,
                    //         'no_rujukan'=>$request->noRujukan,
                    //         'row_status' => 1,                  
                    //         'created_at' =>  Carbon::now()->format('Y-m-d H:i:s'),            
                    //         'dibuat_pada' => Carbon::now()->format('Y-m-d H:i:s'),
                    //         'dibuat_oleh' => $request->user_id,
                    //         'dikemaskini_oleh' => $request->user_id,
                    //         'dikemaskini_pada' => Carbon::now()->format('Y-m-d H:i:s'),
                    //     ]);
                    // }
                    

                    // return response()->json([
                    //     'code' => '200',
                    //     'status' => 'Success',
                    //     'data' => $data,
                    // ]);
            //     }
            // }
            // else{

            //     $data2 = \App\Models\noc_projectModel::create([
            //         // 'noc_id'=>$request->noc_id,                    
            //         'pp_id' => $request->project_id,
            //         'skop' => null,
            //         'keterangan' => null,
            //         'komponen' => null,
            //         'nama_projek'=>null,
            //         'kod_projek'=>null,
            //         'objektif'=>null,
            //         'kos_projek'=>null,
            //         'row_status' => 1,                  
            //         'created_at' =>  Carbon::now()->format('Y-m-d H:i:s'),            
            //         'dibuat_pada' => Carbon::now()->format('Y-m-d H:i:s'),
            //         'dibuat_oleh' => $request->user_id,
            //         'dikemaskini_oleh' => $request->user_id,
            //         'dikemaskini_pada' => Carbon::now()->format('Y-m-d H:i:s'),
            //     ]);
            //     // dd($data2->id);

            //     foreach ($request->output as $outputdetails) {  
            //         $data = json_decode($outputdetails, TRUE); 
            //         // dd($request->project_id); 
            //         // dd(\App\Models\noc_projectModel::latest()->first()["id"]);
            //         $data = \App\Models\noc_OutputModel::create([   
            //             'pp_id' => $request->project_id,
            //             'noc_id'=>\App\Models\noc_projectModel::latest()->first()["id"],
            //             'Permohonan_Projek_id' => $request->project_id,
            //             'unit_id' => $data['unit_id'],
            //             'output_proj' => $data['output_proj'],
            //             'Kuantiti' => $data['Kuantiti'],
            //             'dibuat_oleh' => $request->user_id,
            //             'no_rujukan'=>$request->noRujukan,
            //             'row_status' => 1,                  
            //             'created_at' =>  Carbon::now()->format('Y-m-d H:i:s'),            
            //             'dibuat_pada' => Carbon::now()->format('Y-m-d H:i:s'),
            //             'dibuat_oleh' => $request->user_id,
            //             'dikemaskini_oleh' => $request->user_id,
            //             'dikemaskini_pada' => Carbon::now()->format('Y-m-d H:i:s'),
            //         ]);
            //     }
                
            //     return response()->json([
            //         'code' => '200',
            //         'status' => 'Success',
            //         'data' => $data,
            //         'data2' => $data2,

            //     ]);
            // }

        } catch (\Throwable $th) {
            logger()->error($th->getMessage());


            return response()->json([
                'code' => '500',
                'status' => 'Failed',
                'error' => $th->getMessage(),
            ]);
        }
        
    }


    public function StoreNocOutcome(Request $request){
        try {
            $noc_data = noc_projectModel::where('pp_id', $request->project_id)->first();
            
            $noc_id=$noc_data['id'];
            
            $outcome= noc_OutcomeModel::where('noc_id',$noc_id)->where('pp_id',$request->project_id)->delete();
            foreach ($request->outcome as $outputdetails) {  
                $data = json_decode($outputdetails, TRUE); 
                $data_outcome = noc_OutcomeModel::create([   
                                                            'pp_id' => $request->project_id,
                                                            'noc_id'=> $noc_id,
                                                            'Permohonan_Projek_id' => $request->project_id,
                                                            'unit_id' => $data['unit_id'],
                                                            'Projek_Outcome' => $data['outcome_proj'],
                                                            'Kuantiti' => $data['Kuantiti'],
                                                            'dibuat_oleh' => $request->user_id,
                                                            'no_rujukan'=>$request->noRujukan,
                                                            'row_status' => 1,                  
                                                            'created_at' =>  Carbon::now()->format('Y-m-d H:i:s'),            
                                                            'dibuat_pada' => Carbon::now()->format('Y-m-d H:i:s'),
                                                            'dibuat_oleh' => $request->user_id,
                                                            'dikemaskini_oleh' => $request->user_id,
                                                            'dikemaskini_pada' => Carbon::now()->format('Y-m-d H:i:s'),
                                                        ]);
            }
        
            return response()->json([
                'code' => '200',
                'status' => 'Success',
                'data' => $data_outcome,
            ]);

            //$AlreadyExist = \App\Models\noc_projectModel::where('pp_id',$request->project_id)->first();

            // if($AlreadyExist){
            //     // dd($AlreadyExist);
            //     $AlreadyExistNoc = \App\Models\noc_OutcomeModel::where('noc_id',$AlreadyExist->id)->first();
            //     if($AlreadyExistNoc){
            //         // dd($AlreadyExistNoc);
            //         $updateId='';
            //         for($i=0;$i<count($AlreadyExistNoc->toArray());$i++){
            //             $updateId=$AlreadyExistNoc->toArray()[$i]["id"];
            //         }
            //         foreach ($request->outcome as $outputdetails) {  
            //             $data = json_decode($outputdetails, TRUE);
            //             // dd($request->project_id); 
            //             $data = \App\Models\noc_OutcomeModel::where('id',$updateId)->update([ 
            //                 'pp_id'=>$request->project_id,                   
            //                 'Permohonan_Projek_id' => $request->project_id,
            //                 'unit_id' => $data['unit_id'],
            //                 'Projek_Outcome' => $data['outcome_proj'],
            //                 'Kuantiti' => $data['Kuantiti'],
            //                 'dibuat_oleh' => $request->user_id,
            //                 'no_rujukan'=>$request->noRujukan,
            //                 'row_status' => 1,                  
            //                 'created_at' =>  Carbon::now()->format('Y-m-d H:i:s'),            
            //                 'dibuat_pada' => Carbon::now()->format('Y-m-d H:i:s'),
            //                 'dibuat_oleh' => $request->user_id,
            //                 'dikemaskini_oleh' => $request->user_id,
            //                 'dikemaskini_pada' => Carbon::now()->format('Y-m-d H:i:s'),
            //             ]);
            //         }
            //     }
            //     else{
                    // foreach ($request->outcome as $outputdetails) {  
                    //     $data = json_decode($outputdetails, TRUE); 
                    //     $data = \App\Models\noc_OutcomeModel::create([   
                    //         'pp_id' => $request->project_id,
                    //         'noc_id'=>\App\Models\noc_projectModel::latest()->first()["id"],
                    //         'Permohonan_Projek_id' => $request->project_id,
                    //         'unit_id' => $data['unit_id'],
                    //         'Projek_Outcome' => $data['outcome_proj'],
                    //         'Kuantiti' => $data['Kuantiti'],
                    //         'dibuat_oleh' => $request->user_id,
                    //         'no_rujukan'=>$request->noRujukan,
                    //         'row_status' => 1,                  
                    //         'created_at' =>  Carbon::now()->format('Y-m-d H:i:s'),            
                    //         'dibuat_pada' => Carbon::now()->format('Y-m-d H:i:s'),
                    //         'dibuat_oleh' => $request->user_id,
                    //         'dikemaskini_oleh' => $request->user_id,
                    //         'dikemaskini_pada' => Carbon::now()->format('Y-m-d H:i:s'),
                    //     ]);
                    // }
                    

                    // return response()->json([
                    //     'code' => '200',
                    //     'status' => 'Success',
                    //     'data' => $data,
                    // ]);
            //     }
            // }
            // else{

            //     $data2 = \App\Models\noc_projectModel::create([
            //         // 'noc_id'=>$request->noc_id,                    
            //         'pp_id' => $request->project_id,
            //         'skop' => null,
            //         'keterangan' => null,
            //         'komponen' => null,
            //         'nama_projek'=>null,
            //         'kod_projek'=>null,
            //         'objektif'=>null,
            //         'kos_projek'=>null,
            //         'row_status' => 1,                  
            //         'created_at' =>  Carbon::now()->format('Y-m-d H:i:s'),            
            //         'dibuat_pada' => Carbon::now()->format('Y-m-d H:i:s'),
            //         'dibuat_oleh' => $request->user_id,
            //         'dikemaskini_oleh' => $request->user_id,
            //         'dikemaskini_pada' => Carbon::now()->format('Y-m-d H:i:s'),
            //     ]);
            //     // dd($data2->id);

            //     foreach ($request->outcome as $outputdetails) {  
            //         $data = json_decode($outputdetails, TRUE); 
            //         // dd($request->project_id); 
            //         // dd(\App\Models\noc_projectModel::latest()->first()["id"]);
            //         $data = \App\Models\noc_OutcomeModel::create([   
            //             'pp_id' => $request->project_id,
            //             'noc_id'=>\App\Models\noc_projectModel::latest()->first()["id"],
            //             'Permohonan_Projek_id' => $request->project_id,
            //             'unit_id' => $data['unit_id'],
            //             'Projek_Outcome' => $data['outcome_proj'],
            //             'Kuantiti' => $data['Kuantiti'],
            //             'dibuat_oleh' => $request->user_id,
            //             'no_rujukan'=>$request->noRujukan,
            //             'row_status' => 1,                  
            //             'created_at' =>  Carbon::now()->format('Y-m-d H:i:s'),            
            //             'dibuat_pada' => Carbon::now()->format('Y-m-d H:i:s'),
            //             'dibuat_oleh' => $request->user_id,
            //             'dikemaskini_oleh' => $request->user_id,
            //             'dikemaskini_pada' => Carbon::now()->format('Y-m-d H:i:s'),
            //         ]);
            //     }
                
            //     return response()->json([
            //         'code' => '200',
            //         'status' => 'Success',
            //         'data' => $data,
            //         'data2' => $data2,

            //     ]);
            // }

        }catch (\Throwable $th) {
            logger()->error($th->getMessage());


            return response()->json([
                'code' => '500',
                'status' => 'Failed',
                'error' => $th->getMessage(),
            ]);
        }
    }

    public function nocList($id){

        try {
            $user = \App\Models\User::whereId($id)->with('bahagian')->first(); 

            

            $query = DB::table('noc_project')
                            ->leftjoin('pemantauan_project','pemantauan_project.id', '=','noc_project.pp_id')
                            ->leftjoin('status','status.status', '=','noc_project.status_id')
                            ->leftjoin('noc_checked_status','noc_checked_status.noc_id', '=','noc_project.id')
                            ->select('pemantauan_project.kod_projeck','pemantauan_project.bahagian_pemilik','pemantauan_project.no_rujukan as rujukan','pemantauan_project.nama_projek as name','noc_project.*','status.status_name','noc_checked_status.*');


                            if($user->bahagian->acym == 'BKOR'|| $user->bahagian->acym == 'BPK') { 
                                $query->whereIn('noc_project.status_id',['28','32','41','42','43','44','45']);

                                $query->orwhere(function($query1) use ($user){
                                    return $query1->orwhere('pemantauan_project.bahagian_pemilik','=',$user->bahagian_id)
                                            ->where('noc_project.status_id','=',40);
                                });
                            }
                            else 
                            {        
                                if($user->is_superadmin!=1)
                                { 
                                    $query->where('pemantauan_project.bahagian_pemilik','=',$user->bahagian_id);
                                }
                            }
                
                            $result = $query->get();

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

    public function nocPageData($id){

        try {
            $result = \App\Models\noc_projectModel::with('media')->where('id',$id)->where('row_status',1)->first();
            // dd($result->id);
            $result2 = \App\Models\noc_ButiranBaharuModel::where('noc_id',$id)->where('row_status',1)->first();

            $result3 = \App\Models\noc_SemulaButiranModel::where('noc_id',$id)->where('row_status',1)->first();

            $result_data = \App\Models\noc_projectModel::with('statuses')->where('id',$id)->where('row_status',1)->first();

            $skop_data = NocSkop::where('noc_id',$id)->where('sub_skop_id','0')->get();
            $sub_skop_data = NocSkop::where('noc_id',$id)->where('skop_kos','0')->get();

            $lampiran = $result->getMedia('lampiran_file_name')->first();
            $memo = $result->getMedia('memo_file_name')->first();

            $pindan = \App\Models\Noc_pindan::with('media','agensi')->where('noc_id', $id)->get();
            $maklumbalas_data = \App\Models\MaklumbalasPindan::with('media')->where('noc_id', $id)->get();




            return response()->json([
                'code' => '200',
                'status' => 'Success',
                'data' => $result,
                'data2' => $result2,
                'data3' => $result3,
                'result_data' => $result_data,
                'noc_skops' => $skop_data,
                'noc_sub_skops' => $sub_skop_data,
                'lampiran_file_name' => $lampiran,
                'memo_file_name' => $memo,
                'pindan_data' => $pindan,
                'maklumbalas_data' => $maklumbalas_data
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
    public function nocKpiData($id){
        try {
            
            $result = \App\Models\NocKpiModule::where('noc_id',$id)->where('row_status',1)->first();

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

    

    public function projectData($id){
        try {
            $result = \App\Models\PemantauanProject::where('id',$id)->first();

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
    public function NocOutputData($id,$noc_id){
        try {
            $result = noc_OutputModel::where('pp_id',$id)->where('noc_id',$noc_id)->get();
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

    public function NocOutcomeData($id,$noc_id){
        try {
            $result = noc_OutcomeModel::where('pp_id',$id)->where('noc_id',$noc_id)->get();

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

    public function StoreNocButiranBaharu(Request $request){
        try {

                if($request->file('lampiran_file_name')) {
                    $lampiran_file_name=$request->file('lampiran_file_name')->getClientOriginalName();
                }else {
                    $lampiran_file_name=NULL;
                }

                if($request->file('memo_file_name')) {
                    $memo_file_name=$request->file('memo_file_name')->getClientOriginalName();
                }else {
                    $memo_file_name=NULL;
                }

                $data = \App\Models\noc_projectModel::where('id',$request->noc_id)->update([
                    'pp_id' => $request->pp_id,    
                    'lampiran_file_name' => $lampiran_file_name,
                    'memo_file_name' => $memo_file_name,      
                    'dikemaskini_oleh' => $request->user_id,
                    'dikemaskini_pada' => Carbon::now()->format('Y-m-d H:i:s')
                ]);

                nocCheckedStatus::where('noc_id',$request->noc_id)->delete();
                $noc_data=new nocCheckedStatus();
                $noc_data->noc_id = $request->noc_id;
                $noc_data->pp_id  = $request->pp_id;
                $noc_data->skop_status = $request->inlineCheckbox1;
                $noc_data->kos_status = $request->inlineCheckbox4;
                $noc_data->butiran_status = $request->inlineCheckbox7;
                $noc_data->semula_status  = $request->inlineCheckbox10;
                $noc_data->nama_status    = $request->inlineCheckbox2;
                $noc_data->lokasi_status  = $request->inlineCheckbox5;
                $noc_data->kpi_status     = $request->inlineCheckbox8;
                $noc_data->outcome_status = $request->inlineCheckbox11;
                $noc_data->kod_status     = $request->inlineCheckbox3;
                $noc_data->objectif_status = $request->inlineCheckbox6;
                $noc_data->output_status   = $request->inlineCheckbox9;
                $noc_data->dibuat_oleh     = $request->user_id;
                $noc_data->dibuat_pada     =  Carbon::now()->format('Y-m-d H:i:s');
                $noc_data->dikemaskini_oleh = $request->user_id;
                $noc_data->dikemaskini_pada =  Carbon::now()->format('Y-m-d H:i:s');
                $noc_data->save();

                \App\Models\noc_ButiranBaharuModel::where('noc_id', $request->noc_id)->delete();
                \App\Models\noc_SemulaButiranModel::where('noc_id', $request->noc_id)->delete();


                $data2 = \App\Models\noc_ButiranBaharuModel::create([
                        'pp_id' => $request->pp_id,
                        'noc_id'=>$request->noc_id,                    
                        'nama_projek' => $request->namaProjek,
                        'justifikasi' => $request->justification,
                        'kod_projek'=>$request->kod_projek,
                        'keperluan'=>$request->sKeperluan,
                        'kos_projek'=>$request->kosProjek,
                        'row_status' => 1,                  
                        'created_at' =>  Carbon::now()->format('Y-m-d H:i:s'),            
                        'dibuat_pada' => Carbon::now()->format('Y-m-d H:i:s'),
                        'dibuat_oleh' => $request->user_id,
                        'dikemaskini_oleh' => $request->user_id,
                        'dikemaskini_pada' => Carbon::now()->format('Y-m-d H:i:s'),
                ]);

                $result_data = noc_projectModel::with('media')->where('id',$request->noc_id)->first();

                if($request->file('lampiran_file_name')) {
                    $result_data->clearMediaCollection('lampiran_file_name');
                    $result_data
                    ->addMedia($request->file('lampiran_file_name'))
                    ->toMediaCollection('lampiran_file_name');
                }

                if($request->file('memo_file_name')) {
                    $result_data->clearMediaCollection('memo_file_name');
                    $result_data
                    ->addMedia($request->file('memo_file_name'))
                    ->toMediaCollection('memo_file_name');
                }

            // dd($data);
            //$AlreadyExist = \App\Models\noc_projectModel::where('id',$request->projekID)->first();
            //$this->deleteAllData($request->projekID);  //for deleting all the other datas
            // if($request->projekID){
            //     // dd($AlreadyExist);
            //     $datas = \App\Models\noc_ButiranBaharuModel::where('noc_id',$request->projekID)->update([
                                        
            //         'nama_projek' => $request->wujudProjekName,
            //         'justifikasi' => $request->justifikasi,
            //         'kod_projek'=>$request->kod_projek,
            //         'keperluan'=>$request->keperluan,
            //         'kos_projek'=>$request->kos_projek,
            //         'row_status' => 1,                  
            //         'created_at' =>  Carbon::now()->format('Y-m-d H:i:s'),            
            //         'dibuat_pada' => Carbon::now()->format('Y-m-d H:i:s'),
            //         'dibuat_oleh' => $request->user_id,
            //         'dikemaskini_oleh' => $request->user_id,
            //         'dikemaskini_pada' => Carbon::now()->format('Y-m-d H:i:s'),
            //     ]);
            //         return response()->json([
            //             'code' => '200',
            //             'status' => 'Success',
            //             'data' => $datas,
            //         ]);
            // }
            // else{

            //     $data2 = \App\Models\noc_projectModel::create([
            //         // 'noc_id'=>$request->noc_id,                    
            //         // 'pp_id' => $request->project_id,
            //         'skop' => null,
            //         'keterangan' => null,
            //         'komponen' => null,
            //         'nama_projek'=>null,
            //         'kod_projek'=>null,
            //         'objektif'=>null,
            //         'kos_projek'=>null,
            //         'row_status' => 1,          
            //         'status_id'=>40,        
            //         'created_at' =>  Carbon::now()->format('Y-m-d H:i:s'),            
            //         'dibuat_pada' => Carbon::now()->format('Y-m-d H:i:s'),
            //         'dibuat_oleh' => $request->user_id,
            //         'dikemaskini_oleh' => $request->user_id,
            //         'dikemaskini_pada' => Carbon::now()->format('Y-m-d H:i:s'),
            //     ]);
                
            //     // dd($data2->id);
            //     // \App\Models\noc_projectModel::latest()->first()["id"],
            //     $data = \App\Models\noc_ButiranBaharuModel::create([
            //         'noc_id'=>$data2->id,                    
            //         'nama_projek' => $request->wujudProjekName,
            //         'justifikasi' => $request->justifikasi,
            //         'kod_projek'=>$request->kod_projek,
            //         'keperluan'=>$request->keperluan,
            //         'kos_projek'=>$request->kos_projek,
            //         'row_status' => 1,                  
            //         'created_at' =>  Carbon::now()->format('Y-m-d H:i:s'),            
            //         'dibuat_pada' => Carbon::now()->format('Y-m-d H:i:s'),
            //         'dibuat_oleh' => $request->user_id,
            //         'dikemaskini_oleh' => $request->user_id,
            //         'dikemaskini_pada' => Carbon::now()->format('Y-m-d H:i:s'),
            //     ]);
                
                return response()->json([
                    'code' => '200',
                    'status' => 'Success',
                    'data' => $data,
                    'data2' => $data2,

                ]);

        }catch (\Throwable $th) {
            logger()->error($th->getMessage());


            return response()->json([
                'code' => '500',
                'status' => 'Failed',
                'error' => $th->getMessage(),
            ]);
        }
            
    }

    public function StoreNocSemulaButiran(Request $request){
        try {

                if($request->file('lampiran_file_name')) {
                    $lampiran_file_name=$request->file('lampiran_file_name')->getClientOriginalName();
                }else {
                    $lampiran_file_name=NULL;
                }

                if($request->file('memo_file_name')) {
                    $memo_file_name=$request->file('memo_file_name')->getClientOriginalName();
                }else {
                    $memo_file_name=NULL;
                }

                $data = \App\Models\noc_projectModel::where('id',$request->noc_id)->update([
                        'pp_id' => $request->pp_id,      
                        'lampiran_file_name' => $lampiran_file_name,
                        'memo_file_name' => $memo_file_name,    
                        'dikemaskini_oleh' => $request->user_id,
                        'dikemaskini_pada' => Carbon::now()->format('Y-m-d H:i:s'),
                        'justifikasi' => $request->justification,
                    ]);

                    nocCheckedStatus::where('noc_id',$request->noc_id)->delete();
                    $noc_data=new nocCheckedStatus();
                    $noc_data->noc_id = $request->noc_id;
                    $noc_data->pp_id  = $request->pp_id;
                    $noc_data->skop_status = $request->inlineCheckbox1;
                    $noc_data->kos_status = $request->inlineCheckbox4;
                    $noc_data->butiran_status = $request->inlineCheckbox7;
                    $noc_data->semula_status  = $request->inlineCheckbox10;
                    $noc_data->nama_status    = $request->inlineCheckbox2;
                    $noc_data->lokasi_status  = $request->inlineCheckbox5;
                    $noc_data->kpi_status     = $request->inlineCheckbox8;
                    $noc_data->outcome_status = $request->inlineCheckbox11;
                    $noc_data->kod_status     = $request->inlineCheckbox3;
                    $noc_data->objectif_status = $request->inlineCheckbox6;
                    $noc_data->output_status   = $request->inlineCheckbox9;
                    $noc_data->dibuat_oleh     = $request->user_id;
                    $noc_data->dibuat_pada     =  Carbon::now()->format('Y-m-d H:i:s');
                    $noc_data->dikemaskini_oleh = $request->user_id;
                    $noc_data->dikemaskini_pada =  Carbon::now()->format('Y-m-d H:i:s');
                    $noc_data->save();

                \App\Models\noc_SemulaButiranModel::where('noc_id', $request->noc_id)->delete();
                \App\Models\noc_ButiranBaharuModel::where('noc_id', $request->noc_id)->delete();

                $data_new = \App\Models\noc_SemulaButiranModel::create([
                        'noc_id'=>$request->noc_id,                    
                        'pp_id'=>$request->pp_id,                   
                        'nama_projek' => $request->namaProjek,
                        'justifikasi' => $request->justification,
                        'kod_projek'=>$request->kodProjek,
                        'keperluan'=>$request->sKeperluan,
                        'kos_projek'=>$request->kosProjek,
                        'row_status' => 1,                  
                        'created_at' =>  Carbon::now()->format('Y-m-d H:i:s'),            
                        'dibuat_pada' => Carbon::now()->format('Y-m-d H:i:s'),
                        'dibuat_oleh' => $request->user_id,
                        'dikemaskini_oleh' => $request->user_id,
                        'dikemaskini_pada' => Carbon::now()->format('Y-m-d H:i:s'),
                ]);

                $result_data = noc_projectModel::with('media')->where('id',$noc_id)->first();

                if($request->file('lampiran_file_name')) {
                    $result_data->clearMediaCollection('lampiran_file_name');
                    $result_data
                    ->addMedia($request->file('lampiran_file_name'))
                    ->toMediaCollection('lampiran_file_name');
                }

                if($request->file('memo_file_name')) {
                    $result_data->clearMediaCollection('memo_file_name');
                    $result_data
                    ->addMedia($request->file('memo_file_name'))
                    ->toMediaCollection('memo_file_name');
                }
                
                return response()->json([
                    'code' => '200',
                    'status' => 'Success',
                    'data' => $data,
                    'data2' => $data_new,

                ]);
           // }
           // $this->deleteAllData($request->projekID);  //for deleting all the other datas


        }catch (\Throwable $th) {
            logger()->error($th->getMessage());


            return response()->json([
                'code' => '500',
                'status' => 'Failed',
                'error' => $th->getMessage(),
            ]);
        }
    }

    public function deleteAllData($id)
    {
        noc_negeri::where('pp_id', $id)->delete();
        noc_OutcomeModel::where('pp_id', $id)->delete();
        noc_OutputModel::where('pp_id', $id)->delete();
        NocKpiModule::where('pp_id', $id)->delete();
        //noc_projectModel::where('pp_id', $id)->delete();
    }

    public function StatusUpdate(Request $request){
        try {            
                $data = \App\Models\noc_projectModel::where('id', $request->id)->update([  
                            'status_id' => $request->status,             
                            'dikemaskini_oleh' => $request->user_id,
                            'dikemaskini_pada' => Carbon::now()->format('Y-m-d H:i:s'),
                ]);

                return response()->json([
                    'code' => '200',
                    'status' => 'Success',
                    'data' => $data,
                ]);

            } catch (\Throwable $th) {
                logger()->error($th->getMessage());


                return response()->json([
                    'code' => '500',
                    'status' => 'Failed',
                    'error' => $th->getMessage(),
                ]);
            }
    }

    public function KementerianUpdate(Request $request){
        try {            
                $kementerian_file_name=$request->file('kementerian_file_name')->getClientOriginalName();
                $result = NOCKementerian::Create(
                [
                    'noc_id'=>$request->id,
                    'kementerian_tarikh'=>$request->kementerian_date,
                    'kementerian_file_name' =>$kementerian_file_name,
                    'dibuat_oleh' => $request->user_id,
                    'dikemaskini_oleh' => $request->user_id,
                    'dikemaskini_pada' => Carbon::now()->format('Y-m-d H:i:s')
                ]
                );

                $result_data = NOCKementerian::with('media')->where('id',$result['id'])->first();


                if($request->file('kementerian_file_name')) {
                    $result_data->clearMediaCollection('kementerian_file_name');
                    $result_data
                    ->addMedia($request->file('kementerian_file_name'))
                    ->toMediaCollection('kementerian_file_name');
                }

                noc_projectModel::where('id', $request->id)->update([  
                    'status_id' => $request->status,             
                    'dikemaskini_oleh' => $request->user_id,
                    'dikemaskini_pada' => Carbon::now()->format('Y-m-d H:i:s'),
                ]);

                return response()->json([
                    'code' => '200',
                    'status' => 'Success',
                    'data'=> $result_data
                ]);

            } catch (\Throwable $th) {
                logger()->error($th->getMessage());


                return response()->json([
                    'code' => '500',
                    'status' => 'Failed',
                    'error' => $th->getMessage(),
                ]);
            }
    }

    public function nocKementerianData($id){

        try {

            $data['noc'] = noc_projectModel::where('id',$id)->where('row_status',1)->first();
            $data['noc_kementerian'] = NOCKementerian::with(['media'])->where('noc_id',$id)->where('row_status',1)->first();
            $data['noc_economi'] = NOCKementerianEconomi::where('noc_id',$id)->where('row_status',1)->first();


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

    public function previewfile(Request $request ,Media $mediaItem){
        try{
            $id = $request->id;
            if($request->type=='kementerian_file_name')
            {
                $doc = NOCKementerian::where('id','=',$id)->with('media')->first();
            }
            else
            {
                $doc = NOCKementerianEconomi::where('id','=',$id)->with('media')->first();
            }

            $mediaItem = $doc->getMedia($request->type)->first(); //dd($mediaItem);
            return response()->download($mediaItem->getPath(), $mediaItem->file_name);

        } catch (\Throwable $th) {
            logger()->error($th->getMessage());

            return response()->json([
                'code' => '500',
                'status' => 'Failed',
                'error' => $th,
            ]);
        }
    }

    public function KementerianEconomicUpdate(Request $request){
        try {            
                $economi_file_name=$request->file('economi_hanter_file_name')->getClientOriginalName();
                $economi_surat_file_name=$request->file('economi_surat_file_name')->getClientOriginalName();

                $result = NOCKementerianEconomi::Create(
                [
                    'noc_id'=>$request->id,
                    'economi_tarikh'=>$request->economi_date,
                    'economi_file_name' =>$economi_file_name,
                    'economi_surat_tarikh' =>$request->economi_surat_date,
                    'economi_surat_file_name' =>$economi_surat_file_name,
                    'status' =>$request->status_permohonan,
                    'catatan' =>$request->catatan,
                    'dibuat_oleh' => $request->user_id,
                    'dikemaskini_oleh' => $request->user_id,
                    'dikemaskini_pada' => Carbon::now()->format('Y-m-d H:i:s')
                ]
                );

                $result_data = NOCKementerianEconomi::with('media')->where('id',$result['id'])->first();


                if($request->file('economi_hanter_file_name')) {
                    $result_data->clearMediaCollection('economi_hanter_file_name');
                    $result_data
                    ->addMedia($request->file('economi_hanter_file_name'))
                    ->toMediaCollection('economi_hanter_file_name');
                }

                if($request->file('economi_surat_file_name')) {
                    $result_data->clearMediaCollection('economi_surat_file_name');
                    $result_data
                    ->addMedia($request->file('economi_surat_file_name'))
                    ->toMediaCollection('economi_surat_file_name');
                }

                noc_projectModel::where('id', $request->id)->update([  
                    'status_id' => $request->status,             
                    'dikemaskini_oleh' => $request->user_id,
                    'dikemaskini_pada' => Carbon::now()->format('Y-m-d H:i:s'),
                ]);

                return response()->json([
                    'code' => '200',
                    'status' => 'Success',
                    'data'=> $result_data
                ]);

            } catch (\Throwable $th) {
                logger()->error($th->getMessage());


                return response()->json([
                    'code' => '500',
                    'status' => 'Failed',
                    'error' => $th->getMessage(),
                ]);
            }
    }


    public function ListNoc(Request $request){
        try {

            if($request->va==1)
            {
                $data = NOCPeruntukan::where('type',1)->where('row_status',1)->get();
            }
            else
            {
                $data = NOCPeruntukan::where('type',0)->where('row_status',1)->get();
            }
            //$data = refNegeri::with('updatedBy')->get();
            return response()->json([
                'code' => '200',
                'status' => 'Success',
                'data' => $data,
            ]);

        } catch (\Throwable $th) {
            logger()->error($th->getMessage());


            return response()->json([
                'code' => '500',
                'status' => 'Failed',
                'error' => $th,
            ]);
        }
        
    }

    public function addNOC(Request $request){
        try {

            $startDate = $request->tarikh_buka;
            $endDate = $request->tarikh_tutup;

            $existingEvents = NOCPeruntukan::where(function ($query) use ($startDate, $endDate) {
                $query->where(function ($subQuery) use ($startDate, $endDate) {
                    $subQuery->where('tarikh_buka', '<=', $startDate)
                             ->where('tarikh_tutup', '>=', $startDate);
                })->orWhere(function ($subQuery) use ($startDate, $endDate) {
                    $subQuery->where('tarikh_buka', '<=', $endDate)
                             ->where('tarikh_tutup', '>=', $endDate);
                })->orWhere(function ($subQuery) use ($startDate, $endDate) {
                    $subQuery->where('tarikh_buka', '>=', $startDate)
                             ->where('tarikh_tutup', '<=', $endDate);
                });
            })->exists();


            if($existingEvents==false){

                $noc_data = new NOCPeruntukan();
                $noc_data->bilangan     = $request->bilangan;
                $noc_data->tahun        = $request->tahun;
                $noc_data->tarikh_buka  = $request->tarikh_buka;
                $noc_data->tarikh_tutup = $request->tarikh_tutup;
                $noc_data->status_permohonan = 0;
                $noc_data->status = 0;
                $noc_data->type = $request->type;
                $noc_data->dibuat_oleh = $request->user_id;
                $noc_data->dikemaskini_oleh = $request->user_id;
                $noc_data->dikemaskini_pada = Carbon::now()->format('Y-m-d H:i:s');
                $noc_data->save();

                return response()->json([
                    'code' => '200',
                    'status' => 'Success'
                ]);
            }
            else
            {

                return response()->json([
                    'code' => '500',
                    'status' => 'Already Exists'
                ]);
            }
            
            

        } catch (\Throwable $th) {
            logger()->error($th->getMessage());


            return response()->json([
                'code' => '500',
                'status' => 'Failed',
                'error' => $th,
            ]);
        }
        
    }

    public function getNocData(Request $request){
        try {

            $currentDate = Carbon::now();
            $data  = NOCPeruntukan::where('tarikh_buka', '<=', $currentDate)
                                    ->where('tarikh_tutup', '>=', $currentDate)
                                    ->first();
        
            $data1 = CmsPengumuman::where('row_status',1)->with('updatedBy','media')->first();

            //$data = refNegeri::with('updatedBy')->get();
            return response()->json([
                'code' => '200',
                'status' => 'Success',
                'data' => $data,
                'data_pop_up' => $data1
            ]);

        } catch (\Throwable $th) {
            logger()->error($th->getMessage());


            return response()->json([
                'code' => '500',
                'status' => 'Failed',
                'error' => $th,
            ]);
        }    
    }

    public function getOldProjectDetails($id){

        try {

            $vm_skop = VmSkop::where('pp_id',$id)->where('row_status',1)->get(); 
            if(count($vm_skop)>0)
            { 
                $data['skop'] = VmSkop::where('pp_id',$id)->with('va')->get();
                $data['type'] = 'vm';
            }
            else
            { 
                $data['skop'] = PemantauanSkopProjects::where('pp_id',$id)->with(['pemantauanskopOptions'])->get();
                $data['type'] = 'pemantauan';
            }

            $data['skop_cmp'] = PemantauanKewanganSkop::where('pp_id',$id)->get();

            $vm_objectif = VmObjektif::where('pp_id',$id)->where('row_status',1)->get(); 
            if(count($vm_objectif)>0)
            { 
                $data['objectif'] = VmObjektif::where('pp_id',$id)->with('va')->get();
                $data['obj_type'] = 'vm';
            }
            else
            { 
                $data['objectif'] = PemantauanProject::where('id',$id)->select('objektif')->get();
                $data['obj_type'] = 'pemantauan';
            }

            $vm_output = VmOutput::where('pp_id',$id)->where('row_status',1)->get(); 
            if(count($vm_output)>0)
            { 
                $data['output'] = VmOutput::with('unit')->where('pp_id',$id)->with('va')->get();
                $data['output_type'] = 'vm';
            }
            else
            { 
                $data['output'] = DB::table('pemantauan_output')
                                    ->join('REF_Unit','REF_Unit.id', '=','pemantauan_output.unit_id')
                                    ->where('pemantauan_output.pp_id','=',$id)->get();
                $data['output_type'] = 'pemantauan';
            }

            $vm_outcome = VmOutcome::where('pp_id',$id)->where('row_status',1)->get(); 
            if(count($vm_outcome)>0)
            { 
                $data['outcome'] = VmOutcome::with('unit')->where('pp_id',$id)->with('va')->get();
                $data['outcome_type'] = 'vm';
            }
            else
            { 
                $data['outcome'] = DB::table('pemantauan_outcome')
                                      ->join('REF_Unit','REF_Unit.id', '=','pemantauan_outcome.unit_id')
                                      ->where('pemantauan_outcome.pp_id','=',$id)->get();
                $data['outcome_type'] = 'pemantauan';
            }

            $data['project_data'] = PemantauanProject::with(['bahagianPemilik','rmk'])->where('id',$id)->first();

        
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

    public function getCheckboxStatuses($pp_id,$noc_id) {

        try {

                $data=nocCheckedStatus::where('noc_id',$noc_id)->first();

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

    public function ListProjects($id)
    {
        try {

            $data=PemantauanProject::get();
            $data1=nocSelectedProjeck::with('bahagianPemilik','peruntukan')->where('noc_id',$id)->get();

            return response()->json([
                'code' => '200',
                'status' => 'Success',
                'data' => $data,
                'noc_data'=>$data1
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

    public function addNOCProject(Request $request){
        try {
                if($request->project_array)
                {
                    nocSelectedProjeck::where('noc_id',$request->noc_id)->delete();  

                    foreach($request->project_array as $project){
                        $project_json = json_decode($project,TRUE);

                        $noc_data = new nocSelectedProjeck;
                        $noc_data->noc_id= $request->noc_id;
                        $noc_data->pp_id = $project_json['pp_id'];
                        $noc_data->no_rujukan = $project_json['rujukan'];
                        $noc_data->kod_projeck= $project_json['kod'];
                        $noc_data->butiran_code= $project_json['butiran'];
                        $noc_data->nama_projek = $project_json['nama'];
                        $noc_data->kos_projeck  = $project_json['kos'];
                        $noc_data->dibuat_oleh = $request->user_id;
                        $noc_data->dikemaskini_oleh = $request->user_id;
                        $noc_data->bahagian_pemilik = 4;
                        $noc_data->dikemaskini_pada = Carbon::now()->format('Y-m-d H:i:s');
                        $noc_data->dibuat_pada = Carbon::now()->format('Y-m-d H:i:s');
                        $noc_data->save();  
                    }      
                }
                return response()->json([
                    'code' => '200',
                    'status' => 'Success'
                ]);
            
            } catch (\Throwable $th) {
                logger()->error($th->getMessage());
            return response()->json([
                    'code' => '500',
                    'status' => 'Failed',
                    'error' => $th,
                ]);
            }    
    }

    public function deleteNOC(Request $request){
        try {
                nocSelectedProjeck::where('id',$request->noc_id)->delete();  
                return response()->json([
                    'code' => '200',
                    'status' => 'Success'
                ]);
            } catch (\Throwable $th) {
                logger()->error($th->getMessage());
                return response()->json([
                    'code' => '500',
                    'status' => 'Failed',
                    'error' => $th,
                ]);
            }    
    }

    public function updateNocStatus(Request $request){
        try {
                $data=NOCPeruntukan::where('id',$request->noc_id)->first();  
                $data->status=$request->status;
                $data->update();

                return response()->json([
                    'code' => '200',
                    'status' => 'Success'
                ]);
            } catch (\Throwable $th) {
                logger()->error($th->getMessage());
                return response()->json([
                    'code' => '500',
                    'status' => 'Failed',
                    'error' => $th,
                ]);
            }    
    }

    public function nocKementerianSillingData($id){

        try {
            $data['noc'] = NOCPeruntukan::where('id',$id)->where('row_status',1)->first();
            $result=nocKementerianSilling::with(['media'])->where('noc_id',$id)->where('row_status',1)->first();
            $data['noc_kementerian'] = $result;
            $data['noc_kementerian_file'] = $result->getMedia('kementerian_file_name')->first();
            $data['noc_kelulusan_file'] = $result->getMedia('kelulusan_file_name')->first();
            $result_noc_economi = nocKementerianEconomiSilling::with(['media'])->where('noc_id',$id)->where('row_status',1)->first();
            $data['noc_economi'] = $result_noc_economi;
            $data['noc_economi_kementerian_file'] = $result_noc_economi->getMedia('economi_hanter_file_name')->first();
            $data['noc_economi_kelulusan_file'] = $result_noc_economi->getMedia('economi_surat_file_name')->first();

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

    public function saveNocData(Request $request){
        try {
           Log::info($request);
            $data = noc_projectModel::create([
                                                    // 'noc_id'=>$request->noc_id,                    
                                                    'pp_id' => $request->project_id,
                                                    'skop' => NULL,
                                                    'keterangan' => NULL,
                                                    'komponen' => NULL,
                                                    'nama_projek'=>NULL,
                                                    'kod_projek'=>NULL,
                                                    'objektif'=>NULL,
                                                    'kos_projek'=>NULL,
                                                    'row_status' => 1, 
                                                    'status_id' => 40,                 
                                                    'created_at' =>  Carbon::now()->format('Y-m-d H:i:s'),            
                                                    'dibuat_pada' => Carbon::now()->format('Y-m-d H:i:s'),
                                                    'dibuat_oleh' => $request->user_id,
                                                    'dikemaskini_oleh' => $request->user_id,
                                                    'dikemaskini_pada' => Carbon::now()->format('Y-m-d H:i:s'),
                                                    'justifikasi' => $request->justification
                                                ]);

                $noc_id=$data['id'];
            

            Log::info($noc_id);

              nocCheckedStatus::where('noc_id',$noc_id)->delete();
              $noc_data=new nocCheckedStatus();
              $noc_data->noc_id = $noc_id;
              $noc_data->pp_id  = $request->project_id;
              $noc_data->skop_status = $request->inlineCheckbox1;
              $noc_data->kos_status = $request->inlineCheckbox4;
              $noc_data->butiran_status = $request->inlineCheckbox7;
              $noc_data->semula_status  = $request->inlineCheckbox10;
              $noc_data->nama_status    = $request->inlineCheckbox2;
              $noc_data->lokasi_status  = $request->inlineCheckbox5;
              $noc_data->kpi_status     = $request->inlineCheckbox8;
              $noc_data->outcome_status = $request->inlineCheckbox11;
              $noc_data->kod_status     = $request->inlineCheckbox3;
              $noc_data->objectif_status = $request->inlineCheckbox6;
              $noc_data->output_status   = $request->inlineCheckbox9;
              $noc_data->dibuat_oleh     = $request->user_id;
              $noc_data->dibuat_pada     =  Carbon::now()->format('Y-m-d H:i:s');
              $noc_data->dikemaskini_oleh = $request->user_id;
              $noc_data->dikemaskini_pada =  Carbon::now()->format('Y-m-d H:i:s');
              $noc_data->save();


            return response()->json([
                'code' => '200',
                'status' => 'Success',
                'data' => $data,
            ]);

        } catch (\Throwable $th) {
            logger()->error($th->getMessage());


            return response()->json([
                'code' => '500',
                'status' => 'Failed',
                'error' => $th->getMessage(),
            ]);
        }

    }

    public function savePindanData(Request $request)
    {
        try{

            if($request->file('lampiran_pindan_file_name')) {
                $lampiran_pindan_file_name=$request->file('lampiran_pindan_file_name')->getClientOriginalName();
            }else {
                $lampiran_pindan_file_name=NULL;
            }

            //\App\Models\Noc_pindan::where('noc_id', $request->noc_id)->delete();

            $data = \App\Models\Noc_pindan::create([
                    'noc_id'=>$request->noc_id,                    
                    'pp_id'=>$request->pp_id,                   
                    'lampiran_pindan_file_name' => $lampiran_pindan_file_name,
                    'agensi' => $request->agensi,
                    'maklumat_pindan_date'=>$request->maklumat_pindan_date,
                    'ringasakan_ulasan'=>$request->ringasakan_ulasan,
                    'row_status' => 1,                  
                    'created_at' =>  Carbon::now()->format('Y-m-d H:i:s'),            
                    'dibuat_pada' => Carbon::now()->format('Y-m-d H:i:s'),
                    'dibuat_oleh' => $request->user_id,
                    'dikemaskini_oleh' => $request->user_id,
                    'dikemaskini_pada' => Carbon::now()->format('Y-m-d H:i:s'),
            ]);

            $result_data = \App\Models\Noc_pindan::with('media')->where('id',$data['id'])->first();

            Log::info($result_data);

            if($request->file('lampiran_pindan_file_name')) {
                $result_data
                ->addMedia($request->file('lampiran_pindan_file_name'))
                ->toMediaCollection('lampiran_pindan_file_name');
            }

            return response()->json([
                'code' => '200',
                'status' => 'Success',
                'data' => $data,
            ]);

        } catch (\Throwable $th) {
            logger()->error($th->getMessage());


            return response()->json([
                'code' => '500',
                'status' => 'Failed',
                'error' => $th->getMessage(),
            ]);
        }
    }

    public function saveMaklubalasData(Request $request)
    {
        try{

            if($request->file('maklubalas_file_name')) {
                $maklubalas_file_name=$request->file('maklubalas_file_name')->getClientOriginalName();
            }else {
                $maklubalas_file_name=NULL;
            }

            $data = \App\Models\MaklumbalasPindan::create([
                    'noc_id'=>$request->noc_id,                    
                    'pp_id'=>$request->pp_id,                   
                    'maklubalas_file_name' => $maklubalas_file_name,
                    'maklubalas_date'=>$request->maklubalas_date,
                    'row_status' => 1,                  
                    'created_at' =>  Carbon::now()->format('Y-m-d H:i:s'),            
                    'dibuat_pada' => Carbon::now()->format('Y-m-d H:i:s'),
                    'dibuat_oleh' => $request->user_id,
                    'dikemaskini_oleh' => $request->user_id,
                    'dikemaskini_pada' => Carbon::now()->format('Y-m-d H:i:s'),
            ]);

            $result_data = \App\Models\MaklumbalasPindan::with('media')->where('id',$data['id'])->first();

            if($request->file('maklubalas_file_name')) {
                $result_data
                ->addMedia($request->file('maklubalas_file_name'))
                ->toMediaCollection('maklubalas_file_name');
            }

            return response()->json([
                'code' => '200',
                'status' => 'Success',
                'data' => $data,
            ]);

        } catch (\Throwable $th) {
            logger()->error($th->getMessage());
            return response()->json([
                'code' => '500',
                'status' => 'Failed',
                'error' => $th->getMessage(),
            ]);
        }
    }

    public function updateBilanganData(Request $request){
        try {
                if($request->noc_jps_data){ 

                    foreach ($request->noc_jps_data as $noc_jps_data) {  
                        $sub_json = json_decode($noc_jps_data,TRUE); 

                        $bilanagan_data = nocSelectedProjeck::where('id',$sub_json['id'])->first();
                            $bilanagan_data->peruntukan_asal = $sub_json['peruntukan_asal'];
                            $bilanagan_data->tambah = $sub_json['tamba'];
                            $bilanagan_data->kurang = $sub_json['kurang'];
                            $bilanagan_data->dipinda = $sub_json['dipinda'];
                            $bilanagan_data->justifikasi = $sub_json['jps_justifikasi'];
                            $bilanagan_data->update();
                    }
                }

                return response()->json([
                    'code' => '200',
                    'status' => 'Success'
                ]);
            } catch (\Throwable $th) {
                logger()->error($th->getMessage());
                return response()->json([
                    'code' => '500',
                    'status' => 'Failed',
                    'error' => $th,
                ]);
            }    
    }

    public function KementerianSillingUpdate(Request $request){
        try {            
                $kementerian_file_name=$request->file('kementerian_file_name')->getClientOriginalName();
                $kelulusan_file_name=$request->file('kelulusan_file_name')->getClientOriginalName();

                $result = nocKementerianSilling::Create(
                [
                    'noc_id'=>$request->id,
                    'kementerian_tarikh'=>$request->kementerian_date,
                    'kementerian_file_name' =>$kementerian_file_name,
                    'kelulusan_file_name' => $kelulusan_file_name,
                    'kelulusan_tarikh' => $request->kelulusan_date,
                    'dibuat_oleh' => $request->user_id,
                    'dikemaskini_oleh' => $request->user_id,
                    'dikemaskini_pada' => Carbon::now()->format('Y-m-d H:i:s')
                ]
                );

                $result_data = nocKementerianSilling::with('media')->where('id',$result['id'])->first();

                if($request->file('kementerian_file_name')) {
                    $result_data->clearMediaCollection('kementerian_file_name');
                    $result_data
                    ->addMedia($request->file('kementerian_file_name'))
                    ->toMediaCollection('kementerian_file_name');
                }

                if($request->file('kelulusan_file_name')) {
                    $result_data->clearMediaCollection('kelulusan_file_name');
                    $result_data
                    ->addMedia($request->file('kelulusan_file_name'))
                    ->toMediaCollection('kelulusan_file_name');
                }

                return response()->json([
                    'code' => '200',
                    'status' => 'Success',
                    'data'=> $result_data
                ]);

            } catch (\Throwable $th) {
                logger()->error($th->getMessage());


                return response()->json([
                    'code' => '500',
                    'status' => 'Failed',
                    'error' => $th->getMessage(),
                ]);
            }
    }

    public function KementerianSillingEconomicUpdate(Request $request){
        try {            
                $economi_file_name=$request->file('economi_hanter_file_name')->getClientOriginalName();
                $economi_surat_file_name=$request->file('economi_surat_file_name')->getClientOriginalName();

                $result = nocKementerianEconomiSilling::Create(
                [
                    'noc_id'=>$request->id,
                    'economi_tarikh'=>$request->economi_date,
                    'economi_file_name' =>$economi_file_name,
                    'economi_surat_tarikh' =>$request->economi_surat_date,
                    'economi_surat_file_name' =>$economi_surat_file_name,
                    'status' =>1,
                    'dibuat_oleh' => $request->user_id,
                    'dikemaskini_oleh' => $request->user_id,
                    'dikemaskini_pada' => Carbon::now()->format('Y-m-d H:i:s')
                ]
                );

                $result_data = nocKementerianEconomiSilling::with('media')->where('id',$result['id'])->first();


                if($request->file('economi_hanter_file_name')) {
                    $result_data->clearMediaCollection('economi_hanter_file_name');
                    $result_data
                    ->addMedia($request->file('economi_hanter_file_name'))
                    ->toMediaCollection('economi_hanter_file_name');
                }

                if($request->file('economi_surat_file_name')) {
                    $result_data->clearMediaCollection('economi_surat_file_name');
                    $result_data
                    ->addMedia($request->file('economi_surat_file_name'))
                    ->toMediaCollection('economi_surat_file_name');
                }

                return response()->json([
                    'code' => '200',
                    'status' => 'Success',
                    'data'=> $result_data
                ]);

            } catch (\Throwable $th) {
                logger()->error($th->getMessage());


                return response()->json([
                    'code' => '500',
                    'status' => 'Failed',
                    'error' => $th->getMessage(),
                ]);
            }
    }
}
