<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use \App\Models\Pentadbir_Data_Services;
use \App\Models\Pentadbir_modules;
use \App\Models\PSDA_model;
use \App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
use Jenssegers\Agent\Facades\Agent;


class PSDA_Controller extends Controller
{
    
    public function list(){

        try{
                $data= PSDA_model::with(['Module'=> function ($query) {
                    $query->select('id', 'modul_name');
                }])->where('status_id','=',1)->orderBy('id','desc')->get();
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
                $module_id=$data['module_id'];
                $pautan=$data['pautan'];
                $keterangan=$data['keterangan'];
                $session_id = session()->getId();
                $Pentadbir_modules= new PSDA_model;
                $Pentadbir_modules->modul_id=$module_id;
                $Pentadbir_modules->keterangan=$keterangan;
                $Pentadbir_modules->pautan=$pautan;
                $Pentadbir_modules->dibuat_oleh=$request->user_id;
                $Pentadbir_modules->dikemaskini_oleh=$request->user_id;
                $Pentadbir_modules->dibuat_pada=Carbon::now()->format('Y-m-d H:i:s');
                $Pentadbir_modules->dikemaskini_pada=Carbon::now()->format('Y-m-d H:i:s');
                $Pentadbir_modules->save();
                if($Pentadbir_modules->save()=='true'){
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

    public function edit(Request $request){
        try{
            $data=$request->toArray();
            $modul_id=$data['modul_id'];
            $modul_list=Pentadbir_modules::all();
            $modulData=compact('modul_list');
            $data=PSDA_model::where('id','=',$modul_id)->with(['Module'=> function ($query) {
                $query->select('id', 'modul_name');
            }])->get();
            return response()->json([
                'code' => '200',
                'status' => 'Success',
                'data' => [$data,$modulData],
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
                $update_id=$data['update_id'];
                $module_id=$data['module_id'];
                $pautan=$data['pautan'];
                $keterangan=$data['keterangan'];
                $session_id = session()->getId();
                $updatePentadbir_modules=PSDA_model::find($update_id);
                $updatePentadbir_modules->modul_id=$module_id;
                $updatePentadbir_modules->keterangan=$keterangan;
                $updatePentadbir_modules->pautan=$pautan;
                $updatePentadbir_modules->dibuat_oleh=$request->user_id;
                $updatePentadbir_modules->dikemaskini_oleh=$request->user_id;
                $updatePentadbir_modules->dibuat_pada=Carbon::now()->format('Y-m-d H:i:s');
                $updatePentadbir_modules->dikemaskini_pada=Carbon::now()->format('Y-m-d H:i:s');
                $updatePentadbir_modules->save();
                if($updatePentadbir_modules->save()=='true'){
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
    public function activate(Request $request){

        try{
                $data=$request->toArray();
                // print_r($data);
                $activate_id=$data['id'];
                $value=$data['value'];
                $activate_modules=PSDA_model::find($activate_id);
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
            $deactivate_modules=PSDA_model::find($activate_id);
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
