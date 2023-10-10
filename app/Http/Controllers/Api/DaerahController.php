<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use \App\Models\refDaerah;
use \App\Models\refMukim;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Carbon;
use Jenssegers\Agent\Facades\Agent;


class DaerahController extends Controller
{
    //
    public function list(Request $request)
    {
        try {
            //code...            
            if($request->has('id')){
                $data = \App\Models\refDaerah::where('negeri_id',$request->id)->where('is_hidden','!=',1)->where('row_status','=',1)->with(['updatedBy','negeri','mukim'])->get();
            }else {
                if($request->has('daerah_id')){
                    $data = \App\Models\refDaerah::whereId($request->daerah_id)->where('is_hidden','!=',1)->where('row_status','=',1)->with(['updatedBy','negeri','mukim'])->get();
                }else {
                    $data = \App\Models\refDaerah::where('is_hidden','!=',1)->where('row_status','=',1)->with(['updatedBy','negeri','mukim'])->get();
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

    public function listDaerah(Request $request)
    {
        try {
            //code...            
            if($request->has('id')){
                $data = \App\Models\refDaerah::where('negeri_id',$request->id)->where('row_status','=',1)->with(['updatedBy','negeri','mukim'])->get();
            }else {
                if($request->has('daerah_id')){
                    $data = \App\Models\refDaerah::whereId($request->daerah_id)->where('row_status','=',1)->with(['updatedBy','negeri','mukim'])->get();
                }else {
                    $data = \App\Models\refDaerah::where('row_status','=',1)->with(['updatedBy','negeri','mukim'])->get();
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

    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(),[
                'code' => ['required', 'string', 'max:255'],  
                'name' => ['required', 'string', 'max:255'],
                'negeri' => ['required', 'string', 'max:255'],                
            ]);

            if(!$validator->fails()) {   
                if($request->id) {                    
                    $data = refDaerah::where('id', $request->id)->update([
                        'kod_daerah' => $request->code,
                        'kod_negeri' => $request->code_negeri,
                        'nama_daerah' => $request->name,
                        'penerangan_daerah' => $request->description,
                        'negeri_id' => $request->negeri,
                        'dikemaskini_oleh' => $request->user_id,
                        'dikemaskini_pada' => Carbon::now()->format('Y-m-d H:i:s'),
                    ]);
                }else {                               
                    $data = refDaerah::create([                    
                            'kod_daerah' => $request->code,
                            'kod_negeri' => $request->code_negeri,
                            'row_status' => 1,
                            'nama_daerah' => $request->name,
                            'penerangan_daerah' => $request->description,
                            'negeri_id' => $request->negeri,
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
            $data = \App\Models\refDaerah::whereId($id)->with('updatedBy')->first();
            
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
            $data = refDaerah::where('id', $request->id)->update([
                'is_hidden' => $request->value,
                'dikemaskini_oleh' => $request->loged_user_id,
                'dikemaskini_pada' => Carbon::now()->format('Y-m-d H:i:s'),
            ]);

            $data = refMukim::where('daerah_id', $request->id)->update([
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
                // dd($request);
                $data = refDaerah::where('id', $request->id)->update([
                    'is_hidden' => $request->value,
                    'dikemaskini_oleh' => $request->loged_user_id,
                    'dikemaskini_pada' => Carbon::now()->format('Y-m-d H:i:s'),
                ]);

                $data = refMukim::where('daerah_id', $request->id)->update([
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
