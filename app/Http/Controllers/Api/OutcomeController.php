<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use \App\Models\Outcome;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Jenssegers\Agent\Facades\Agent;

class OutcomeController extends Controller
{
    public function list(Request $request)
    {
        try {
            if($request->id){
                $data = \App\Models\Outcome::where('Permohonan_Projek_id','=',$request->id)->first();
            }else {
                $data = \App\Models\Outcome::get();
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
       
        try{

            $data=$request->toArray();
            //print_r($data);
            $project_id=$data['Permohonan_Projek_id'];
            //$session_id = session()->getId();

            $output_data = \App\Models\Outcome::where('Permohonan_Projek_id', $request->id)->first();     
                        if($output_data)
                        {
                            $outputpage = $this->updateoutcomedata($request->all(),$request->id);                        
                        }
                        else{
                            $outputpage= new Outcome;
                            $outputpage->Permohonan_Projek_id=$project_id;
                            $outputpage->Projek_Outcome=$data['Projek_Outcome'];
                            $outputpage->Kuantiti=$data['Kuantiti'];
                            $outputpage->unit_id=$data['unit_id'];
                            $outputpage->dibuat_oleh=$request->user_id;
                            $outputpage->dikemaskini_oleh=$request->user_id;
                            $outputpage->dibuat_pada=Carbon::now()->format('Y-m-d H:i:s');
                            $outputpage->dikemaskini_pada=Carbon::now()->format('Y-m-d H:i:s');
                            $outputpage->save();
                        }
                        

            
            
                return response()->json([
                    'code' => '200',
                    'status' => 'Success',
                    'data' => $outputpage,
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

    public function OutputPageDetails($id)
    {
        try {
            $user = \App\Models\Outcome::whereId($id)->first();            
            
            return response()->json([
                'code' => '200',
                'status' => 'Sucess',
                'data' => $data
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

    

    

    protected function updateoutcomedata(array $data, $id)
    {  
        try{      
                return Outcome::where('Permohonan_Projek_id', $id)->update([
                    'Projek_Outcome' => $data['Projek_Outcome'],
                    'Kuantiti' => $data['Kuantiti'],            
                    'unit_id' => $data['unit_id'],            
                    'dikemaskini_oleh' => $request->user_id,
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
