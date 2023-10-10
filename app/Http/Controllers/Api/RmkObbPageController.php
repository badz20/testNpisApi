<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use \App\Models\RmkObbPage;
use \App\Models\RmkSDGIndikator;
use \App\Models\RMKSDGSasaranIndikator;
use \App\Models\RmkSdg;
use \App\Models\RmkObb;
use \App\Models\projectLog;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Jenssegers\Agent\Facades\Agent;




class RmkObbPageController extends Controller
{
    public function list(Request $request)
    {
        try {
            if($request->id){
                $projectid = $request->id;
                $data['rmkpage'] = \App\Models\RmkObbPage::where('permohonan_projek_id','=',$request->id)->first();
                $data['sdg'] = \App\Models\RMKSDGSasaranIndikator::where('permohonan_projek_id','=',$request->id)->where('row_status','=',1)->get();
                $data['allsdg'] = \App\Models\RmkSdg::get();
                $data['all_distinct_sdg'] = DB::table('RMK_SDG_Sasaran_Indikator')->select(DB::raw('DISTINCT SDG_id'))
                                                                                    ->where('permohonan_projek_id',$request->id)
                                                                                    ->where('row_status','=',1)
                                                                                    ->get();
                $data['all_sasarans'] = DB::table('REF_Sasaran')->select(DB::raw('*'))
                                                                ->wherein('SDG_id',function($query) use ($projectid)
                                                                {
                                                                    $query->select(DB::raw('SDG_id'))
                                                                            ->from('RMK_SDG_Sasaran_Indikator')
                                                                            ->where('permohonan_projek_id','=',$projectid)
                                                                            ->whereRaw('row_status=1');
                                                                })->get();
                $data['all_indikators'] = DB::table('REF_Indikatori')->select(DB::raw('*'))
                                                                ->wherein('Sasaran_id',function($query) use ($projectid)
                                                                {
                                                                    $query->select(DB::raw('Sasaran_id'))
                                                                            ->from('RMK_SDG_Sasaran_Indikator')
                                                                            ->where('permohonan_projek_id','=',$projectid)
                                                                            ->whereRaw('row_status=1');
                                                                })->get();   
                $data['indikatori'] = \App\Models\RmkSDGIndikator::where('permohonan_projek_id','=',$request->id)->where('row_status','=',1)->get();
                $data['activity'] = \App\Models\RmkObb::get();

            }else {
                $data['rmkpage'] = \App\Models\RmkObbPage::get();
                $data['sdg'] = \App\Models\RMKSDGSasaranIndikator::where('permohonan_projek_id','=',$request->id)->where('row_status','=',1)->get();
                $data['allsdg'] = \App\Models\RmkSdg::get();
                $data['all_distinct_sdg'] = DB::table('RMK_SDG_Sasaran_Indikator')->select(DB::raw('DISTINCT SDG_id'))
                                                                                    ->where('permohonan_projek_id',$request->id)
                                                                                    ->get();
                $data['all_sasaran'] = DB::table('REF_Sasaran')->select(DB::raw('*'))
                                                                ->wherein('SDG_id',function($query) use ($projectid)
                                                                {
                                                                    $query->select(DB::raw('SDG_id'))
                                                                            ->from('RMK_SDG_Sasaran_Indikator')
                                                                            ->where('permohonan_projek_id','=',$projectid)
                                                                            ->whereRaw('row_status=1');
                                                                })->get();
                $data['all_indikators'] = DB::table('REF_Indikatori')->select(DB::raw('*'))
                                                                    ->wherein('Sasaran_id',function($query) use ($projectid)
                                                                    {
                                                                        $query->select(DB::raw('Sasaran_id'))
                                                                                ->from('RMK_SDG_Sasaran_Indikator')
                                                                                ->where('permohonan_projek_id','=',$projectid)
                                                                                ->whereRaw('row_status=1');
                                                                    })->get();
                $data['indikatori'] = \App\Models\RmkSDGIndikator::where('permohonan_projek_id','=',$request->id)->where('row_status','=',1)->get();
                $data['activity'] = \App\Models\RmkObb::get();
            }
            
            
        //     $data['allindikators'] = DB::table('REF_Indikatori')
        //    ->whereIn('id', function ($query) {
        //        $query->where('votes', '>', 100)
        //              ->orWhere('title', '=', 'Admin');
        //    })
        //    ->get();
           
            
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
    

    public function getsasaranlist(Request $request)
    {
        try {
            if($request->id){
                // $data['rmkpage'] = \App\Models\RmkObbPage::where('permohonan_projek_id','=',$request->id)->first();
                $data['sasaran'] = \App\Models\RMKSDGSasaranIndikator::where('permohonan_projek_id','=',$request->projectid)->where('SDG_id','=',$request->id)->where('row_status','=',1)->get();
                //$data['allsdg'] = \App\Models\RmkSdg::get();
            //     $data['all_distinct_sdg'] = DB::table('RMK_SDG_Sasaran_Indikator')->select(DB::raw('DISTINCT SDG_id'))
            // ->where('permohonan_projek_id',$request->id)
            // ->get();

            }else {
                //$data['rmkpage'] = \App\Models\RmkObbPage::get();
                $data['sasaran'] = \App\Models\RMKSDGSasaranIndikator::where('permohonan_projek_id','=',$request->projectid)->where('SDG_id','=',$request->id)->where('row_status','=',1)->get();
                //$data['allsdg'] = \App\Models\RmkSdg::get();
                // $data['all_distinct_sdg'] = DB::table('RMK_SDG_Sasaran_Indikator')->select(DB::raw('DISTINCT SDG_id'))
                //                                                                     ->where('permohonan_projek_id',$request->id)
                //                                                                     ->get();
            }
            
            
        //     $data['allindikators'] = DB::table('REF_Indikatori')
        //    ->whereIn('id', function ($query) {
        //        $query->where('votes', '>', 100)
        //              ->orWhere('title', '=', 'Admin');
        //    })
        //    ->get();
           
            
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

    public function getindikatorilist(Request $request)
    {
        try {
            if($request->id){
                // $data['rmkpage'] = \App\Models\RmkObbPage::where('permohonan_projek_id','=',$request->id)->first();
                $data['indikatori'] = \App\Models\RmkSDGIndikator::where('permohonan_projek_id','=',$request->projectid)->where('SDG_id','=',$request->sdgid)->where('row_status','=',1)->get();
                //$data['allsdg'] = \App\Models\RmkSdg::get();
            //     $data['all_distinct_sdg'] = DB::table('RMK_SDG_Sasaran_Indikator')->select(DB::raw('DISTINCT SDG_id'))
            // ->where('permohonan_projek_id',$request->id)
            // ->get();

            }else {
                //$data['rmkpage'] = \App\Models\RmkObbPage::get();
                $data['indikatori'] = \App\Models\RmkSDGIndikator::where('permohonan_projek_id','=',$request->projectid)->where('SDG_id','=',$request->sdgid)->where('row_status','=',1)->get();
                //$data['allsdg'] = \App\Models\RmkSdg::get();
                // $data['all_distinct_sdg'] = DB::table('RMK_SDG_Sasaran_Indikator')->select(DB::raw('DISTINCT SDG_id'))
                //                                                                     ->where('permohonan_projek_id',$request->id)
                //                                                                     ->get();
            }
            
            
        //     $data['allindikators'] = DB::table('REF_Indikatori')
        //    ->whereIn('id', function ($query) {
        //        $query->where('votes', '>', 100)
        //              ->orWhere('title', '=', 'Admin');
        //    })
        //    ->get();
           
            
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


    public function store(Request $request){
        try{
            $data=$request->toArray();
            $project_id=$data['id'];
            $rmkobbsdg = $this->updateRMKSDG($request->all(),$request->id);
            
            if($request->sdgcomponents){
                
                foreach ($request->sdgcomponents as $sdgcomponentsitem) {  
                    $data1 = json_decode($sdgcomponentsitem, TRUE);               
                    $perundingcomponen = \App\Models\RMKSDGSasaranIndikator::create([  
                        'permohonan_projek_id' => $request->id,
                        'SDG_id' => $data1['SDG_id'],
                        'Indikatori_id' => $data1['Indikatori_id'],
                        'Sasaran_id' => $data1['Sasaran_id'],
                        'dibuat_oleh' => $request->user_id
                    ]);
                }                
            }   
            
            
            if($request->sdgindikators){
                $rmkobbpageindi = $this->updateRMKIndikator($request->all(),$request->id);
                // \App\Models\RMKSDGSasaranIndikator::where('permohonan_projek_id',$request->id)->delete();
                foreach ($request->sdgindikators as $sdgcomponentsitem) {  
                    $data1 = json_decode($sdgcomponentsitem, TRUE);               
                    $perundingcomponen = \App\Models\RmkSDGIndikator::create([  
                        'permohonan_projek_id' => $request->id,
                        'SDG_id' => $data1['SDG_id'],
                        'Indikatori_id' => $data1['Indikatori_id'],
                        'dibuat_oleh' => $request->user_id
                    ]);
                }

                
            } 


            $rmk_data = \App\Models\RmkObbPage::where('permohonan_projek_id', $request->id)->first();     
            if($rmk_data)
            {
                $rmkobbpage = $this->updateRMKData($request->all(),$request->id);
                
            }
            else{
                $rmkobbpage= new RmkObbPage;
                $rmkobbpage->permohonan_projek_id=$project_id;
                $rmkobbpage->Pemangkin_Dasar=$data['Pemangkin_Dasar'];
                $rmkobbpage->Outcome_Nasional=$data['Outcome_Nasional'];
                $rmkobbpage->Bidang_Keutamaan=$data['Bidang_Keutamaan'];
                $rmkobbpage->Bab=$data['Bab'];
                $rmkobbpage->Strategi=$data['Strategi'];
                $rmkobbpage->OBB_Program=$data['OBB_Program'];
                $rmkobbpage->OBB_Aktiviti=$data['OBB_Aktiviti'];
                $rmkobbpage->OBB_Output_Aktiviti_id=$data['OBB_Output_Aktiviti_id'];
                $rmkobbpage->SDG_id=$data['SDG_id'];
                // $rmkobbpage->Indikatori_id=$data['Indikatori'];
                // $rmkobbpage->Sasaran_id=$data['Sasaran'];
                $rmkobbpage->dibuat_oleh=$request->user_id;
                $rmkobbpage->dikemaskini_oleh=$request->user_id;
                $rmkobbpage->dibuat_pada=Carbon::now()->format('Y-m-d H:i:s');
                $rmkobbpage->dikemaskini_pada=Carbon::now()->format('Y-m-d H:i:s');
                $rmkobbpage->save();
                $section_name='RmkOBB';
                if($rmkobbpage){
                    $user_data = DB::table('users')
                               ->join('ref_jawatan','ref_jawatan.id', '=','users.jawatan_id')
                               ->select('users.*','ref_jawatan.nama_jawatan')->where('users.id',$request->user_id)->first();
                    $no_rojukan_data = DB::table('projects')->select('no_rujukan')->where('id',$request->id)->first();
                    $logData=[
                                'user_id' =>$request->user_id, 
                                'section_name'=>$section_name,   
                                'projek_id'=>$request->id,
                                'modul' => 'Permohonan Projek',
                                'user_ic_no' => $user_data->no_ic,
                                'user_jawatan' => $user_data->nama_jawatan,
                                'user_name' => $user_data->name,
                                'no_rujukan' => $no_rojukan_data-> no_rujukan,
                          ];
                    DB::connection(env('DB_CONNECTION_AUDIT'))->table('projek_log')->insert($logData);

                }
            }       

            return response()->json([
                'code' => '200',
                'status' => 'Success',
                'data' => $rmkobbpage,
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
    public function storeSDG()
    {
        try{
            $rmkobbpage = $this->updateRMKSDG($request->all(),$request->id);
            
            // \App\Models\RMKSDGSasaranIndikator::where('permohonan_projek_id',$request->id)->delete();
            if($request->sdgcomponents){
                // \App\Models\RMKSDGSasaranIndikator::where('permohonan_projek_id',$request->id)->delete();
                foreach ($request->sdgcomponents as $sdgcomponentsitem) {  
                    $data1 = json_decode($sdgcomponentsitem, TRUE);               
                    $perundingcomponen = \App\Models\RMKSDGSasaranIndikator::create([  
                        'permohonan_projek_id' => $request->id,
                        'SDG_id' => $data1['SDG_id'],
                        'Indikatori_id' => $data1['Indikatori_id'],
                        'Sasaran_id' => $data1['Sasaran_id'],
                        'dibuat_oleh' => $request->user_id
                    ]);
                }

                
            } 

            
            
            // \App\Models\RMKSDGSasaranIndikator::where('permohonan_projek_id',$request->id)->delete();
            
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

    public function RMKPageDetails($id)
    {
        try {
            $user = \App\Models\RmkObbPage::whereId($id)->first();            
            
            return response()->json([
                'code' => '200',
                'status' => 'Sucess',
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

    public function updatermk(Request $request)
    { 
        try {
                      
            $rmkpage = $this->updateRMKData($request->all(),$request->id);                
            return response()->json([
                'code' => '200',
                'status' => 'Sucess',
                'data' => $rmkpage,
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

    protected function updateRMKData(array $data, $id)
    {   try
        {
            return RmkObbPage::where('permohonan_projek_id', $id)->update([
                'Pemangkin_Dasar' => $data['Pemangkin_Dasar'],
                'Bab' => $data['Bab'],            
                'Bidang_Keutamaan' => $data['Bidang_Keutamaan'],            
                'Outcome_Nasional' => $data['Outcome_Nasional'],            
                'Strategi' => $data['Strategi'],
                'OBB_Program' => $data['OBB_Program'],
                'OBB_Aktiviti' => $data['OBB_Aktiviti'],
                'OBB_Output_Aktiviti_id' => $data['OBB_Output_Aktiviti_id'],
                'SDG_id' => $data['SDG_id'],
                // 'Indikatori_id' => $data['Indikatori'],
                // 'Sasaran_id' => $data['Sasaran'],            
                'dikemaskini_oleh' => $data['user_id'],
                'dikemaskini_pada' => Carbon::now()->format('Y-m-d H:i:s'),
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


    protected function updateRMKSDG(array $data, $id)
    {   try
        {
            return \App\Models\RMKSDGSasaranIndikator::where('permohonan_projek_id', $id)->update([
                'row_status' => 0,
                'dikemaskini_oleh' => $data['user_id'],
                'dikemaskini_pada' => Carbon::now()->format('Y-m-d H:i:s'),
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

    protected function updateRMKIndikator(array $data, $id)
    {   try
        {
            return \App\Models\RmkSDGIndikator::where('permohonan_projek_id', $id)->update([
                'row_status' => 0,
                'dikemaskini_oleh' => $data['user_id'],
                'dikemaskini_pada' => Carbon::now()->format('Y-m-d H:i:s'),
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
