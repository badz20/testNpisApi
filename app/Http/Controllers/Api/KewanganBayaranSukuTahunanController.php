<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use \App\Models\Project;
use \App\Models\RollingPlan;
use \App\Models\SkopProject;
use \APP\Models\KewanganBayaranSukuTahunan;
use \APP\Models\KewanganSkopSilling;
use \App\Models\GetSkopOptions;
use \App\Models\KewanganSkop;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Jenssegers\Agent\Facades\Agent;



class KewanganBayaranSukuTahunanController extends Controller
{
    public function getBayaranSukuForKewangan($id)
    {
        try {
            //code...
            $result['project'] = Project::whereId($id)->with(['skopProjects'])->first();
            // $result['skop'] = lookupOption('skop_project');
            $result['skop'] = GetSkopOptions::get(); //lookupOption('skop_project');
            $result['kewangan'] = \App\Models\KewanganSkopSilling::where('permohonan_projek_id', $id)->where('row_status',1)->get();
            $result['bayaran'] = \App\Models\KewanganBayaranSukuTahunan::where('permohonan_projek_id', $id)
                                ->where('row_status',1)->get();
            

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

    public function addBayaranSukuForKewangan(Request $request,$id)
    { 
        //print_r($request->all());exit;
        try
        {
            
            $myString = $request->scope_id;
            $myArray = explode(',', $myString);
            \App\Models\KewanganBayaranSukuTahunan::where('permohonan_projek_id', $id)->update(['row_status' => 0]);


            for($i=0;$i<count($myArray);$i++)
            { 
                 $siling_array = explode(',', $request->myTablekomponen[$i]);  //print_r($siling_array);

                // $kewangan = \App\Models\KewanganBayaranSukuTahunan::where('permohonan_projek_id', $id)        
                //                                             ->where('skop_id', $myArray[$i])->first();
                
                // if($kewangan)
                // { 
                //     $data=$request->toArray();
                //     $kewangan->yr1_quarters1=$siling_array[1];
                //     $kewangan->yr1_quarters2=$siling_array[2];
                //     $kewangan->yr1_quarters3=$siling_array[3];
                //     $kewangan->yr1_quarters4=$siling_array[4];
                //     $kewangan->yr1_quarters5=$siling_array[5];
                //     $kewangan->yr1_quarters6=$siling_array[6];
                //     $kewangan->yr1_quarters7=$siling_array[7];
                //     $kewangan->yr1_quarters8=$siling_array[8];
                //     $kewangan->yr1_quarters9=$siling_array[9];
                //     $kewangan->yr1_quarters10=$siling_array[10];
                //     $kewangan->yr1_quarters11=$siling_array[11];
                //     $kewangan->yr1_quarters12=$siling_array[12];
                //     $kewangan->yr1_quarters13=$siling_array[13];
                //     $kewangan->yr1_quarters14=$siling_array[14];
                //     $kewangan->yr1_quarters15=$siling_array[15];
                //     $kewangan->yr1_quarters16=$siling_array[16];
                //     $kewangan->yr1_quarters17=$siling_array[17];
                //     $kewangan->yr1_quarters18=$siling_array[18];
                //     $kewangan->yr1_quarters19=$siling_array[19];
                //     $kewangan->yr1_quarters20=$siling_array[20];
                //     $kewangan->yr1_quarters21=$siling_array[21];
                //     $kewangan->yr1_quarters22=$siling_array[22];
                //     $kewangan->yr1_quarters23=$siling_array[23];
                //     $kewangan->yr1_quarters24=$siling_array[24];
                //     $kewangan->yr1_quarters25=$siling_array[25];
                //     $kewangan->yr1_quarters26=$siling_array[26];
                //     $kewangan->yr1_quarters27=$siling_array[27];
                //     $kewangan->yr1_quarters28=$siling_array[28];
                //     $kewangan->yr1_quarters29=$siling_array[29];
                //     $kewangan->yr1_quarters30=$siling_array[30];
                //     $kewangan->yr1_quarters31=$siling_array[31];
                //     $kewangan->yr1_quarters32=$siling_array[32];
                //     $kewangan->yr1_quarters33=$siling_array[33];
                //     $kewangan->yr1_quarters34=$siling_array[34];
                //     $kewangan->yr1_quarters35=$siling_array[35];
                //     $kewangan->yr1_quarters36=$siling_array[36];
                //     $kewangan->yr1_quarters37=$siling_array[37];
                //     $kewangan->yr1_quarters38=$siling_array[38];
                //     $kewangan->yr1_quarters39=$siling_array[39];
                //     $kewangan->yr1_quarters40=$siling_array[40];
                //     $kewangan->dibuat_oleh=$request->user_id;
                //     $kewangan->dikemaskini_oleh=$request->user_id;
                //     $kewangan->dibuat_pada=Carbon::now()->format('Y-m-d H:i:s');
                //     $kewangan->dikemaskini_pada=Carbon::now()->format('Y-m-d H:i:s');
                //     $kewangan->update();

                // }
                // else
                // { 
                    $data=$request->toArray();
                    $kewangan= new KewanganBayaranSukuTahunan;
                    $kewangan->permohonan_projek_id=$id;
                    $kewangan->skop_id=$myArray[$i];
                    $kewangan->yr1_quarters1=$siling_array[1];
                    $kewangan->yr1_quarters2=$siling_array[2];
                    $kewangan->yr1_quarters3=$siling_array[3];
                    $kewangan->yr1_quarters4=$siling_array[4];
                    $kewangan->yr1_quarters5=$siling_array[5];
                    $kewangan->yr1_quarters6=$siling_array[6];
                    $kewangan->yr1_quarters7=$siling_array[7];
                    $kewangan->yr1_quarters8=$siling_array[8];
                    $kewangan->yr1_quarters9=$siling_array[9];
                    $kewangan->yr1_quarters10=$siling_array[10];
                    $kewangan->yr1_quarters11=$siling_array[11];
                    $kewangan->yr1_quarters12=$siling_array[12];
                    $kewangan->yr1_quarters13=$siling_array[13];
                    $kewangan->yr1_quarters14=$siling_array[14];
                    $kewangan->yr1_quarters15=$siling_array[15];
                    $kewangan->yr1_quarters16=$siling_array[16];
                    $kewangan->yr1_quarters17=$siling_array[17];
                    $kewangan->yr1_quarters18=$siling_array[18];
                    $kewangan->yr1_quarters19=$siling_array[19];
                    $kewangan->yr1_quarters20=$siling_array[20];
                    $kewangan->yr1_quarters21=$siling_array[21];
                    $kewangan->yr1_quarters22=$siling_array[22];
                    $kewangan->yr1_quarters23=$siling_array[23];
                    $kewangan->yr1_quarters24=$siling_array[24];
                    $kewangan->yr1_quarters25=$siling_array[25];
                    $kewangan->yr1_quarters26=$siling_array[26];
                    $kewangan->yr1_quarters27=$siling_array[27];
                    $kewangan->yr1_quarters28=$siling_array[28];
                    $kewangan->yr1_quarters29=$siling_array[29];
                    $kewangan->yr1_quarters30=$siling_array[30];
                    $kewangan->yr1_quarters31=$siling_array[31];
                    $kewangan->yr1_quarters32=$siling_array[32];
                    $kewangan->yr1_quarters33=$siling_array[33];
                    $kewangan->yr1_quarters34=$siling_array[34];
                    $kewangan->yr1_quarters35=$siling_array[35];
                    $kewangan->yr1_quarters36=$siling_array[36];
                    $kewangan->yr1_quarters37=$siling_array[37];
                    $kewangan->yr1_quarters38=$siling_array[38];
                    $kewangan->yr1_quarters39=$siling_array[39];
                    $kewangan->yr1_quarters40=$siling_array[40];
                    $kewangan->dibuat_oleh=$request->user_id;
                    $kewangan->dikemaskini_oleh=$request->user_id;
                    $kewangan->dibuat_pada=Carbon::now()->format('Y-m-d H:i:s');
                    $kewangan->dikemaskini_pada=Carbon::now()->format('Y-m-d H:i:s');
                    $kewangan->save();

               // }
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
                    //'validation' => $th->validator->errors()
                ]);
        }  

    }
}
