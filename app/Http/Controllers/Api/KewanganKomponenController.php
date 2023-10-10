<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use \App\Models\KewanganKomponen;
use \App\Models\KewanganNegeri;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Jenssegers\Agent\Facades\Agent;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;



class KewanganKomponenController extends Controller
{
    //
    public function list(Request $request)
    {
        try {
            if($request->id){
                
                $data = \App\Models\KewanganKomponen::where('id','=',$request->id)->where('IsActive','=',1)->where('row_status',1)->first();
            }else {
                
                // var_dump('Hi');
                $data = \App\Models\KewanganKomponen::where('IsActive','=',1)->where('row_status',1)->get();
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

    public function listkomponen(Request $request)
    {
        try {
            if($request->id){
                
                $data = \App\Models\KewanganKomponen::with(['user'])->where('id','=',$request->id)->where('row_status',1)->first();
            }else {
                
                // var_dump('Hi');
                $data = \App\Models\KewanganKomponen::with(['user'])->where('row_status',1)->get();
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

    public function updatekomponen(Request $request){
        try{
            $data=$request->toArray();
            // print_r($data);exit;
            $units = KewanganKomponen::where('id',$data['id'])->first();
            $units->nama_komponen=$data['nama_komponen'];
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

   public function addkomponen(Request $request)
   {
       try{
            $data=$request->toArray();
            // print_r($data);exit;
            $latest = KewanganKomponen::orderBy('id', 'DESC')->first();

            // print_r($latest['kod']);exit;

            if($latest)
            {
            $kod = $latest['kod']+1;
            }
            else
            {
            $kod = 1;
            }

            $units = KewanganKomponen::create([
                'nama_komponen' => $request->nama_komponen,
                'kod' => $kod,
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

   public function updateKomponenStatus(Request $request)
   {
       try{
            $data=$request->toArray();

            $units = KewanganKomponen::where('id',$data['id'])->first();
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

   public function getKewanganNegeriData($id)
   {
       try {

           $result['data'] = \App\Models\KewanganKomponen::with(['user'])->where('row_status',1)->get();           
           $result['project'] = \App\Models\Project::whereId($id)->first();
        //    $result['negeri'] = \App\Models\ProjectNegeriLokas::with(['negeri'])->where('permohonan_Projek_id',$id)->distinct()->get();
           $result['negeri'] = DB::table('project_negeri_lokas')
                                ->join('ref_negeri','ref_negeri.id', '=','project_negeri_lokas.negeri_id')
                                ->select('ref_negeri.nama_negeri','project_negeri_lokas.negeri_id')
                                ->where('project_negeri_lokas.permohonan_Projek_id',$id)
                                ->distinct()
                                ->get();
            $result['kewangan_negeri'] = KewanganNegeri::where('pp_id',$id)->where('row_status',1)->get();
          
           
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

   public function addKewanganNegeriData(Request $request)
    { 
        Log::info($request);
        try
        {     
            
            

            if($request->kos_array){ 

                \App\Models\KewanganNegeri::where('pp_id', $request->project_id)->update(['row_status' => 0]);

                foreach ($request->kos_array as $kos_array) {  
                    $sub_json = json_decode($kos_array,TRUE);
                    
                    $terda_data=new KewanganNegeri;
                    $terda_data->pp_id = $request->project_id;
                    $terda_data->negeri_id = $sub_json['negeri'];
                    $terda_data->kos_data = $sub_json['kos'];
                    $terda_data->siling_yr1 = $sub_json['siling_0'];
                    $terda_data->siling_yr2 = $sub_json['siling_1'];
                    $terda_data->siling_yr3 = $sub_json['siling_2'];
                    $terda_data->siling_yr4 = $sub_json['siling_3'];
                    $terda_data->siling_yr5 = $sub_json['siling_4'];
                    $terda_data->siling_yr6 = $sub_json['siling_5'];
                    $terda_data->siling_yr7 = $sub_json['siling_6'];
                    $terda_data->siling_yr8 = $sub_json['siling_7'];
                    $terda_data->siling_yr9 = $sub_json['siling_8'];
                    $terda_data->siling_yr10 = $sub_json['siling_9'];
                    $terda_data->dibuat_oleh = $request->user_id;
                    $terda_data->dibuat_pada = Carbon::now()->format('Y-m-d H:i:s');
                    $terda_data->dikemaskini_oleh = $request->user_id;
                    $terda_data->dikemaskini_pada = Carbon::now()->format('Y-m-d H:i:s');
                    $terda_data->save();
                    
                }

            }
        
                return response()->json([
                    'code' => '200',
                    'status' => 'Success'
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
