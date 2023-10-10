<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use \App\Models\RmkSasaran;
use \App\Models\RmkIndikatori;
use \App\Models\RMKOBBSasaran;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Jenssegers\Agent\Facades\Agent;


class RmkSasaranController extends Controller
{
    public function list(Request $request)
    {
        try {
            //code...
                if($request->id){
                    //$data = \App\Models\RmkSasaran::where('SDG_id',$request->id)->get();   
                    $data = \App\Models\RmkSasaran::where('SDG_id',$request->id)->with(['sdg','updatedBy'])->get(); 
                }
                else{
                    //$data = \App\Models\RmkSasaran::get();
                    $data = \App\Models\RmkSasaran::with(['sdg','updatedBy'])->get();
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

    public function list_rmk(Request $request)
    {
        try {
            //code...
                if($request->id){
                    //$data = \App\Models\RmkSasaran::where('SDG_id',$request->id)->get();   
                    $data = \App\Models\RmkSasaran::where('SDG_id',$request->id)->where('is_active',1)->where('row_status',1)->get(); 
                }
                // else{
                //     //$data = \App\Models\RmkSasaran::get();
                //     $data = \App\Models\RmkSasaran::with(['sdg','updatedBy'])->get();
                // }
                
           
            
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

    public function listsingle(Request $request)
    {
        try {
            //code...
                if($request->id){
                    //$data = \App\Models\RmkSasaran::where('SDG_id',$request->id)->get();   
                    $data = \App\Models\RmkSasaran::where('id',$request->id)->with(['sdg'])->get(); 
                }
                else{
                    //$data = \App\Models\RmkSasaran::get();
                    $data = \App\Models\RmkSasaran::with(['sdg'])->get();
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

    public function listsasaran_single(Request $request)
    {
        try {
            //code...
                if($request->id){
                    $data = \App\Models\RmkSasaran::where('id',$request->id)->get();    
                }
                // else{
                //     $data = \App\Models\RmkSasaran::get();
                // }
                
           
            
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

                $rmk_data = \App\Models\RMKOBBSasaran::where('permohonan_projek_id', $request->id)->first();     
                            if($rmk_data)
                            {
                                $rmkobbpage = $this->updateSasaranData($request->all(),$request->id);
                                
                            }
                            else{
                                $rmkobbpage= new RMKOBBSasaran;
                                $rmkobbpage->permohonan_projek_id=$request->id;
                                $rmkobbpage->Sasaran_id=$data['Sasaran'];
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
    public function updateSasaranMasterData(Request $request)
    {      
        try{
            $data=$request->toArray();
            if($request->id) {   
                
                return RmkSasaran::where('id', $data['id'])->update([
                    'Sasaran' =>  $data['Sasaran'],
                    'SDG_id' => $data['SDG_id'],
                    'dikemaskini_oleh' => $data['user_id'],
                    'dikemaskini_pada' => Carbon::now()->format('Y-m-d H:i:s'),
                ]);

                
            }else {                    
                $data = RmkSasaran::create([                    
                        'SDG_id' => $request->SDG_id,
                        'BIL' => $request->BIL,
                        'Sasaran' => $request->Sasaran,
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

    protected function updateSasaranData(array $data, $id)
    {        
        return RMKOBBSasaran::where('permohonan_projek_id', $id)->update([
            'Sasaran_id' => $data['Sasaran'],
                     
            'dikemaskini_oleh' => $request->user_id,
            'dikemaskini_pada' => Carbon::now()->format('Y-m-d H:i:s'),
        ]);
    }

    public function activate(Request $request){
        try{
                $data = RmkSasaran::where('id', $request->id)->update([
                    'is_active' => $request->value,
                    'dikemaskini_oleh' => $request->loged_user_id,
                    'dikemaskini_pada' => Carbon::now()->format('Y-m-d H:i:s'),
                ]);

                $data = RmkIndikatori::where('Sasaran_id', $request->id)->update([
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
                $data = RmkSasaran::where('id', $request->id)->update([
                    'is_active' => $request->value,
                    'dikemaskini_oleh' => $request->loged_user_id,
                    'dikemaskini_pada' => Carbon::now()->format('Y-m-d H:i:s'),
                ]);

                $data = RmkIndikatori::where('Sasaran_id', $request->id)->update([
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
