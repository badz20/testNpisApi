<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use \App\Models\Pentadbir_modules;
use \App\Models\PSDA_model;
use \App\Models\User;
use \App\Models\MasterUsertype;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
use \App\Models\ModuleLinkMaster;
use Illuminate\Support\Facades\Log;
use App\Models\Role;
use Jenssegers\Agent\Facades\Agent;



class Pentadbir_Modules_Controller extends Controller
{
    public function modulelist(){
        try{
                $data= Pentadbir_modules::all();
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

    public function listMasterlinks(Request $request)
    {
        try{
                $data['moduleLinks']= ModuleLinkMaster::all();
                $role = Role::where('name',$request->role)->first();
                $data['permissions']= $role->getPermissionNames()->toArray();
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

    public function Masterlinks(Request $request)
    {
        try{
                $data['moduleLinks']= ModuleLinkMaster::all();
                $user = User::whereId($request->user_id)->first();
                $data['permissions']= $user->getPermissionNames()->toArray();
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

    public function getMasterDetails(Request $request,$id)
    {
        try{
                $data['modules']= Pentadbir_modules::where('id',$id)->first();
                $data['userTypes'] = MasterUsertype::where('module_id',$id)->where('row_status',1)->get();

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

    public function addMasterDetails(Request $request)
    {
        try{
                    // MasterUsertype::where('module_id',$request->id)->delete();
                    MasterUsertype::where('module_id', $request->id)->update(['row_status' => 0]);

                    $types= explode(',',$request->userTypes); //print_r($abc);exit;
                    foreach($types as $type)
                    {
                        $data = MasterUsertype::create([       
                            'module_id' => $request->id,
                            'user_type' => $type,
                            'dibuat_pada' => Carbon::now()->format('Y-m-d H:i:s'),
                            'dibuat_oleh' => $request->user_id,
                            'dikemaskini_oleh' => $request->user_id,
                            'dikemaskini_pada' => Carbon::now()->format('Y-m-d H:i:s'),
                            'row_status' => 1,
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

    public function activate(Request $request){
        try{
                $data = Pentadbir_modules::where('id', $request->id)->update([
                        'row_status' => 1,
                        'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
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
                $data = Pentadbir_modules::where('id', $request->id)->update([
                        'row_status' => 0,
                        'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
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

    public function getModuleAccessByUsertype(Request $request)
    {
        try{
                $data = MasterUsertype::where('user_type',$request->user_type)->where('row_status',1)->get();

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
    
}
