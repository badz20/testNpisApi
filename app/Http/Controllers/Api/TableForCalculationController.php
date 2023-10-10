<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use \App\Models\TableForCalculation;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Jenssegers\Agent\Facades\Agent;

class TableForCalculationController extends Controller
{
    public function list()
    {
        try {
           
            $data = \App\Models\TableForCalculation::where('IsActive','=',1)->get();       
           
            
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

    public function listskopcost()
    {
        try {
           
            $data = \App\Models\TableForCalculation::get();       
           
            
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

    public function listskopcostbyid($id)
    {
        try {
  
             $data = \App\Models\TableForCalculation::where('id',$id)->get();                
            
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

    public function addskopcost(Request $request)
    {
         $skop = TableForCalculation::create([
             'total_cost' => $request->total_cost,
             'P_min' => $request->p_min,
             'P_max' => $request->p_max
         ]);
         
         return response()->json([
                 'code' => '200',
                 'status' => 'Success',
         ]);

    }
    public function editskopcost(Request $request)
    {
        $data=$request->toArray();
        // print_r($data);exit;
        $units = TableForCalculation::where('id',$data['id'])->first();
        $units->total_cost=$data['total_cost'];
        $units->P_min=$data['p_min'];
        $units->P_max=$data['p_max'];
        $units->update();
         
         return response()->json([
                 'code' => '200',
                 'status' => 'Success',
         ]);

    }

    public function updateSkopCostStatus(Request $request)
    {
         $data=$request->toArray();
 
         $units = TableForCalculation::where('id',$data['id'])->first();
         $units->IsActive=$data['value'];
         $units->update();
         if($units->update()=='true'){
             return response()->json([
                 'code' => '200',
                 'status' => 'Success',
             ]);
         }
    }
}
