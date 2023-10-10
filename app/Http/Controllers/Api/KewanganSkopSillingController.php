<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use \App\Models\Project;
use \App\Models\RollingPlan;
use \App\Models\SkopProject;
use \APP\Models\KewanganSkopSilling;
use \App\Models\GetSkopOptions;
use \App\Models\KewanganSkop;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Jenssegers\Agent\Facades\Agent;



class KewanganSkopSillingController extends Controller
{
    public function getProjectSkopForKewangan($id)
    {
        try {
            //code...
            $result['project'] = Project::whereId($id)->with(['skopProjects'])->first();
            // $result['skop'] = lookupOption('skop_project');
            $result['skop'] = GetSkopOptions::get(); //lookupOption('skop_project');
            $result['kewangan'] = \App\Models\KewanganSkopSilling::where('permohonan_projek_id', $id)->where('row_status',1)->get();
            

            return response()->json([
                'code' => '200',
                'status' => 'Success',
                'data' => $result
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

    public function addProjectSkopForKewangan(Request $request,$id)
    { 
        try
        {
            
            $myString = $request->scope_id;
            $myArray = explode(',', $myString);
            
            \App\Models\KewanganSkopSilling::where('permohonan_projek_id', $id)->update(['row_status' => 0]);


            for($i=0;$i<count($myArray);$i++)
            { //print_r($i);
                 $siling_array = explode(',', $request->myTableArray[$i]);  //print_r($siling_array);
                 $jumlah_array = explode(',', $request->arrayOfJumlah[$i]);  //print_r($jumlah_array);//exit;

                // $kewangan = \App\Models\KewanganSkopSilling::where('permohonan_projek_id', $id)        
                //                                             ->where('skop_id', $myArray[$i])->first();
                
                // if($kewangan)
                // { 
                //     $data=$request->toArray();
                //     $kewangan->siling_yr1=$siling_array[0];
                //     $kewangan->siling_yr2=$siling_array[1];
                //     $kewangan->siling_yr3=$siling_array[2];
                //     $kewangan->siling_yr4=$siling_array[3];
                //     $kewangan->siling_yr5=$siling_array[4];
                //     $kewangan->siling_yr6=$siling_array[5];
                //     $kewangan->siling_yr7=$siling_array[6];
                //     $kewangan->siling_yr8=$siling_array[7];
                //     $kewangan->siling_yr9=$siling_array[8];
                //     $kewangan->siling_yr10=$siling_array[9];
                //     $kewangan->jumlah_kos=$jumlah_array[0];
                //     $kewangan->dibuat_oleh=$request->user_id;
                //     $kewangan->dikemaskini_oleh=$request->user_id;
                //     $kewangan->dibuat_pada=Carbon::now()->format('Y-m-d H:i:s');
                //     $kewangan->dikemaskini_pada=Carbon::now()->format('Y-m-d H:i:s');
                //     $kewangan->update();

                // }
                // else
                // { 
                    $data=$request->toArray();
                    $silling_data= new KewanganSkopSilling;
                    $silling_data->permohonan_projek_id=$id;
                    $silling_data->skop_id=$myArray[$i];
                    $silling_data->siling_yr1=$siling_array[0];
                    $silling_data->siling_yr2=$siling_array[1];
                    $silling_data->siling_yr3=$siling_array[2];
                    $silling_data->siling_yr4=$siling_array[3];
                    $silling_data->siling_yr5=$siling_array[4];
                    $silling_data->siling_yr6=$siling_array[5];
                    $silling_data->siling_yr7=$siling_array[6];
                    $silling_data->siling_yr8=$siling_array[7];
                    $silling_data->siling_yr9=$siling_array[8];
                    $silling_data->siling_yr10=$siling_array[9];
                    $silling_data->jumlah_kos=$jumlah_array[0];
                    $silling_data->dibuat_oleh=$request->user_id;
                    $silling_data->dikemaskini_oleh=$request->user_id;
                    $silling_data->dibuat_pada=Carbon::now()->format('Y-m-d H:i:s');
                    $silling_data->dikemaskini_pada=Carbon::now()->format('Y-m-d H:i:s');
                    $silling_data->save();

                //}
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