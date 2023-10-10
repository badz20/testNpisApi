<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use \App\Models\Pentadbir_Data_Services;
use \App\Models\Pentadbir_modules;
use \App\Models\PSDA_model;
use \App\Models\RmkStrategi;
use \App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
use Jenssegers\Agent\Facades\Agent;


class rmk_Controller extends Controller
{
  public function list(){
    try{
            $data=RmkStrategi::with(['user'=> function ($query) {
                $query->select('id', 'name');
            }])->get();
            return response()->json([
                'code' => '200',
                'status' => 'Success',
                'data'=>$data
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
        $RmkStrategi= new RmkStrategi;
        $RmkStrategi->Tema_Pemangkin_Dasar=$data['tema'];
        $RmkStrategi->Bab=$data['bab'];
        $RmkStrategi->Bidang_Keutamaan=$data['bidang'];
        $RmkStrategi->Outcome_Nasional=$data['outcome'];
        $RmkStrategi->nama_strategi=$data['strategi'];
        $RmkStrategi->kod_strategi=$data['kod'];
        $RmkStrategi->Catatan=$data['catatan'];
        $RmkStrategi->dibuat_oleh=$request->user_id;
        $RmkStrategi->dikemaskini_oleh=$request->user_id;
        $RmkStrategi->dibuat_pada=Carbon::now()->format('Y-m-d H:i:s');
        $RmkStrategi->dikemaskini_pada=Carbon::now()->format('Y-m-d H:i:s');
        $RmkStrategi->save();
        if($RmkStrategi->save()=='true'){
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

  public function edit($id){

    try{
            $data=RmkStrategi::where('id','=',$id)->get();
            return response()->json([
                'code' => '200',
                'status' => 'Success',
                'data'=>$data
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

  public function update(Request $request){

    try{
        $data=$request->toArray();
        $RmkStrategi = RmkStrategi::where('id',$data['update_id'])->first();
        $RmkStrategi->Tema_Pemangkin_Dasar=$data['tema'];
        $RmkStrategi->Bab=$data['bab'];
        $RmkStrategi->Bidang_Keutamaan=$data['bidang'];
        $RmkStrategi->Outcome_Nasional=$data['outcome'];
        $RmkStrategi->nama_strategi=$data['strategi'];
        $RmkStrategi->kod_strategi=$data['kod'];
        $RmkStrategi->Catatan=$data['catatan'];
        $RmkStrategi->dibuat_oleh=$request->user_id;
        $RmkStrategi->dikemaskini_oleh=$request->user_id;
        $RmkStrategi->dibuat_pada=Carbon::now()->format('Y-m-d H:i:s');
        $RmkStrategi->dikemaskini_pada=Carbon::now()->format('Y-m-d H:i:s');
        $RmkStrategi->save();
        if($RmkStrategi->save()=='true'){
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
    $data=$request->toArray();
        // print_r($data);
        $activate_id=$data['id'];
        $value=$data['value'];
        $activate_modules=RmkStrategi::find($activate_id);
        $activate_modules->dibuat_oleh=$request->user_id;
        $activate_modules->dikemaskini_oleh=$request->user_id;
        $activate_modules->dibuat_pada=Carbon::now()->format('Y-m-d H:i:s');
        $activate_modules->dikemaskini_pada=Carbon::now()->format('Y-m-d H:i:s');
        $activate_modules->dikemaskini_pada=Carbon::now()->format('Y-m-d H:i:s'); 
        $activate_modules->row_status=$value;        
        $activate_modules->save();
        if($activate_modules->save()=='true'){
            return response()->json([
                'code' => '200',
                'status' => 'Success',
                'data' => $data,
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

  public function deactivate(Request $request){

    try{
        $data=$request->toArray();
        // print_r($data);
        $activate_id=$data['id'];
        $value=$data['value'];
        $deactivate_modules=RmkStrategi::find($activate_id);
        $deactivate_modules->dibuat_oleh=$request->user_id;
        $deactivate_modules->dikemaskini_oleh=$request->user_id;
        $deactivate_modules->dibuat_pada=Carbon::now()->format('Y-m-d H:i:s');
        $deactivate_modules->dikemaskini_pada=Carbon::now()->format('Y-m-d H:i:s');
        $deactivate_modules->dikemaskini_pada=Carbon::now()->format('Y-m-d H:i:s'); 
        $deactivate_modules->row_status=$value;        
        $deactivate_modules->save();
        if($deactivate_modules->save()=='true'){
            return response()->json([
                'code' => '200',
                'status' => 'Success',
                'data' => $data,
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


}
