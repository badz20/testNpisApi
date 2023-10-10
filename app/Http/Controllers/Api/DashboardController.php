<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\tempUser;
use App\Models\CmsKandungan;
use Jenssegers\Agent\Facades\Agent;


class DashboardController extends Controller
{

    public function getUsersCount(Request $request)
    {
        try {
            $data = [];
            $data['users']  = User::get()->count();
            $data['users_jps'] = User::where('jenis_pengguna_id' , 1)->get()->count();
            $data['users_agensi'] = User::where('jenis_pengguna_id' , 2)->get()->count();
            $data['users_temp'] = tempUser::get()->count();
            $result = CmsKandungan::where('unique_key','header')->with('media')->first();
            // if($data->is_video) {                
            //     $media = $data->getMedia('video');
            // }else {                
            //     $media = $data->getMedia('image');
            // }
            // $data['media_details'] = $media; 
            //$result = \App\Models\CmsHeader::whereId(1)->first();

            $logo1 = $result->getMedia('logo_1')->first();
            $logo2 = $result->getMedia('logo_2')->first();
            $logo3 = $result->getMedia('logo_3')->first();
            
            
            $result['logo1'] = '';
            $result['logo2'] = '';
            $result['logo3'] = '';
            if($logo1){
                $result['logo1'] = $logo1->getUrl();
            }

            if($logo2){
                $result['logo2'] = $logo2->getUrl();
            }

            if($logo3){
                $result['logo3'] = $logo3->getUrl();
            }

            
            return response()->json([
                'code' => '200',
                'status' => 'Success',
                'data' => $data,
                'data2' => $result,
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
