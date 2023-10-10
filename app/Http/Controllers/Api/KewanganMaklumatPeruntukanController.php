<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use \App\Models\Project;
use \APP\Models\KewanganMaklumatPeruntukan;
use \APP\Models\KewanganBelanja;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Jenssegers\Agent\Facades\Agent;


class KewanganMaklumatPeruntukanController extends Controller
{
    public function getMaklumatPeruntukan($id)
    {
        try {
            //code...
            $result['project'] = Project::whereId($id)->with(['skopProjects'])->first();
            $result['peruntukan'] = \App\Models\KewanganMaklumatPeruntukan::where('permohonan_projek_id', $id)
                                                                 ->get();

            $result['belanja'] = \App\Models\KewanganBelanja::where('permohonan_projek_id','=',$id)->where('row_status','=',1)->get();
        
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

    public function addMaklumatPeruntukan(Request $request,$id)
    { 
        //print_r($request->all());exit;
        try
        {
            $myString = $request->perkra_id;
            $myArray = explode(',', $myString);

            for($i=0;$i<3;$i++)
            { //print_r($i);
                 $maklumat_array = explode(',', $request->myTableArrayMaklumat[$i]);  //print_r($siling_array);
                 $jumlah_array = explode(',', $request->arrayOfJumlahMaklumat[$i]);  //print_r($jumlah_array);//exit;

                $kewangan = \App\Models\KewanganMaklumatPeruntukan::where('permohonan_projek_id', $id)        
                                                            ->where('perkra_id', $myArray[$i])->first();
                
                
                if($kewangan)
                { 
                    $data=$request->toArray();
                    $kewangan->year1=$maklumat_array[0];
                    $kewangan->year2=$maklumat_array[1];
                    $kewangan->year3=$maklumat_array[2];
                    $kewangan->year4=$maklumat_array[3];
                    $kewangan->year5=$maklumat_array[4];
                    $kewangan->year6=$maklumat_array[5];
                    $kewangan->year7=$maklumat_array[6];
                    $kewangan->year8=$maklumat_array[7];
                    $kewangan->year9=$maklumat_array[8];
                    $kewangan->year10=$maklumat_array[9];
                    $kewangan->jumlah_kos=$jumlah_array[0];
                    $kewangan->dibuat_oleh=$request->user_id;
                    $kewangan->dikemaskini_oleh=$request->user_id;
                    $kewangan->dibuat_pada=Carbon::now()->format('Y-m-d H:i:s');
                    $kewangan->dikemaskini_pada=Carbon::now()->format('Y-m-d H:i:s');
                    $kewangan->update();

                }
                else
                { 
                    $data=$request->toArray();
                    $kewangan= new KewanganMaklumatPeruntukan;
                    $kewangan->permohonan_projek_id=$id;
                    $kewangan->perkra_id=$myArray[$i];
                    $kewangan->year1=$maklumat_array[0];
                    $kewangan->year2=$maklumat_array[1];
                    $kewangan->year3=$maklumat_array[2];
                    $kewangan->year4=$maklumat_array[3];
                    $kewangan->year5=$maklumat_array[4];
                    $kewangan->year6=$maklumat_array[5];
                    $kewangan->year7=$maklumat_array[6];
                    $kewangan->year8=$maklumat_array[7];
                    $kewangan->year9=$maklumat_array[8];
                    $kewangan->year10=$maklumat_array[9];
                    $kewangan->jumlah_kos=$jumlah_array[0];
                    $kewangan->dibuat_oleh=$request->user_id;
                    $kewangan->dikemaskini_oleh=$request->user_id;
                    $kewangan->dibuat_pada=Carbon::now()->format('Y-m-d H:i:s');
                    $kewangan->dikemaskini_pada=Carbon::now()->format('Y-m-d H:i:s');
                    $kewangan->save();

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

    public function addMaklumatBelenja(Request $request,$id)
    { 
        //print_r($request->all());exit;
        try
        {
            
            
            //$kewangan = \App\Models\KewanganBelanja::where('permohonan_projek_id', $id)->delete();
            
            $kewanganbelanja = $this->updateBelanja($request->all(),$request->id);
            $j = sizeof($request->myTableArrayBelanja);
            for($i=0;$i<$j-1;$i++)
            { 
                 $maklumat_array = explode(',', $request->myTableArrayBelanja[$i]);  //print_r($siling_array);
                 $jumlah_array = explode(',', $request->arrayOfJumlahBelanja[$i]);  //print_r($jumlah_array);//exit;  
                
                
                
                    $data=$request->toArray();
                    $kewangan= new KewanganBelanja;
                    $kewangan->permohonan_projek_id=$id;
                    $kewangan->Kategori_nama=$maklumat_array[0];
                    $kewangan->kategori_1_yr=$maklumat_array[1];
                    $kewangan->kategori_2_yr=$maklumat_array[2];
                    $kewangan->kategori_3_yr=$maklumat_array[3];
                    $kewangan->kategori_4_yr=$maklumat_array[4];
                    $kewangan->kategori_5_yr=$maklumat_array[5];
                    $kewangan->kategori_6_yr=$maklumat_array[6];
                    $kewangan->kategori_7_yr=$maklumat_array[7];
                    $kewangan->kategori_8_yr=$maklumat_array[8];
                    $kewangan->kategori_9_yr=$maklumat_array[9];
                    $kewangan->kategori_10_yr=$maklumat_array[10];
                                      
                    $kewangan->jumlah_kos=$jumlah_array[0];
                    $kewangan->dibuat_oleh=$request->user_id;
                    $kewangan->dikemaskini_oleh=$request->user_id;
                    $kewangan->dibuat_pada=Carbon::now()->format('Y-m-d H:i:s');
                    $kewangan->dikemaskini_pada=Carbon::now()->format('Y-m-d H:i:s');
                    $kewangan->save();

                
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

    protected function updateBelanja(array $data, $id)
    {   try
        {
            return \App\Models\KewanganBelanja::where('permohonan_projek_id', $id)->update([
                'row_status' => 0,
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