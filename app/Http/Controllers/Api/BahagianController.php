<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use \App\Models\refBahagian;
use \App\Models\refKementerian;
use \App\Models\refJabatan;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Carbon;
use Jenssegers\Agent\Facades\Agent;


class BahagianController extends Controller
{
    //
    public function list(Request $request)
    {
        try {
            //code...
            if($request->has('id')){
                $data = \App\Models\refBahagian::where('jabatan_id',$request->id)->where('is_hidden','!=',1)
                                                ->where('row_status','=',1)->with(['updatedBy','jabatan'])->get();
            }else {
                if($request->has('bahagian_id')){
                    $data = \App\Models\refBahagian::whereId($request->bahagian_id)->where('is_hidden','!=',1)
                                                    ->where('row_status','=',1)->with(['updatedBy','jabatan'])->get();
                }else {
                    $data = \App\Models\refBahagian::where('is_hidden','!=',1)->where('row_status','=',1)->with(['updatedBy','jabatan'])->get();
                }
                
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

    public function listBahagian(Request $request)
    {
        try {
            //code...
            if($request->has('id')){
                $data = \App\Models\refBahagian::where('jabatan_id',$request->id)->where('row_status','=',1)->with(['updatedBy','jabatan'])->get();
            }else {
                if($request->has('bahagian_id')){
                    $data = \App\Models\refBahagian::whereId($request->bahagian_id)->where('row_status','=',1)->with(['updatedBy','jabatan'])->get();
                }else {
                    $data = \App\Models\refBahagian::where('row_status','=',1)->with(['updatedBy','jabatan'])->get();
                }
                
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

    public function listWithKementerien(Request $request)
    {
        try {
            //code...
            $data = \App\Models\refBahagian::where('kementerian_id',$request->id)->where('is_hidden','!=',1)->where('row_status','1')->get();
            
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
                'kementerian' => ['required', 'string', 'max:255'], 
                'jabatan' => ['required', 'string', 'max:255'],                
            ]);

            $kementerian = refKementerian::where('nama_kementerian', $request->kementerian)->first();
            if(!$validator->fails()) {      
                if($request->id) {                    
                    $data = refBahagian::where('id', $request->id)->update([
                        'kod_bahagian' => $request->code,
                        'nama_bahagian' => $request->name,
                        'penerangan_bahagian' => $request->description,
                        'kementerian_id' => $kementerian->id,
                        'jabatan_id' => $request->jabatan,
                        'dikemaskini_oleh' => $request->user_id,
                        'dikemaskini_pada' => Carbon::now()->format('Y-m-d H:i:s'),
                    ]);
                }else {               
                    $bahaginaName=$request->toArray();
                    $text = $bahaginaName['name'];
                    preg_match('#\((.*?)\)#', $text, $match);
                    if(!$match){
                        $acym=$request->code;
                    }
                    else{
                        $acym=$match[1];
                    }
                    // print $acym;
                    // exit();
                    $data = refBahagian::create([                 
                        'kod_bahagian' => $request->code,
                        'acym'=>$acym,
                        'row_status' => 1,
                        'nama_bahagian' => $request->name,
                        'penerangan_bahagian' => $request->description,
                        'kementerian_id' => $kementerian->id,
                        'jabatan_id' => $request->jabatan,
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
            $data = \App\Models\refBahagian::whereId($id)->with(['updatedBy','kementerian','jabatan'])->first();
            
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

    public function activate(Request $request){
        try{
                $data = refBahagian::where('id', $request->id)->update([
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
        // dd($request);
        try{
                $data = refBahagian::where('id', $request->id)->update([
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
