<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\Permission;
use App\Models\UserTypeRole;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Jenssegers\Agent\Facades\Agent;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        try { 
            $data = Role::with('updatedBy')->get();
           
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

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        Log::info($request->all());
        //
        try {
            //code...
            $validator = Validator::make($request->all(),[
                'name' => ['required', 'string', 'max:255'],                
            ]);

            if(!$validator->fails()) {  
                if($request->id) {
                    $data = Role::where('id', $request->id)->update([
                        'name' => $request->name,
                        'dikemaskini_oleh' => $request->user_id,
                        'dikemaskini_pada' => Carbon::now()->format('Y-m-d H:i:s'),
                    ]);
                }else {
                    $data = Role::create([                    
                        'name' => $request->name,
                        'guard_name' => 'web',
                        // 'row_status' => 1,
                        'created_at' =>  Carbon::now()->format('Y-m-d H:i:s'),            
                        'updated_at' =>  Carbon::now()->format('Y-m-d H:i:s'),            
                        'dibuat_pada' => Carbon::now()->format('Y-m-d H:i:s'),
                        'dibuat_oleh' => $request->user_id,
                        'dikemaskini_oleh' => $request->user_id,
                        'dikemaskini_pada' => Carbon::now()->format('Y-m-d H:i:s'),
                    ]);
                }
                
            }else {                
                return response()->json([
                    'code' => '422',
                    'status' => 'Unprocessable Entity',
                    'data' => $validator->errors(),
                ]);
            }

            $userTypes = explode(",",$request->userTypes);
                $temp = UserTypeRole::where('role_id',$request->id)->delete();
                foreach($userTypes as $userType){
                    UserTypeRole::create([ 
                        'user_type_id' => $userType,
                        'role_id' => $request->id,
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

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Role  $role
     * @return \Illuminate\Http\Response
     */
    public function show(Role $role)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Role  $role
     * @return \Illuminate\Http\Response
     */
    public function edit(Role $role)
    {
        //
        //Log::info($role);
        try { 
            $data['roles'] = $role;
            $data['userTypes'] = UserTypeRole::where('role_id',$role->id)->get();
            
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

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Role  $role
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Role $role)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Role  $role
     * @return \Illuminate\Http\Response
     */
    public function destroy(Role $role)
    {
        //
    }

    public function userTypeRoles($id)
    {
        //
        try {
            //code...
            $user = User::whereId($id)->with('bahagian')->first();

            // $user = User::whereId($id)->first();
            $userType = $user->user_type_id;

            if($user->bahagian->acym == 'BKOR' || $user->bahagian->kod_bahagian == 'BPK') 
            {
                $roles_ids = UserTypeRole::where('user_type_id',$userType)->pluck('role_id')->toArray();
            }
            else
            {
                $roles_ids = UserTypeRole::where('user_type_id',$userType)->where('role_id','!=',9)->pluck('role_id')->toArray();
            }


            $data['all_roles'] = Role::whereIn('id',$roles_ids)->get();
            $data['existing_roles'] = $user->roles->pluck('name');

            
            

            return response()->json([
                'code' => '200',
                'status' => 'Success',
                'data' => $data,
                'user' => $user
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


    public function rolePermissions(Request $request)
    {
        try { 
           
            // get the role and permission instances
            $role = Role::where('name',$request->role)->first();
            // $requestPermissions = $request->except(['role', 'pentadbir_full_access','permohon_full_access','permantuan_full_access','kontrak_full_access','peruding_full_access','vm_full_access']);
            $requestPermissions = $request->except(['role']);

            foreach($requestPermissions as $permission) {
                Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
            }

            // sync the permissions to the role
            $role->syncPermissions($requestPermissions);
            
            return response()->json([
                'code' => '200',
                'status' => 'Success',
                'data' => $role,
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

    public function userSpecificPermissions(Request $request)
    {
        try { 
           
            // get the user and permission instances
            $user = User::whereId($request->user_id)->first();
            // $requestPermissions = $request->except(['role', 'pentadbir_full_access','permohon_full_access','permantuan_full_access','kontrak_full_access','peruding_full_access','vm_full_access']);
            $requestPermissions = $request->except(['user_id']);

            foreach($requestPermissions as $permission) {
                Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
            }

            // sync the permissions to the role
            $user->syncPermissions($requestPermissions);
            
            return response()->json([
                'code' => '200',
                'status' => 'Success',
                'data' => $user,
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
