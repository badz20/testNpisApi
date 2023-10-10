<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UserType;
use App\Models\UserTypeRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Jenssegers\Agent\Facades\Agent;


class UserTypeController extends Controller
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
            $data = UserType::with('updatedBy')->get();
           
            return response()->json([
                'code' => '200',
                'status' => 'Success',
                'data' => $data,
            ]);

        } catch (\Throwable $th) {
            logger()->error($th->getMessage());            
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
        //
        try {
            //code...
            $validator = Validator::make($request->all(),[
                'name' => ['required', 'string', 'max:255'],                
            ]);

            if(!$validator->fails()) {  
                if($request->id) {
                    $data = UserType::where('id', $request->id)->update([
                        'name' => $request->name,
                        'dikemaskini_oleh' => $request->user_id,
                        'dikemaskini_pada' => Carbon::now()->format('Y-m-d H:i:s'),
                    ]);

                    $data = UserType::whereId($request->id)->first();
                }else {
                    $data = UserType::create([                    
                        'name' => $request->name,
                        'guard_name' => 'api',
                        // 'row_status' => 1,
                        'created_at' =>  Carbon::now()->format('Y-m-d H:i:s'),            
                        'updated_at' =>  Carbon::now()->format('Y-m-d H:i:s'),            
                        'dibuat_pada' => Carbon::now()->format('Y-m-d H:i:s'),
                        'dibuat_oleh' => $request->user_id,
                        'dikemaskini_oleh' => $request->user_id,
                        'dikemaskini_pada' => Carbon::now()->format('Y-m-d H:i:s'),
                    ]);
                }


                
                $roles = explode(",",$request->roles);
                $temp = UserTypeRole::where('user_type_id',$data->id)->delete();
                foreach($roles as $role){
                    UserTypeRole::create([ 
                        'user_type_id' => $data->id,
                        'role_id' => $role,
                    ]);
                }
                
                
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
     * @param  \App\Models\UserType  $userType
     * @return \Illuminate\Http\Response
     */
    public function show(UserType $userType)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\UserType  $userType
     * @return \Illuminate\Http\Response
     */
    public function edit($userType)
    {
        //
        $data = UserType::whereId($userType)->with('roles')->first();
        try { 
            return response()->json([
                'code' => '200',
                'status' => 'Success',
                'data' => $data,
            ]);

        } catch (\Throwable $th) {
            logger()->error($th->getMessage());            
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
     * @param  \App\Models\UserType  $userType
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, UserType $userType)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\UserType  $userType
     * @return \Illuminate\Http\Response
     */
    public function destroy(UserType $userType)
    {
        //
    }
}
