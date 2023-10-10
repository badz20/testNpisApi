<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use \App\Models\projectLog;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Carbon;
use \App\Models\LookupOption;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Jenssegers\Agent\Facades\Agent;

class LookupOptionsController extends Controller
{
    //
    public function list(Request $request)
    {
        try {
            //code...            
            // if($request->has('id')){
            //     $data = \App\Models\SubSkopOption::where('skop_id',$request->id)->where('is_hidden','!=',1)->where('row_status','=',1)->with(['updatedBy','skop'])->get();
            // }else {
            //     if($request->has('sub_skop_id')){
            //         $data = \App\Models\SubSkopOption::whereId($request->daerah_id)->where('is_hidden','!=',1)->where('row_status','=',1)->with(['updatedBy','skop'])->get();
            //     }else {
                        $data = \App\Models\LookupOption::where('row_status','=',1)->with(['updatedBy'])
                                ->orderBy('key','asc')
                                ->get();
            //     }
                
            // }
            
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

    public function listKey(Request $request)
    {
        try {
            //code...            
            // if($request->has('id')){
            //     $data = \App\Models\SubSkopOption::where('skop_id',$request->id)->where('row_status','=',1)->with(['updatedBy','skop'])->get();
            // }else {
            //     if($request->has('sub_skop_id')){
            //         $data = \App\Models\SubSkopOption::whereId($request->sub_skop_id)->where('row_status','=',1)->with(['updatedBy','skop'])->get();
            //     }else {
                    $data = \App\Models\LookupOption::where('row_status','=',1)->distinct()->get(['key']);
            //     }
                
            // }
            
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
                'key' => ['required', 'string', 'max:255'],  
                'value' => ['required', 'string', 'max:255'],
                'code' => ['required', 'string', 'max:255'],                
            ]);

            if(!$validator->fails()) {   
                if($request->id) {                    
                    $data = LookupOption::where('id', $request->id)->update([
                        'key' => $request->key,
                        'value' => $request->value,
                        'code' => $request->code,
                        'order_by' => $request->order_no,
                        'catatan' => $request->description,
                        'dikemaskini_oleh' => $request->user_id,
                        'dikemaskini_pada' => Carbon::now()->format('Y-m-d H:i:s'),
                    ]);
                }else {                               
                    $data = LookupOption::create([ 
                            'uuid' => Str::uuid(),                    
                            'key' => $request->key,
                            'row_status' => 1,
                            'value' => $request->value,
                            'code' => $request->code,
                            'order_by' => $request->order_no,
                            'catatan' => $request->description,
                            'is_hidden' => 0,
                            'json_value' => '{}',
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
            $data = \App\Models\LookupOption::whereId($id)->with('updatedBy')->first();
            
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
                $data = LookupOption::where('id', $request->id)->update([
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
                $data = LookupOption::where('id', $request->id)->update([
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
