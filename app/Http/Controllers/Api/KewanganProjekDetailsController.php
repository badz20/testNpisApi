<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use \App\Models\Project;
use \App\Models\projectLog;
use \APP\Models\KewanganProjekDetails;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Jenssegers\Agent\Facades\Agent;


class KewanganProjekDetailsController extends Controller
{
    public function list(Request $request)
    {
        try {
            if($request->id){
                $data = \App\Models\KewanganProjekDetails::where('permohonan_projek_id','=',$request->id)->with('komponen')->first();
            }else {
                $data = \App\Models\KewanganProjekDetails::get();
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

    public function listrollingplan(Request $request)
    {
        try {
            if($request->id){
                $data = \App\Models\Project::where('id', $request->id)->with('rollingPlan')->first();

                
            }
           
            
            return response()->json([
                'code' => '200',
                'status' => 'Successs',
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
        //$project_id=$data['id'];
        //$session_id = session()->getId();

        $rmk_data = \App\Models\KewanganProjekDetails::where('permohonan_projek_id', $request->id)->first();     
                    if($rmk_data)
                    {
                        $rmkobbpage = $this->updateKewanganProjekDetail($request->all(),$request->id);
                        $rmkobbpage = $this->updateKosProjek($request->all(),$request->id);
                        
                    }
                    else{
                        $rmkobbpage= new KewanganProjekDetails;
                        $rmkobbpage->permohonan_projek_id=$data['permohonan_projek_id'];
                        $rmkobbpage->totalkos=$data['totalkos'];
                        $rmkobbpage->totalkos_perunding=$data['totalkos_perunding'];
                        $rmkobbpage->Komponen_id=$data['Komponen_id'];
                        $rmkobbpage->Siling_Dimohon=$data['Siling_Dimohon'];
                        $rmkobbpage->Siling_Bayangan=$data['Siling_Bayangan'];
                        $rmkobbpage->kos_keseluruhan_oe=$data['kos_keseluruhan_oe'];
                        $rmkobbpage->kos_keseluruhan_oe=$data['kos_keseluruhan'];
                        $rmkobbpage->imbuhan_balik=$data['imbuhan_balik'];
                        // $rmkobbpage->sst_tax=$data['sst_tax'];  
                        // $rmkobbpage->temp_sst_tax=$data['temp_sst_tax']; 
                        $rmkobbpage->jumlahkos=$data['jumlahkos']; 
                        $rmkobbpage->temp_jumlahkos=$data['temp_jumlahkos']; 
                        
                        $rmkobbpage->anggaran_mainworks=$data['anggaran_mainworks'];
                        $rmkobbpage->P_max=$data['P_max'];  
                        $rmkobbpage->P_min=$data['P_min']; 
                        $rmkobbpage->P_avg=$data['P_avg']; 
                        $rmkobbpage->P_selection=$data['P_selection'];
                        $rmkobbpage->design_fee=$data['design_fee']; 

                        $rmkobbpage->imbuhanbalik_piawai=$data['imbuhanbalik_piawai']; 
                        $rmkobbpage->cukai_sst=$data['cukai_sst']; 
                        $rmkobbpage->anggarankos_piawai=$data['anggarankos_piawai'];
                        
                        $rmkobbpage->yuran_perunding_kos=$data['yuran_perunding_kos'];
                        $rmkobbpage->yuran_professional=$data['yuran_professional'];
                        $rmkobbpage->yuran_subprofessional=$data['yuran_subprofessional'];  
                        $rmkobbpage->yuran_imbuhanbalik=$data['yuran_imbuhanbalik']; 
                        $rmkobbpage->yuran_ssttax=$data['yuran_ssttax']; 
                        $rmkobbpage->yuran_anggaran=$data['yuran_anggaran']; 

                        $rmkobbpage->yuran_perunding_kos=$data['yuran_perunding_kos_tapak'];                        
                        $rmkobbpage->yuran_professional=$data['yuran_professional_tapak'];
                        $rmkobbpage->yuran_subprofessional=$data['yuran_subprofessional_tapak'];  
                        $rmkobbpage->yuran_imbuhanbalik=$data['yuran_imbuhanbalik_tapak']; 
                        $rmkobbpage->yuran_ssttax=$data['yuran_ssttax_tapak']; 
                        $rmkobbpage->yuran_anggaran=$data['yuran_anggaran_tapak'];

                        
                        $rmkobbpage->dibuat_oleh=$request->user_id;
                        $rmkobbpage->dikemaskini_oleh=$request->user_id;
                        $rmkobbpage->dibuat_pada=Carbon::now()->format('Y-m-d H:i:s');
                        $rmkobbpage->dikemaskini_pada=Carbon::now()->format('Y-m-d H:i:s');
                        $rmkobbpage->save();
                        $rmkobbpage = $this->updateKosProjek($request->all(),$request->id);
                        
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

    protected function updateKewanganProjekDetail(array $data, $id)
    {        
        try{
            return KewanganProjekDetails::where('permohonan_projek_id', $id)->update([
                'Komponen_id' => $data['Komponen_id'],
                'totalkos' => $data['totalkos'], 
                'totalkos_perunding' => $data['totalkos_perunding'],            
                'Siling_Dimohon' => $data['Siling_Dimohon'],            
                'Siling_Bayangan' => $data['Siling_Bayangan'],
                'kos_keseluruhan_oe' => $data['kos_keseluruhan_oe'],
                'kos_keseluruhan' => $data['kos_keseluruhan'],
                'imbuhan_balik' => $data['imbuhan_balik'],
                // 'sst_tax' => $data['sst_tax'], 
                'temp_jumlahkos' => $data['temp_jumlahkos'],
                'jumlahkos' => $data['jumlahkos'],
                // 'temp_sst_tax' => $data['temp_sst_tax'],                
                'anggaran_mainworks' => $data['anggaran_mainworks'],
                'P_max' => $data['P_max'],  
                'P_min' => $data['P_min'], 
                'P_avg' => $data['P_avg'], 
                'P_selection' => $data['P_selection'],
                'design_fee' => $data['design_fee'],
                'imbuhanbalik_piawai' => $data['imbuhanbalik_piawai'], 
                'cukai_sst' => $data['cukai_sst'], 
                'anggarankos_piawai' => $data['anggarankos_piawai'],
                'yuran_perunding_kos' => $data['yuran_perunding_kos'],
                'yuran_professional' => $data['yuran_professional'],
                'yuran_subprofessional' => $data['yuran_subprofessional'],  
                'yuran_imbuhanbalik' => $data['yuran_imbuhanbalik'], 
                'yuran_ssttax' => $data['yuran_ssttax'], 
                'yuran_anggaran' => $data['yuran_anggaran'],
                
                'yuran_perunding_kos_tapak' => $data['yuran_perunding_kos_tapak'],
                'yuran_professional_tapak' => $data['yuran_professional_tapak'],
                'yuran_subprofessional_tapak' => $data['yuran_subprofessional_tapak'],  
                'yuran_imbuhanbalik_tapak' => $data['yuran_imbuhanbalik_tapak'], 
                'yuran_ssttax_tapak' => $data['yuran_ssttax_tapak'], 
                'yuran_anggaran_tapak' => $data['yuran_anggaran_tapak'],

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

    protected function updateKosProjek(array $data, $id)
    {

        try{           
           
            return Project::where('id', $id)->update([
                
                'kos_projeck' => $data['totalkos'],            

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
