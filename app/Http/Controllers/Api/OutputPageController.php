<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use \App\Models\OutputPage;
use \App\Models\Outcome;
use \App\Models\projectLog;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Jenssegers\Agent\Facades\Agent;



class OutputPageController extends Controller
{

    public function index($id)
    {
        try {
            //code...
            $result['output'] = OutputPage::where('Permohonan_Projek_id',$id)->where('row_status', 1)->get();
            $result['outcome'] = Outcome::where('Permohonan_Projek_id',$id)->where('row_status', 1)->get();     
            $result['unit'] = \App\Models\OutputUnit::where('IsActive','=',1)->get();

            
            return response()->json([
                'code' => '200',
                'status' => 'Success',
                'data' => $result,
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

    public function list(Request $request)
    {
        try {
            if($request->id){
                $data = \App\Models\OutputPage::where('Permohonan_Projek_id','=',$request->id)
                                              ->where('row_status', 1)->first();
            }else {
                $data = \App\Models\OutputPage::where('row_status', 1)->get();
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
        //$project_id=$data['Permohonan_Projek_id'];
        //$session_id = session()->getId();

        $project_id = $request->id;
            if($request->output){
                    OutputPage::where('Permohonan_Projek_id',$request->id)->update(['row_status' => 0]);
                    foreach ($request->output as $outputdetails) {  
                        $data = json_decode($outputdetails, TRUE);               
                        $outcome = OutputPage::create([   
                            'Permohonan_Projek_id' => $request->id,
                            'unit_id' => $data['unit_id'],
                            'output_proj' => $data['output_proj'],
                            'Kuantiti' => $data['Kuantiti'],
                            'dibuat_oleh' => $request->user_id
                        ]);
                    }
                
        
            }

            if($request->outcome){
                Outcome::where('Permohonan_Projek_id',$request->id)->update(['row_status' => 0]);
                foreach ($request->outcome as $outcomedetails) {   
                    $data = json_decode($outcomedetails, TRUE);              
                    $outcome = Outcome::create([   
                        'Permohonan_Projek_id' => $request->id,
                        'unit_id' => $data['unit_id'],
                        'Projek_Outcome' => $data['Projek_Outcome'],
                        'Kuantiti' => $data['Kuantiti'],
                        'dibuat_oleh' => $request->user_id
                    ]);
                }

            }   
            $section_name='Output outcome';
            if($outcome){
                $user_data = DB::table('users')
                ->join('ref_jawatan','ref_jawatan.id', '=','users.jawatan_id')
                ->select('users.*','ref_jawatan.nama_jawatan')->where('users.id',$request->user_id)->first();
                $no_rojukan_data = DB::table('projects')->select('no_rujukan')->where('id',$request->id)->first();
                $logData=[
                 'user_id' =>$request->user_id, 
                 'section_name'=>$section_name,   
                 'projek_id'=>$request->id,
                 'modul' => 'Permohonan Projek',
                 'user_ic_no' => $user_data->no_ic,
                 'user_jawatan' => $user_data->nama_jawatan,
                 'user_name' => $user_data->name,
                 'no_rujukan' => $no_rojukan_data-> no_rujukan,
                ];
                DB::connection(env('DB_CONNECTION_AUDIT'))->table('projek_log')->insert($logData);

            }
            return response()->json([
                'code' => '200',
                'status' => 'Success',
                'data' => json_decode($request->outcome[0], TRUE),
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
            $user = \App\Models\OutputPage::whereId($id)->where('row_status', 1)->first();            
            
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

    

    protected function updateoutputdata(array $data, $id)
    {   
        try{     
                    return OutputPage::where('Permohonan_Projek_id', $id)->update([
                    'output_proj' => $data['output_proj'],
                    'Kuantiti' => $data['Kuantiti'],            
                    'unit_id' => $data['unit_id'],            
                    'dikemaskini_oleh' => $data['user_id'],
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
