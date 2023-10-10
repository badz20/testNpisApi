<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use \App\Models\refBahagian;
use \App\Models\refKementerian;
use \App\Models\refJabatan;
use \App\Models\Project;
use \App\Models\projectLog;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Jenssegers\Agent\Facades\Agent;


class KementerianController extends Controller
{
    //
    public function list(Request $request)
    {
        try {
            //code...
            if($request->has('id')){
                $data = \App\Models\refKementerian::where('id',$request->id)->where('row_status','=',1)->with('updatedBy')->get();
            }else {
                $data = \App\Models\refKementerian::where('row_status','=',1)->with('updatedBy')->get();
            }
            
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

    public function listwithKementerian(Request $request)
    {
        try {
            //code...
            if($request->has('id')){
                $data = \App\Models\refKementerian::where('id',$request->id)->where('is_hidden','!=',1)->where('row_status','=',1)->with('updatedBy')->get();
            }else {
                $data = \App\Models\refKementerian::where('is_hidden','!=',1)->where('row_status','=',1)->with('updatedBy')->get();
            }
            
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

    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(),[
                'code' => ['required', 'string', 'max:255'],  
                'name' => ['required', 'string', 'max:255'],                
            ]);

            if(!$validator->fails()) {    
                if($request->id) {
                    $data = refKementerian::where('id', $request->id)->update([
                        'kod_kementerian' => $request->code,
                        'nama_kementerian' => $request->name,
                        'penerangan_kementerian' => $request->description,
                        'dikemaskini_oleh' => $request->user_id,
                        'dikemaskini_pada' => Carbon::now()->format('Y-m-d H:i:s'),
                    ]);
                }else {             
                    $data = refKementerian::create([                    
                            'kod_kementerian' => $request->code,
                            'row_status' => 1,
                            'nama_kementerian' => $request->name,
                            'penerangan_kementerian' => $request->description,
                            'is_hidden' => 0,                    
                            'created_at' =>  Carbon::now()->format('Y-m-d H:i:s'),            
                            'dibuat_pada' => Carbon::now()->format('Y-m-d H:i:s'),
                            'dibuat_oleh' => $request->user_id,
                            'dikemaskini_oleh' => $request->user_id,
                            'dikemaskini_pada' => Carbon::now()->format('Y-m-d H:i:s'),
                        ]);
                    }
                return response()->json([
                    'code' => '200',
                    'status' => 'Sucess',
                    'data' => $data,
                ]);
            }else {                
                return response()->json([
                    'code' => '422',
                    'status' => 'Unprocessable Entity',
                    'data' => $validator->errors(),
                ]);
            }
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

    public function edit($id)
    {
        try {
            //code...
            $data = \App\Models\refKementerian::whereId($id)->with('updatedBy')->first();
            
            //$data = refNegeri::with('updatedBy')->get();
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

    public function listByname(Request $request)
    {
        try {
            //code...
            $kementerian = \App\Models\refKementerian::where('nama_kementerian','Kementerian Sumber Asli, Alam Sekitar dan Perubahan Iklim (NRECC)')->with('updatedBy')->get();
            $data['jabatan'] = \App\Models\refJabatan::where('nama_jabatan','Jabatan Pengairan dan Saliran (JPS)')->with('updatedBy')->get();

            if($kementerian[0]['id'])
            {
                $data['bahagian'] = \App\Models\refBahagian::where('kementerian_id',$kementerian[0]['id'])->where('is_hidden','!=',1)->where('row_status','1')->get();

            }
            else
            {
                $data['bahagian'] ='';
            }
            $data['kementerian']=$kementerian;
            
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

    public function listwithKementerianId(Request $request)
    {
        try {
            //code...
            $kementerian = \App\Models\refKementerian::where('is_hidden','!=',1)->where('row_status','1')->get();


            if($request->id)
            {
                $data['bahagian'] = \App\Models\refBahagian::where('kementerian_id',$request->id)->where('is_hidden','!=',1)
                                                            ->where('row_status','1')->get();
                $data['jabatan'] = \App\Models\refJabatan::where('kementerian_id',$request->id)->where('is_hidden','!=',1)
                                                            ->with('updatedBy')->get();
            }
            else
            {
                $data['bahagian'] ='';
                $data['jabatan'] ='';
            }
            if($request->jabatan!='null')
            {
                $data['jaba_bahagian'] = \App\Models\refBahagian::where('jabatan_id',$request->jabatan)
                                                                ->where('is_hidden','!=',1)->where('row_status','1')->get();
            }
            else
            {
                $data['jaba_bahagian'] = '';
            }
            $data['kementerian']=$kementerian;
            
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

    public function KementerianController(){

        try{
                    $kementerian =refKementerian::where('is_hidden','!=',1)->where('row_status','1')->get();
                    return response()->json([
                        'code' => '200',
                        'status' => 'Success',
                        'data' => $kementerian,
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

    public function kod_kementerian(Request $request){
        try{
                $data=$request->toArray();
                $projectData =Project::where('kod_baharu',$data["modul_id"])->get();
                return response()->json([
                    'code' => '200',
                    'status' => 'Success',
                    'data' => $projectData,
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

    public function updateKementerian(Request $request){
        $checkDuplicate=refKementerian::all();
        $find=$checkDuplicate->toArray();
        // print_r($find);
        $flag=0;
        for($j=0;$j<count($find);$j++){
            // print_r($find[$j]["kod_kementerian"]);
            if($request->kod_baharu==$find[$j]["kod_kementerian"]){
                // return response()->json([
                //     'code' => '2601',
                //     'status' => 'Duplicate',
                //     'error' => $th,                    
                // ]);
                $flag=1;
                break;
            }
        }   
            if($flag==0)
            {
                try {
                    $logData=Project::select('id')->where('kod_baharu', $request->kod_asal)->get();
                    $log_id=$logData->toArray();
                    $section_name="Selenggara_Kod_Projek";
                   
                    $data = refKementerian::where('kod_kementerian', $request->kod_asal)->update([
                        'kod_kementerian' => $request->kod_baharu,
                        'dikemaskini_oleh' => $request->user_id,
                        'dikemaskini_pada' => Carbon::now()->format('Y-m-d H:i:s'),
                    ]);
                    if($data==true && $logData==true){
                        $data1 = Project::where('kod_baharu', $request->kod_asal)->update([
                            // 'kod_asal', $request->kod_asal,
                            'kod_baharu' => $request->kod_baharu,
                            'dikemaskini_oleh' => $request->user_id,
                            'dikemaskini_pada' => Carbon::now()->format('Y-m-d H:i:s'),
                        ]);

                        $user_data = DB::table('users')
                                ->join('ref_jawatan','ref_jawatan.id', '=','users.jawatan_id')
                                ->select('users.*','ref_jawatan.nama_jawatan')->where('users.id',$request->user_id)->first();

                        for($i=0;$i<count($log_id);$i++){
                            $no_rojukan_data = DB::table('projects')->select('no_rujukan')->where('id',$log_id[$i]["id"])->first();

                            $logData=[
                                'user_id' =>$request->user_id, 
                                'section_name'=>$section_name,   
                                'projek_id'=>$log_id[$i]["id"],
                                'modul' => 'Permohonan Projek',
                                'user_ic_no' => $user_data->no_ic,
                                'user_jawatan' => $user_data->nama_jawatan,
                                'user_name' => $user_data->name,
                                'no_rujukan' => $no_rojukan_data-> no_rujukan,
                            ];
                            DB::connection(env('DB_CONNECTION_AUDIT'))->table('projek_log')->insert($logData);

                        }
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
            else{
                return response()->json([
                    'code' => '2601',
                    'status' => 'Duplicate',                                        
                ]);
            }
            
        }

    public function dataKementerian(Request $request){
        // print_r($request->id);
        // exit();
        // $data=$request->toArray();
        try{
                $projectData =refKementerian::where('id',$request->id)->get();
                return response()->json([
                    'code' => '200',
                    'status' => 'Success',
                    'data' => $projectData,
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

    public function activate(Request $request){
        try{
                $data = refKementerian::where('id', $request->id)->update([
                    'is_hidden' => $request->value,
                    'dikemaskini_oleh' => $request->loged_user_id,
                    'dikemaskini_pada' => Carbon::now()->format('Y-m-d H:i:s'),
                ]);

                $data = refJabatan::where('kementerian_id', $request->id)->update([
                    'is_hidden' => $request->value,
                    'dikemaskini_oleh' => $request->loged_user_id,
                    'dikemaskini_pada' => Carbon::now()->format('Y-m-d H:i:s'),
                ]);

                $data = refBahagian::where('kementerian_id', $request->id)->update([
                    'is_hidden' => $request->value,
                    'dikemaskini_oleh' => $request->loged_user_id,
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
    public function deactivate(Request $request){
        try{
                $data = refKementerian::where('id', $request->id)->update([
                    'is_hidden' => $request->value,
                    'dikemaskini_oleh' => $request->loged_user_id,
                    'dikemaskini_pada' => Carbon::now()->format('Y-m-d H:i:s'),
                ]);

                $data = refJabatan::where('kementerian_id', $request->id)->update([
                    'is_hidden' => $request->value,
                    'dikemaskini_oleh' => $request->loged_user_id,
                    'dikemaskini_pada' => Carbon::now()->format('Y-m-d H:i:s'),
                ]);

                $data = refBahagian::where('kementerian_id', $request->id)->update([
                    'is_hidden' => $request->value,
                    'dikemaskini_oleh' => $request->loged_user_id,
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
