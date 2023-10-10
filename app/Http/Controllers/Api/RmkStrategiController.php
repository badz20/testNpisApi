<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use \App\Models\RmkStrategi;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Carbon;
use Jenssegers\Agent\Facades\Agent;


class RmkStrategiController extends Controller
{
    public function list(Request $request)
    {
        try {
            //code...
            if($request->id){
                $data = \App\Models\RmkStrategi::where('id',$request->id)->first();
            }else {
                $data = \App\Models\RmkStrategi::where('row_status',1)->get();
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

    public function store(Request $request){
        // dd($request->id);
        try{
            if($request->id==null){
                $validated = $request->validate([
                    'NamaStrategi' => 'required',
                    'TemaPemangkinDasar' => 'required',
                    'Bab' => 'required',
                    'BidangKeutamaan' => 'required',
                    'OutcomeNasional' => 'required', 
                ]);
                    // dd($request->Catatan); 
                    $data = RmkStrategi::create([   
                        'nama_strategi' => $request->NamaStrategi,
                        'Tema_Pemangkin_Dasar' => $request->TemaPemangkinDasar,
                        'Bab' => $request->Bab,
                        'Bidang_Keutamaan' => $request->BidangKeutamaan,
                        'Outcome_Nasional' => $request->OutcomeNasional,
                        'Catatan' => $request->Catatan,
                        'is_hidden' => 0,
                        'dibuat_oleh' => $request->user_id,
                    ]);
                    return response()->json([
                        'code' => '200',
                        'status' => 'Success',
                    ]);

                
            }else{
                $validated = $request->validate([
                    'NamaStrategi' => 'required',
                    'TemaPemangkinDasar' => 'required',
                    'Bab' => 'required',
                    'BidangKeutamaan' => 'required',
                    'OutcomeNasional' => 'required', 
                ]);
                // dd($request->id);
                $data = RmkStrategi::where('id',$request->id)->update([   
                    'nama_strategi' => $request->NamaStrategi,
                    'Tema_Pemangkin_Dasar' => $request->TemaPemangkinDasar,
                    'Bab' => $request->Bab,
                    'Bidang_Keutamaan' => $request->BidangKeutamaan,
                    'Outcome_Nasional' => $request->OutcomeNasional,
                    'Catatan' => $request->Catatan,
                    'dikemaskini_oleh' => $request->user_id,
                    'dikemaskini_pada' => Carbon::now()->format('Y-m-d H:i:s'),
                ]);
                return response()->json([
                    'code' => '200',
                    'status' => 'Success',
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
                $data = RmkStrategi::where('id', $request->id)->update([
                    'is_hidden' => $request->value,
                    'dikemaskini_oleh' => $request->loged_user_id,
                    'dikemaskini_pada' => Carbon::now()->format('Y-m-d H:i:s'),
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
        // dd($request);
        try{
            
            $data = RmkStrategi::where('id', $request->id)->update([
                'is_hidden' => $request->value,
                'dikemaskini_oleh' => $request->loged_user_id,
                'dikemaskini_pada' => Carbon::now()->format('Y-m-d H:i:s'),
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
