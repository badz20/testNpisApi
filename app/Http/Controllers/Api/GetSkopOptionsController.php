<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use \App\Models\GetSkopOptions;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Carbon;
use Jenssegers\Agent\Facades\Agent;



class GetSkopOptionsController extends Controller
{
    public function list(Request $request)
    {
        try {
            
            
                $data = \App\Models\GetSkopOptions::get();
           
            
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
