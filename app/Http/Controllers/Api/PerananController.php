<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use \APP\Models\MasterPeranan;
use \APP\Models\PerananModule;
use \APP\Models\UserPeranan;
use App\Models\Role;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Jenssegers\Agent\Facades\Agent;


class PerananController extends Controller
{
    public function addPeranan(Request $request)
    { 
        try { 

            $peranan = \App\Models\MasterPeranan::where('nama_peranan',$request->nama_peranan)->first();

                if($peranan) {    
                    $data=$request->toArray();
                    $peranan->penyedia= $request->peranan[0];
                    $peranan->penyemak= $request->peranan[1];
                    $peranan->penyemak_1= $request->peranan[2];
                    $peranan->penyemak_2= $request->peranan[3];
                    $peranan->pengesah= $request->peranan[4];
                    $peranan->dibuat_oleh=$request->user_id;
                    $peranan->dikemaskini_oleh=$request->user_id;
                    $peranan->dibuat_pada=Carbon::now()->format('Y-m-d H:i:s');
                    $peranan->dikemaskini_pada=Carbon::now()->format('Y-m-d H:i:s');
                    $peranan->update();

                    
                }else {                    
                    $data=$request->toArray();
                    $peranan= new MasterPeranan;
                    $peranan->nama_peranan= $request->nama_peranan;
                    $peranan->penyedia= $request->peranan[0];
                    $peranan->penyemak= $request->peranan[1];
                    $peranan->penyemak_1= $request->peranan[2];
                    $peranan->penyemak_2= $request->peranan[3];
                    $peranan->pengesah= $request->peranan[4];
                    $peranan->dibuat_oleh=$request->user_id;
                    $peranan->dikemaskini_oleh=$request->user_id;
                    $peranan->dibuat_pada=Carbon::now()->format('Y-m-d H:i:s');
                    $peranan->dikemaskini_pada=Carbon::now()->format('Y-m-d H:i:s');
                    $peranan->save();

                    // print_r($peranan['id']);exit;
                    $module= new \App\Models\PerananModule;
                    $module->peranan_id= $peranan['id'];
                    $module->module_id= 1;
                    $module->dibuat_oleh=$request->user_id;
                    $module->dikemaskini_oleh=$request->user_id;
                    $module->dibuat_pada=Carbon::now()->format('Y-m-d H:i:s');
                    $module->dikemaskini_pada=Carbon::now()->format('Y-m-d H:i:s');
                    $module->save();

                }
                return response()->json([
                    'code' => '200',
                    'status' => 'Sucess',
                    'data' => $peranan,
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

    public function getPeranan(Request $request)
    {
        try {

                $peranan = \App\Models\MasterPeranan::where('row_status',1)->get();
                //print_r($peranan);exit;
                return response()->json([
                    'code' => '200',
                    'status' => 'Success',
                    'data' => $peranan,
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


    public function addUserPeranan(Request $request)
    {
        try { 
            $peranan = \App\Models\UserPeranan::where('user_id',$request->user_id);

            if($peranan)
            {
                $peranan->delete();
                for($i=0;$i<count($request->peranan);$i++)
                {   
                        $data=$request->toArray();
                        $peranan= new UserPeranan;
                        $peranan->user_id= $request->user_id;
                        $peranan->peranan_id= $request->peranan[$i];
                        $peranan->dibuat_oleh=$request->loged_user_id;
                        $peranan->dikemaskini_oleh=$request->loged_user_id;
                        $peranan->dibuat_pada=Carbon::now()->format('Y-m-d H:i:s');
                        $peranan->dikemaskini_pada=Carbon::now()->format('Y-m-d H:i:s');
                        $peranan->save();
                }
          //----------------- registration log -----------------------------------------------------------------
                $user = \App\Models\User::where('id',$request->user_id)->with(['jawatan'])->first();
                $logged_user = \App\Models\User::where('id',$request->loged_user_id)->with(['jawatan'])->first();

                $data=[
                    'user_id'=>$request->user_id,
                    'user_ic_no'=>$user['no_ic'],
                    'user_jawatan'=>$user['jawatan']['nama_jawatan'],
                    'user_name'=>$user['name'],
                    'updated_by_user_id'=>$request->loged_user_id,
                    'updated_by_user_ic_no'=>$logged_user['no_ic'],
                    'updated_by_user_jawatan'=>$logged_user['jawatan']['nama_jawatan'],
                    'updated_by_user_name'=>$logged_user['name'],
                    'action_taken'=>'PERANAN PENGGUNA - Kemaskini',
                    'created_on'=>Carbon::now()->format('Y-m-d H:i:s'),
                    'created_at'=>Carbon::now()->format('Y-m-d H:i:s'),
                    'updated_at'=>Carbon::now()->format('Y-m-d H:i:s')
                ];
                DB::connection(env('DB_CONNECTION_AUDIT'))->table('registration_log')->insert($data);
          //---------------------ends-------------------------------------------------------------------------------


                return response()->json([
                    'code' => '200',
                    'status' => 'Sucess',
                    'data' => $peranan,
                ]);

            }
            else
            {
                return response()->json([
                    'code' => '201',
                    'status' => 'no peranan'
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

    public function addUserRole(Request $request)
    {
        try { 
            $user = \App\Models\User::where('id',$request->user_id)->with(['jawatan'])->first();
            if($request->peranan)
            {
                $user->syncRoles($request->peranan); Log::
                $permissions = collect();
                foreach ($user->roles as $role) {
                    $user->addRolePermissionsToUser($role);
                }
          //----------------- registration log -----------------------------------------------------------------
                $logged_user = \App\Models\User::where('id',$request->loged_user_id)->with(['jawatan'])->first();

                $data=[
                    'user_id'=>$request->user_id,
                    'user_ic_no'=>$user['no_ic'],
                    'user_jawatan'=>$user['jawatan']['nama_jawatan'],
                    'user_name'=>$user['name'],
                    'updated_by_user_id'=>$request->loged_user_id,
                    'updated_by_user_ic_no'=>$logged_user['no_ic'],
                    'updated_by_user_jawatan'=>$logged_user['jawatan']['nama_jawatan'],
                    'updated_by_user_name'=>$logged_user['name'],
                    'action_taken'=>'PERANAN PENGGUNA - Kemaskini',
                    'created_on'=>Carbon::now()->format('Y-m-d H:i:s'),
                    'created_at'=>Carbon::now()->format('Y-m-d H:i:s'),
                    'updated_at'=>Carbon::now()->format('Y-m-d H:i:s')
                ];
                DB::connection(env('DB_CONNECTION_AUDIT'))->table('registration_log')->insert($data);
          //---------------------ends-------------------------------------------------------------------------------


                return response()->json([
                    'code' => '200',
                    'status' => 'Sucess',
                    'data' => $user,
                ]);

            }
            else
            {
                return response()->json([
                    'code' => '201',
                    'status' => 'no peranan'
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


    public function addUserPermission(Request $request)
    {
        try { 
            $user = \App\Models\User::where('id',$request->user_id)->first();
            if($request->peranan)
            {
                $user->syncPermissions($request->peranan);
          //----------------- registration log -----------------------------------------------------------------
                $logged_user = \App\Models\User::where('id',$request->loged_user_id)->with(['jawatan'])->first();

                $data=[
                    'user_id'=>$request->user_id,
                    'user_ic_no'=>$user['no_ic'],
                    'user_jawatan'=>$user['jawatan']['nama_jawatan'],
                    'user_name'=>$user['name'],
                    'updated_by_user_id'=>$request->loged_user_id,
                    'updated_by_user_ic_no'=>$logged_user['no_ic'],
                    'updated_by_user_jawatan'=>$logged_user['jawatan']['nama_jawatan'],
                    'updated_by_user_name'=>$logged_user['name'],
                    'action_taken'=>'PERANAN PENGGUNA - Kemaskini',
                    'created_on'=>Carbon::now()->format('Y-m-d H:i:s'),
                    'created_at'=>Carbon::now()->format('Y-m-d H:i:s'),
                    'updated_at'=>Carbon::now()->format('Y-m-d H:i:s')
                ];
                DB::connection(env('DB_CONNECTION_AUDIT'))->table('registration_log')->insert($data);
          //---------------------ends-------------------------------------------------------------------------------


                return response()->json([
                    'code' => '200',
                    'status' => 'Sucess',
                    'data' => $user,
                ]);

            }
            else
            {
                return response()->json([
                    'code' => '201',
                    'status' => 'no peranan'
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

    public function deleteUserPeranan(Request $request)
    {
        try { 
                $peranan = \App\Models\UserPeranan::where('user_id',$request->user_id)
                                                  ->where('peranan_id',$request->peranan_id)
                                                  ->first();
                $peranan->row_status= 0;
                $peranan->update();

                //----------------- registration log -----------------------------------------------------------------
                          $user = \App\Models\User::where('id',$request->user_id)->with(['jawatan'])->first();
                          $logged_user = \App\Models\User::where('id',$request->loged_user_id)->with(['jawatan'])->first();
          
                          $data=[
                              'user_id'=>$request->user_id,
                              'user_ic_no'=>$user['no_ic'],
                              'user_jawatan'=>$user['jawatan']['nama_jawatan'],
                              'user_name'=>$user['name'],
                              'updated_by_user_id'=>$request->loged_user_id,
                              'updated_by_user_ic_no'=>$logged_user['no_ic'],
                              'updated_by_user_jawatan'=>$logged_user['jawatan']['nama_jawatan'],
                              'updated_by_user_name'=>$logged_user['name'],
                              'action_taken'=>'PERANAN PENGGUNA- Dibuang(peranan_id:-'.$request->peranan_id.')',
                              'created_on'=>Carbon::now()->format('Y-m-d H:i:s'),
                              'created_at'=>Carbon::now()->format('Y-m-d H:i:s'),
                              'updated_at'=>Carbon::now()->format('Y-m-d H:i:s')
                          ];
                          DB::connection(env('DB_CONNECTION_AUDIT'))->table('registration_log')->insert($data);
                //---------------------ends-------------------------------------------------------------------------------

                return response()->json([
                    'code' => '200',
                    'status' => 'Sucess',
                    'data' => $peranan,
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

    public function getUserPeanan(Request $request)
    {
        try { 
            $peranan = DB::table('user_peranan')
                        ->join('users', 'users.id', '=', 'user_peranan.user_id')
                        ->select('*')
                        ->where('user_peranan.peranan_id','=',$request->id)
                        ->where('users.row_status','=','1')
                        ->get();    

            if(count($peranan)>0)
            {
                return response()->json([
                    'code' => '200',
                    'status' => 'Sucess',
                    'data' => $peranan,
                ]);

            }
            else
            {
                $peranan = \App\Models\MasterPeranan::where('id',$request->id)
                                                  ->update(['row_status' => '0']);

                return response()->json([
                    'code' => '200',
                    'status' => 'deleted'
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

    public function getPerananData(Request $request)
    {
        try { 
            
                $peranan = \App\Models\MasterPeranan::where('id',$request->id)
                                                  ->where('row_status','=', '1')
                                                  ->first();

                return response()->json([
                                            'code' => '200',
                                            'status' => 'Sucess',
                                            'data' => $peranan,
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

    public function updatePerananData(Request $request)
    {
        try{
                $peranan = \App\Models\MasterPeranan::where('id',$request->peranan_id)->first();
                $peranan->nama_peranan = $request->nama;
                $peranan->penyedia = $request->penyedia;
                $peranan->penyemak = $request->penyemak;
                $peranan->penyemak_1 = $request->penyemak_1;
                $peranan->penyemak_2 = $request->penyemak_2;
                $peranan->pengesah = $request->pengesah;
                $peranan->dikemaskini_pada=Carbon::now()->format('Y-m-d H:i:s');
                $peranan->update();

                return response()->json([
                    'code' => '200',
                    'status' => 'Sucess',
                    'data' => $peranan,
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
