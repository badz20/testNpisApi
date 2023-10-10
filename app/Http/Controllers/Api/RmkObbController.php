<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use \App\Models\RmkObb;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Jenssegers\Agent\Facades\Agent;


class RmkObbController extends Controller
{
    public function list(Request $request)
    {
        
        
        try {
            if($request->id){
                
                $data = \App\Models\RmkObb::where('id','=',$request->id)->first();
            }else {
                
                // var_dump('Hi');
                $data = \App\Models\RmkObb::get();
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


    public function obbmasterlist(){
        // $data=RmkObb::all();
        try{
                $data=RmkObb::with(['user'=> function ($query) {
                    $query->select('id', 'name');
                }])->get();
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
        // print_r($request->all());
        try{
                $data=$request->toArray();
                $Rmk_obb= new RmkObb;
                $Rmk_obb->nama_aktivity=$data['output'];
                $Rmk_obb->obb_program=$data['program'];
                $Rmk_obb->obb_aktiviti=$data['aktiviti'];
                $Rmk_obb->kod_aktivity=$data['kod'];
                $Rmk_obb->catatan=$data['catatan'];
                $Rmk_obb->dibuat_oleh=$request->user_id;
                $Rmk_obb->dikemaskini_oleh=$request->user_id;
                $Rmk_obb->dibuat_pada=Carbon::now()->format('Y-m-d H:i:s');
                $Rmk_obb->dikemaskini_pada=Carbon::now()->format('Y-m-d H:i:s');
                $Rmk_obb->save();
                if($Rmk_obb->save()=='true'){
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

    public function edit($id){

        try{
                // print_r($id);
                $data=RmkObb::where('id','=',$id)->get();
                return response()->json([
                    'code' => '200',
                    'status' => 'Success',
                    'data'=>$data
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

      public function update(Request $request){

        try{
                $data=$request->toArray();
                // print_r($data);
                $Rmk_obb = RmkObb::where('id',$data['update_id'])->first();
                $Rmk_obb->obb_aktiviti=$data['output'];
                $Rmk_obb->obb_program=$data['program'];
                $Rmk_obb->nama_aktivity=$data['aktiviti'];
                $Rmk_obb->kod_aktivity=$data['kod'];
                $Rmk_obb->catatan=$data['catatan'];
                $Rmk_obb->dibuat_oleh=$request->user_id;
                $Rmk_obb->dikemaskini_oleh=$request->user_id;
                $Rmk_obb->dibuat_pada=Carbon::now()->format('Y-m-d H:i:s');
                $Rmk_obb->dikemaskini_pada=Carbon::now()->format('Y-m-d H:i:s');
                $Rmk_obb->save();
                if($Rmk_obb->save()=='true'){
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

  public function activate(Request $request){

    try{
            $data=$request->toArray();
                // print_r($data);
                $activate_id=$data['id'];
                $value=$data['value'];
                $activate_modules=RmkObb::find($activate_id);
                $activate_modules->dibuat_oleh=$request->user_id;
                $activate_modules->dikemaskini_oleh=$request->user_id;
                $activate_modules->dibuat_pada=Carbon::now()->format('Y-m-d H:i:s');
                $activate_modules->dikemaskini_pada=Carbon::now()->format('Y-m-d H:i:s');
                $activate_modules->dikemaskini_pada=Carbon::now()->format('Y-m-d H:i:s'); 
                $activate_modules->row_status=$value;        
                $activate_modules->save();
                if($activate_modules->save()=='true'){
                    return response()->json([
                        'code' => '200',
                        'status' => 'Success',
                        'data' => $data,
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

  public function deactivate(Request $request){

        try{
            $data=$request->toArray();
                // print_r($data);
                $activate_id=$data['id'];
                $value=$data['value'];
                $deactivate_modules=RmkObb::find($activate_id);
                $deactivate_modules->dibuat_oleh=$request->user_id;
                $deactivate_modules->dikemaskini_oleh=$request->user_id;
                $deactivate_modules->dibuat_pada=Carbon::now()->format('Y-m-d H:i:s');
                $deactivate_modules->dikemaskini_pada=Carbon::now()->format('Y-m-d H:i:s');
                $deactivate_modules->dikemaskini_pada=Carbon::now()->format('Y-m-d H:i:s'); 
                $deactivate_modules->row_status=$value;        
                $deactivate_modules->save();
                if($deactivate_modules->save()=='true'){
                    return response()->json([
                        'code' => '200',
                        'status' => 'Success',
                        'data' => $data,
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
    
    

}
