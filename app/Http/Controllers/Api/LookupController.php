<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use \App\Models\Lookup;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Carbon;
use DB;
use Jenssegers\Agent\Facades\Agent;

class LookupController extends Controller
{
    //

    public function index()
    {
        try {
            $data = Lookup::with('users')->get();
            //
            //dd($data);
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
                'name' => ['required', 'string', 'max:255'],                
            ]);

            if(!$validator->fails()) {  
               $data = DB::table('lookups')
                    ->insert([
                    'uuid'=>\Illuminate\Support\Str::uuid(),
                    'value' => $request->name,
                    'row_status' => 1,
                    'dikemaskini_oleh' => $request->user_id,
                    'key' => "master",
                    'json_value' => '{}',
                    'catatan' => $request->description, 
                    'created_at' =>  Carbon::now()->format('Y-m-d H:i:s'),            
                    'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
                    ]);
            //    $data = Lookup::create([
            //         'uuid' => \Illuminate\Support\Str::uuid(),
            //         'value' => $request->name,
            //         'row_status' => 1,
            //         'dikemaskini_oleh' => $request->user_id,
            //         'code' => "master",
            //         'json_value' => '{}',
            //         'catatan' => $request->description, 
            //         'created_at' =>  Carbon::now()->format('Y-m-d H:i:s'),            
            //         'dikemaskini_pada' => Carbon::now()->format('Y-m-d H:i:s'),
            //     ]);

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
}
