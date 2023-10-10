<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use \App\Models\Units;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Carbon;
use Jenssegers\Agent\Facades\Agent;

class UnitsController extends Controller
{
    //
    public function list(Request $request)
    {
        try {
            //code...
            
            $data = \App\Models\Units::where('IsActive','=',1)->get();
           
            
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

    public function listunits(Request $request)
    {
        try {
            //code...
            
                if($request->id){
                    $data = \App\Models\Units::with(['user'])->where('id',$request->id)->first();
                }else {
                    $data = \App\Models\Units::with(['user'])->get();
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

    public function updateunits(Request $request){
        try{
                $data=$request->toArray();
                // print_r($data);exit;
                $units = Units::where('id',$data['id'])->first();
                $units->nama_unit=$data['nama_unit'];
                $units->dikemaskini_oleh=$data['user_id'];
                $units->dikemaskini_pada=Carbon::now()->format('Y-m-d H:i:s');
                $units->update();
                if($units->update()=='true'){
                    return response()->json([
                        'code' => '200',
                        'status' => 'Success',
                    ]);
                }
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

   public function addunits(Request $request)
   {
        try{
                $data=$request->toArray();
                // print_r($data);exit;
                $units = Units::create([
                    'nama_unit' => $request->nama_unit,
                    'dibuat_pada' => Carbon::now()->format('Y-m-d H:i:s'),
                    'dibuat_oleh' => $request->user_id,
                    'dikemaskini_pada' => Carbon::now()->format('Y-m-d H:i:s'),
                    'dikemaskini_oleh' => $request->user_id,
                ]);
                
                return response()->json([
                        'code' => '200',
                        'status' => 'Success',
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

   public function updateStatus(Request $request)
   {
        try{
            $data=$request->toArray();

            $units = Units::where('id',$data['id'])->first();
            $units->IsActive=$data['value'];
            $units->dikemaskini_oleh=$data['user_id'];
            $units->dikemaskini_pada=Carbon::now()->format('Y-m-d H:i:s');
            $units->update();
            if($units->update()=='true'){
                return response()->json([
                    'code' => '200',
                    'status' => 'Success',
                ]);
            }
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
}
