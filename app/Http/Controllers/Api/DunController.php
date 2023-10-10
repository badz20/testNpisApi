<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use \App\Models\refParlimen;
use \App\Models\refDun;
use \App\Models\refNegeri;
use \App\Models\refDaerah;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Carbon;
use Jenssegers\Agent\Facades\Agent;


class DunController extends Controller
{
    //
    public function list(Request $request)
    {
        try {
            //code...            
            if($request->has('id')){
                $data = \App\Models\refDun::where('parlimen_id',$request->id)->where('is_hidden','!=',1)->where('row_status','=',1)->with(with(['updatedBy','negeri','parlimen']))->get();
            }else {
                $data = \App\Models\refDun::where('is_hidden','!=',1)->where('row_status','=',1)->with(with(['updatedBy','negeri','parlimen']))->get();
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

    public function listDun(Request $request)
    {
        try {
            //code...            
            if($request->has('id')){
                $data = \App\Models\refDun::where('parlimen_id',$request->id)->where('row_status','=',1)->with(with(['updatedBy','negeri','parlimen']))->get();
            }else {
                $data = \App\Models\refDun::where('row_status','=',1)->with(with(['updatedBy','negeri','parlimen']))->get();
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
                'negeri' => ['required', 'string', 'max:255'],                 
                'parlimen' => ['required', 'string', 'max:255'],              
            ]);

            $negeri = refNegeri::where('nama_negeri',$request->negeri)->first();            
            $parlimen = refParlimen::where('nama_parlimen',$request->parlimen)->first();
            if(!$validator->fails()) {   
                if($request->id) {                    
                    $data = refDun::where('id', $request->id)->update([
                        'kod_dun' => $request->code,
                        'nama_dun' => $request->name,
                        'penerangan_dun' => $request->description,
                        'negeri_id' => $negeri->id,                        
                        'dikemaskini_oleh' => $request->user_id,
                        'negeri_id' => $negeri->id,                        
                        'parlimen_id' => $request->parlimen,
                        'dikemaskini_pada' => Carbon::now()->format('Y-m-d H:i:s'),
                    ]);
                }else {                
                    $data = refDun::create([                    
                            'kod_dun' => $request->code,
                            'row_status' => 1,
                            'nama_dun' => $request->name,
                            'penerangan_dun' => $request->description,
                            'negeri_id' => $negeri->id,                            
                            'parlimen_id' => $request->parlimen,
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
            $data = \App\Models\refDun::whereId($id)->with(['updatedBy','negeri','parlimen'])->first();
            
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
            $data = refDun::where('id', $request->id)->update([
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
                $data = refDun::where('id', $request->id)->update([
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
