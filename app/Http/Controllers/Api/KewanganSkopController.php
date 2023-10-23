<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use \App\Models\KewanganSkop;
use \App\Models\KewanganSubSkop;
use \App\Models\GetSkopOptions;
use \App\Models\Project;
use \App\Models\projectLog;
use \App\Models\SkopProject;
use \App\Models\GetProjectSkops;
use \App\Models\GetSubSkopOptions;
use \App\Models\KewanganYuranPerunding;
use \App\Models\KewanganYuranPerundingTapak;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Jenssegers\Agent\Facades\Agent;


class KewanganSkopController extends Controller
{
    public function getprojectskop($id){
        try{
            
            $result['skopnames'] = GetSkopOptions::where('row_status','=',1)->get(); //lookupOption('skop_project');
            $result['subskopnames'] = GetSubSkopOptions::where('row_status','=',1)->get(); //lookupOption('skop_project');
            $result['yuranperunding'] = KewanganYuranPerunding::where('permohonan_projek_id','=',$id)->where('row_status','=',1)->get();
            $result['yuranperundingtapak'] = KewanganYuranPerundingTapak::where('permohonan_projek_id','=',$id)->where('row_status','=',1)->get();
            $result['skop'] = GetProjectSkops::where('project_id','=',$id)->where('row_status','=',1)->get();
            $result['skopsforkewangan'] = KewanganSkop::where('permohonan_projek_id','=',$id)->where('row_status','=',1)->get();
            $result['subskopsforkewangan'] = KewanganSubSkop::where('permohonan_projek_id','=',$id)->where('row_status','=',1)->orderby('id','ASC')->get();
    
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

    public function getskopdetails($id){
        try{
            $result['skops'] = KewanganSkop::where('permohonan_projek_id','=',$id)->where('row_status','=',1)->get();
            //$result['skopnames'] = lookupOption('skop_project');
    
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
    public function store(Request $request){

        $data=$request->toArray();
        //print_r($data);
        //$project_id=$data['Permohonan_Projek_id'];
        //$session_id = session()->getId();
        if($request->mainskopdetails){
            try{
                foreach ($request->mainskopdetails as $mainskopdetailscost) {  
                        
                    $kewangan_skop_data = \App\Models\GetProjectSkops::where('project_id', $request->id)->first();
                    if($kewangan_skop_data){
                        $data = json_decode($mainskopdetailscost, TRUE);               
                        \App\Models\GetProjectSkops::where('skop_project_code','=', $data['skop_id'])->where('project_id', $request->id)->update([   
                            
                            'cost' => $data['jumlahkos'],
                            'dikemaskini_oleh' => $request->user_id,
                            'dikemaskini_pada' => Carbon::now()->format('Y-m-d H:i:s'),
                        ]);
                    }
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

        $project_id = $request->id;
            if($request->componentstext){
                //KewanganSkop::where('permohonan_projek_id',$request->id)->delete();

                // return RMKOBBSasaran::where('permohonan_projek_id', $id)->update([
                //     'Sasaran_id' => $data['Sasaran'],
                             
                //     'dikemaskini_oleh' => $request->user_id,
                //     'dikemaskini_pada' => Carbon::now()->format('Y-m-d H:i:s'),
                // ]);


                try{
                    foreach ($request->componentstext as $componentdetails) {  
                        
                        $kewangan_data = \App\Models\KewanganSkop::where('permohonan_projek_id', $request->id)->first();
                        if($kewangan_data){
                            $data = json_decode($componentdetails, TRUE);               
                            \App\Models\KewanganSkop::where('sub_skop_project_code','=', $data['sub_skop_project_code'])->where('permohonan_projek_id', $request->id)->update([   
                                //'permohonan_projek_id' => $request->id,
                                //'skop_id' => $data['skop_id'],
                                //'sub_skop_project_code' => $data['sub_skop_project_code'],
                                //'nama_componen' => $data['nama_componen'],
                                'jumlahkos' => $data['jumlahkos'],
                                'Kuantiti' => $data['Kuantiti'],
                                'units' => $data['units'],
                                'Kos' => $data['Kos'],
                                'Catatan' => $data['Catatan'],
                                'dibuat_oleh' => $request->user_id,
                                'dikemaskini_oleh' => $request->user_id,
                                'dikemaskini_pada' => Carbon::now()->format('Y-m-d H:i:s'),
                            ]);
                        }
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

            try{
                $kewangansub_subskop = $this->updateKewanganSub_subskop($request->all(),$request->id);
                if($request->componentssubtext){
                    // \App\Models\KewanganSubSkop::where('permohonan_projek_id',$request->id)->delete();
                    
                    foreach ($request->componentssubtext as $componentdetails1) {  
                        $data = json_decode($componentdetails1, TRUE);               
                        $skopcomponen = \App\Models\KewanganSubSkop::create([   
                            'permohonan_projek_id' => $request->id,
                            'skop_id' => $data['skop_id'],
                            'sub_skop_id' => $data['sub_skop_id'],
                            'nama_componen' => $data['nama_componen'],
                            'jumlahkos' => $data['jumlahkos'],
                            'Kuantiti' => $data['Kuantiti'],
                            'units' => $data['units'],
                            'Kos' => $data['Kos'],
                            'Catatan' => $data['Catatan'],
                            'dibuat_oleh' => $request->user_id
                        ]);
                        
                    }
                    // return response()->json([
                    //     'code' => '200',
                    //     'status' => 'Success',
                    //     'data' => $data,
                    // ]);
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
                
            
                try{
                    //KewanganYuranPerunding::where('permohonan_projek_id',$request->id)->delete();
                    $kewangankajian = $this->updateKewangan_yurankajian($request->all(),$request->id);
                    if($request->perundingtext){
                        //KewanganYuranPerunding::where('permohonan_projek_id',$request->id)->delete();
                        
                        foreach ($request->perundingtext as $perundingdetails) {  
                            $data = json_decode($perundingdetails, TRUE);               
                            $perundingcomponen = KewanganYuranPerunding::create([  
                                'is_Profesional' => $data['is_Profesional'],
                                'permohonan_projek_id' => $request->id,
                                'man_month' => $data['man_month'],
                                'jawatan' => $data['jawatan'],
                                'jumlahkos' => $data['jumlahkos'],
                                'multiplier' => $data['multiplier'],
                                'salary' => $data['salary'],
                                'amount' => $data['amount'],
                                'catatan' => $data['catatan'],
                                'dibuat_oleh' => $request->user_id
                            ]);
                        }

                        
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
            

            //KewanganYuranPerundingTapak::where('permohonan_projek_id',$request->id)->delete();
            $kewangantapak = $this->updateKewangan_yurantapak($request->all(),$request->id);
            if($request->perundingtexttapak){
                //KewanganYuranPerundingTapak::where('permohonan_projek_id',$request->id)->delete();
                
                foreach ($request->perundingtexttapak as $perundingdetails) {  
                    $data = json_decode($perundingdetails, TRUE);               
                    $perundingcomponen = KewanganYuranPerundingTapak::create([  
                        'is_Profesional' => $data['is_Profesional'],
                        'permohonan_projek_id' => $request->id,
                        'man_month' => $data['man_month'],
                        'jawatan' => $data['jawatan'],
                        'jumlahkos' => $data['jumlahkos'],
                        'multiplier' => $data['multiplier'],
                        'salary' => $data['salary'],
                        'amount' => $data['amount'],
                        'catatan' => $data['catatan'],
                        'dibuat_oleh' => $request->user_id
                    ]);
                }

                
            } 
        $section_name='Kewangan';
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
           

            return response()->json([
                'code' => '200',
                'status' => 'Success',
                'data' => json_decode($request->componentstext[0], TRUE),
            ]);

    }


    protected function updateKewanganSub_subskop(array $data, $id)
    {   try
        {
            return \App\Models\KewanganSubSkop::where('permohonan_projek_id', $id)->update([
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
    protected function updateKewangan_yurankajian(array $data, $id)
    {   try
        {
            return \App\Models\KewanganYuranPerunding::where('permohonan_projek_id', $id)->update([
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

    protected function updateKewangan_yurantapak(array $data, $id)
    {   try
        {
            return \App\Models\KewanganYuranPerundingTapak::where('permohonan_projek_id', $id)->update([
                'row_status' => 0,
                'dikemaskini_oleh' => $data->user_id,
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