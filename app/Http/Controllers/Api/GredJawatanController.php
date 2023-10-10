<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use \App\Models\refGredJawatan;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Carbon;
use Jenssegers\Agent\Facades\Agent;


class GredJawatanController extends Controller
{
    
    public function list(Request $request)
    {
        try {
            //code...
            if($request->has('id')){
                
                $data = \App\Models\refGredJawatan::where('id',$request->id)->where('is_hidden','!=',1)->where('row_status',1)->with('updatedBy')->get();
            }else {
                $data = \App\Models\refGredJawatan::where('is_hidden','!=',1)->where('row_status',1)->with('updatedBy')->get();
                $data2 = \App\Models\refNegeri::where('is_hidden','!=',1)->where('row_status',1)->with('updatedBy')->get();
                $data3 = \App\Models\refJawatan::where('is_hidden','!=',1)->where('row_status',1)->with('updatedBy')->get();
                $data4 = \App\Models\PejabatProjek::where('IsActive',1)->where('row_status',1)->get();
            }
            //$data = \App\Models\refGredJawatan::get();
            return response()->json([
                'code' => '200',
                'status' => 'Success',
                'data' => $data,
                'Negeridata' => $data2,
                'Jawatandata' => $data3,
                'Pejabatdata' => $data4,
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

    public function listGred(Request $request)
    {
        try {
            //code...
            if($request->has('id')){
                
                $data = \App\Models\refGredJawatan::where('id',$request->id)->where('row_status',1)->with('updatedBy')->get();
            }else {
                $data = \App\Models\refGredJawatan::where('row_status',1)->with('updatedBy')->get();
                $data2 = \App\Models\refNegeri::where('row_status',1)->with('updatedBy')->get();
                $data3 = \App\Models\refJawatan::where('row_status',1)->with('updatedBy')->get();
                $data4 = \App\Models\PejabatProjek::where('row_status',1)->get();
            }
            //$data = \App\Models\refGredJawatan::get();
            return response()->json([
                'code' => '200',
                'status' => 'Success',
                'data' => $data,
                'Negeridata' => $data2,
                'Jawatandata' => $data3,
                'Pejabatdata' => $data4,
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
    

    // public function masterData(Request $request)
    // {
    //     try {
    //         //code...
    //         if($request->has('id')){
                
    //             $data = \App\Models\refGredJawatan::where('id',$request->id)->with('updatedBy')->get();
    //         }else {
    //             $data = \App\Models\refGredJawatan::with('updatedBy')->get();
    //             $data2 = \App\Models\refNegeri::with('updatedBy')->get();
    //             $data3 = \App\Models\refJawatan::with('updatedBy')->get();
    //             $data4 = \App\Models\PejabatProjek::get();
    //         }
    //         //$data = \App\Models\refGredJawatan::get();
    //         return response()->json([
    //             'code' => '200',
    //             'status' => 'Success',
    //             'data' => $data,
    //             'Negeridata' => $data2,
    //             'Jawatandata' => $data3,
    //             'Pejabatdata' => $data4,
    //         ]);

    //     } catch (\Throwable $th) {
    //         logger()->error($th->getMessage());

    //         return response()->json([
    //             'code' => '500',
    //             'status' => 'Failed',
    //             'error' => $th,
    //         ]);
    //     }
    // }


    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(),[
                'code' => ['required', 'string', 'max:255'],  
                'name' => ['required', 'string', 'max:255'],                
            ]);

            if(!$validator->fails()) {      
                if($request->id) {
                    $data = refGredJawatan::where('id', $request->id)->update([
                        'kod_gred' => $request->code,
                        'nama_gred' => $request->name,
                        'penerangan_gred' => $request->description,
                        'dikemaskini_oleh' => $request->user_id,
                        'dikemaskini_pada' => Carbon::now()->format('Y-m-d H:i:s'),
                    ]);
                }else {               
                $data = refGredJawatan::create([                    
                        'kod_gred' => $request->code,
                        'row_status' => 1,
                        'nama_gred' => $request->name,
                        'penerangan_gred' => $request->description,
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
            $data = \App\Models\refGredJawatan::whereId($id)->with('updatedBy')->first();
            
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
            $data = refGredJawatan::where('id', $request->id)->update([
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
            $data = refGredJawatan::where('id', $request->id)->update([
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
