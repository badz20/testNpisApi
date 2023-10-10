<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use \App\Models\RmkIndikatori;
use \App\Models\RMKOBBIndikatori;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Jenssegers\Agent\Facades\Agent;



class RmkIndikatoriController extends Controller
{
    public function list(Request $request)
    {
        try {
            //code...
            // $sasaranid = $request->id;
            // $arr = array();
            // $j = sizeof($request->id);
            // for($i=0;$i<$j;$i++)
            // {
            //     $arr[$i] = $sasaranid[$i];
            // }

            $arr = explode(',', $request->id);
            // dd($arr);
            //$data['all_indikators'] = DB::table('REF_Indikatori')->select(DB::raw('*'))->wherein('Sasaran_id',$arr)->get(); 
            $data['all_indikators'] = \App\Models\RmkIndikatori::whereIn('Sasaran_id',$arr)->with(['sdg','updatedBy'])->get();
            // $data = \App\Models\RmkIndikatori::whereIn('Sasaran_id',explode(",",$request->selectedValues))->get();


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

    public function list_rmk(Request $request)
    {
        try {

            $arr = explode(',', $request->id);
           
            $data['all_indikators'] = \App\Models\RmkIndikatori::whereIn('Sasaran_id',$arr)->where('is_active',1)->where('row_status',1)->get();
           
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


    public function listall(Request $request)
    {
        try {
            //code...
            // $sasaranid = $request->id;
            // $arr = array();
            // $j = sizeof($request->id);
            // for($i=0;$i<$j;$i++)
            // {
            //     $arr[$i] = $sasaranid[$i];
            // }

            $arr = explode(',', $request->id);
            //$data['all_indikators'] = DB::table('REF_Indikatori')->select(DB::raw('*'))->get(); 
            $data['all_indikators'] = \App\Models\RmkIndikatori::with(['sdg','updatedBy'])->get();
            // $data = \App\Models\RmkIndikatori::whereIn('Sasaran_id',explode(",",$request->selectedValues))->get();


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

    public function listindikator_single(Request $request)
    {
        try {

            $data = \App\Models\RmkIndikatori::whereId($request->id)->with(['sdg'])->first();
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
                $project_id=$data['id'];
                //$session_id = session()->getId();

                $rmk_data = \App\Models\RMKOBBIndikatori::where('permohonan_projek_id', $request->id)->first();     
                            if($rmk_data)
                            {
                                $rmkobbpage = $this->updateIndikatoriData($request->all(),$request->id);
                                
                            }
                            else{
                                $rmkobbpage= new RMKOBBIndikatori;
                                $rmkobbpage->permohonan_projek_id=$request->id;
                                $rmkobbpage->Indikatori_id=$data['Indikatori'];
                                $rmkobbpage->dibuat_oleh=$request->user_id;
                                $rmkobbpage->dikemaskini_oleh=$request->user_id;
                                $rmkobbpage->dibuat_pada=Carbon::now()->format('Y-m-d H:i:s');
                                $rmkobbpage->dikemaskini_pada=Carbon::now()->format('Y-m-d H:i:s');
                                $rmkobbpage->save();
                            }
                            

                
                
                    return response()->json([
                        'code' => '200',
                        'status' => 'Success',
                        'data' => $rmkobbpage,
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

    protected function updateIndikatoriData(array $data, $id)
    {        
        return RMKOBBIndikatori::where('permohonan_projek_id', $id)->update([
            'Indikatori_id' => $data['Indikatori'],
                     
            'dikemaskini_oleh' => $request->user_id,
            'dikemaskini_pada' => Carbon::now()->format('Y-m-d H:i:s'),
        ]);
    }

    protected function updateIndikatorMasterData(Request $request)
    {        
        try{
            $data=$request->toArray();
            if($data['id']) {   
                
                return RmkIndikatori::where('id', $data['id'])->update([
                    'SDG_id' => $data['sdgid'],
                    'Indikatori' =>  $data['name'],
                    'Sasaran_id' => $data['sasaran'],
                    'BIL' => $data['BIL'],
                    'dikemaskini_oleh' => $data['user_id'],
                    'dikemaskini_pada' => Carbon::now()->format('Y-m-d H:i:s'),
                ]);

                
            }else {                    
                $data = RmkIndikatori::create([                    
                        'SDG_id' => $request->sdgid,
                        'BIL' => $request->BIL,
                        'Indikatori' => $request->name,
                        'Sasaran_id' => $request->sasaran,
                        'created_at' =>  Carbon::now()->format('Y-m-d H:i:s'),            
                        'dibuat_pada' => Carbon::now()->format('Y-m-d H:i:s'),
                        'dibuat_oleh' => $request->user_id,
                        'dikemaskini_oleh' => $request->user_id,
                        'dikemaskini_pada' => Carbon::now()->format('Y-m-d H:i:s'),
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
                $data = RmkIndikatori::where('id', $request->id)->update([
                    'is_active' => $request->value,
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

        try{
                $data = RmkIndikatori::where('id', $request->id)->update([
                    'is_active' => $request->value,
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
