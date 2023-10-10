<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use \App\Models\vae;
use \App\Models\User;
use \App\Models\projectLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Jenssegers\Agent\Facades\Agent;



class vae_Controller extends Controller
{
    public function vae_data(Request $request){

    //   print_r("<pre>");
    //   print_r($request->all());exit;
    try{
        $vae_update = vae::where('Permohonan_Projek_id',$request->Permohonan_Projek_id)->where('row_status',1)->first();
        if($vae_update)
        {
            $data=$request->toArray();
            $vae_update->Acquisition_Cost=$data["Acquisition_Cost"];
            $vae_update->Acquisition_Cost_score=$data["Acquisition_Cost_score"];
            $vae_update->Project_Management=$data["Project_Management"];
            $vae_update->Project_Management_score=$data["Project_Management_score"];
            $vae_update->Schedule=$data["Schedule"];
            $vae_update->Schedule_scope=$data["Schedule_scope"];
            $vae_update->Technical_Difficulty=$data["Technical_Difficulty"];
            $vae_update->Technical_Difficulty_score=$data["Technical_Difficulty_score"];
            $vae_update->Operation_Maintainance=$data["Operation_Maintainance"];
            $vae_update->Operation_Maintainance_score=$data["Operation_Maintainance_score"];
            $vae_update->Industry=$data["Industry"];
            $vae_update->Industry_score=$data["Industry_score"];
            $vae_update->ACAT_score=$data["ACAT_score"];
            $vae_update->ACAT=$data["ACAT"];
            $vae_update->proj_viability_1_1_a=$data["proj_viability_1_1_a"];
            $vae_update->proj_viability_1_2_a=$data["proj_viability_1_2_a"];
            $vae_update->brif_2_1_a=$data["brif_2_1_a"];
            $vae_update->brif_2_2_a=$data["brif_2_2_a"];
            $vae_update->brif_2_2_b=$data["brif_2_2_b"];
            $vae_update->brif_2_2_c=$data["brif_2_2_c"];
            $vae_update->brif_2_2_d=$data["brif_2_2_d"];
            $vae_update->brif_2_3_a=$data["brif_2_3_a"];
            $vae_update->brif_2_3_b=$data["brif_2_3_b"];
            $vae_update->brif_2_3_c=$data["brif_2_3_c"];
            $vae_update->brif_2_4_a=$data["brif_2_4_a"];
            $vae_update->operasi=$data["operasi"];
            $vae_update->tanah_3_1_a=$data["tanah_3_1_a"];
            $vae_update->tanah_3_1_b=$data["tanah_3_1_b"];
            $vae_update->tanah_3_2_a=$data["tanah_3_2_a"];
            $vae_update->tanah_3_2_b=$data["tanah_3_2_b"];
            $vae_update->anggaran_4_1_a=$data["anggaran_4_1_a"];
            $vae_update->anggaran_4_2_a=$data["anggaran_4_2_a"];
            $vae_update->anggaran_4_3_a=$data["anggaran_4_3_a"];
            $vae_update->anggaran_4_4_a=$data["anggaran_4_4_a"];
            $vae_update->anggaran_4_5_a=$data["anggaran_4_5_a"];
            $vae_update->anggaran_4_6_a=$data["anggaran_4_6_a"];
            $vae_update->anggaran_4_7_a=$data["anggaran_4_7_a"];
            $vae_update->anggaran_4_8_a=$data["anggaran_4_8_a"];
            $vae_update->Pelaksanaan_5_2_a=$data["Pelaksanaan_5_2_a"];
            $vae_update->Pelaksanaan_5_1_a=$data["Pelaksanaan_5_1_a"];
            $vae_update->GNO_status=$data["GNO_status"];
            $vae_update->dibuat_oleh=$request->user_id;
            $vae_update->dikemaskini_oleh=$request->user_id;
            $vae_update->dibuat_pada=Carbon::now()->format('Y-m-d H:i:s');
            $vae_update->dikemaskini_pada=Carbon::now()->format('Y-m-d H:i:s');
            $vae_update->update();
            if($vae_update->update()=='true'){

                $section_name='VAE';
                $user_data = DB::table('users')
                                ->join('ref_jawatan','ref_jawatan.id', '=','users.jawatan_id')
                                ->select('users.*','ref_jawatan.nama_jawatan')->where('users.id',$data['user_id'])->first();
                $no_rojukan_data = DB::table('projects')->select('no_rujukan')->where('id',$data['Permohonan_Projek_id'])->first();
                $logData=[
                            'user_id' =>$data['user_id'], 
                            'section_name'=>$section_name,   
                            'projek_id'=>$data['Permohonan_Projek_id'],
                            'modul' => 'Permohonan Projek',
                            'user_ic_no' => $user_data->no_ic,
                            'user_jawatan' => $user_data->nama_jawatan,
                            'user_name' => $user_data->name,
                            'no_rujukan' => $no_rojukan_data-> no_rujukan,
                        ];
                DB::connection(env('DB_CONNECTION_AUDIT'))->table('projek_log')->insert($logData);

                return response()->json([
                    'code' => '200',
                    'status' => 'Success',
                    'message' => 'updated'
                ]);
            }
        }
        else
        {

            $data=$request->toArray();
            $vae_data= new vae;
            $vae_data->Permohonan_Projek_id=$data["Permohonan_Projek_id"];
            $vae_data->Acquisition_Cost=$data["Acquisition_Cost"];
            $vae_data->Acquisition_Cost_score=$data["Acquisition_Cost_score"];
            $vae_data->Project_Management=$data["Project_Management"];
            $vae_data->Project_Management_score=$data["Project_Management_score"];
            $vae_data->Schedule=$data["Schedule"];
            $vae_data->Schedule_scope=$data["Schedule_scope"];
            $vae_data->Technical_Difficulty=$data["Technical_Difficulty"];
            $vae_data->Technical_Difficulty_score=$data["Technical_Difficulty_score"];
            $vae_data->Operation_Maintainance=$data["Operation_Maintainance"];
            $vae_data->Operation_Maintainance_score=$data["Operation_Maintainance_score"];
            $vae_data->Industry=$data["Industry"];
            $vae_data->Industry_score=$data["Industry_score"];
            $vae_data->ACAT_score=$data["ACAT_score"];
            $vae_data->ACAT=$data["ACAT"];
            $vae_data->proj_viability_1_1_a=$data["proj_viability_1_1_a"];
            $vae_data->proj_viability_1_2_a=$data["proj_viability_1_2_a"];
            $vae_data->brif_2_1_a=$data["brif_2_1_a"];
            $vae_data->brif_2_2_a=$data["brif_2_2_a"];
            $vae_data->brif_2_2_b=$data["brif_2_2_b"];
            $vae_data->brif_2_2_c=$data["brif_2_2_c"];
            $vae_data->brif_2_2_d=$data["brif_2_2_d"];
            $vae_data->brif_2_3_a=$data["brif_2_3_a"];
            $vae_data->brif_2_3_b=$data["brif_2_3_b"];
            $vae_data->brif_2_3_c=$data["brif_2_3_c"];
            $vae_data->brif_2_4_a=$data["brif_2_4_a"];
            $vae_data->operasi=$data["operasi"];
            $vae_data->tanah_3_1_a=$data["tanah_3_1_a"];
            $vae_data->tanah_3_1_b=$data["tanah_3_1_b"];
            $vae_data->tanah_3_2_a=$data["tanah_3_2_a"];
            $vae_data->tanah_3_2_b=$data["tanah_3_2_b"];
            $vae_data->anggaran_4_1_a=$data["anggaran_4_1_a"];
            $vae_data->anggaran_4_2_a=$data["anggaran_4_2_a"];
            $vae_data->anggaran_4_3_a=$data["anggaran_4_3_a"];
            $vae_data->anggaran_4_4_a=$data["anggaran_4_4_a"];
            $vae_data->anggaran_4_5_a=$data["anggaran_4_5_a"];
            $vae_data->anggaran_4_6_a=$data["anggaran_4_6_a"];
            $vae_data->anggaran_4_7_a=$data["anggaran_4_7_a"];
            $vae_data->anggaran_4_8_a=$data["anggaran_4_8_a"];
            $vae_data->Pelaksanaan_5_2_a=$data["Pelaksanaan_5_2_a"];
            $vae_data->Pelaksanaan_5_1_a=$data["Pelaksanaan_5_1_a"];
            $vae_data->GNO_status=$data["GNO_status"];
            $vae_data->dibuat_oleh=$request->user_id;
            $vae_data->dikemaskini_oleh=$request->user_id;
            $vae_data->dibuat_pada=Carbon::now()->format('Y-m-d H:i:s');
            $vae_data->dikemaskini_pada=Carbon::now()->format('Y-m-d H:i:s');
            $vae_data->save();

            if($vae_data->save()=='true'){
                $section_name='VAE';
                $user_data = DB::table('users')
                                ->join('ref_jawatan','ref_jawatan.id', '=','users.jawatan_id')
                                ->select('users.*','ref_jawatan.nama_jawatan')->where('users.id',$data['user_id'])->first();
                $no_rojukan_data = DB::table('projects')->select('no_rujukan')->where('id',$data['Permohonan_Projek_id'])->first();
                $logData=[
                            'user_id' =>$data['user_id'], 
                            'section_name'=>$section_name,   
                            'projek_id'=>$data['Permohonan_Projek_id'],
                            'modul' => 'Permohonan Projek',
                            'user_ic_no' => $user_data->no_ic,
                            'user_jawatan' => $user_data->nama_jawatan,
                            'user_name' => $user_data->name,
                            'no_rujukan' => $no_rojukan_data-> no_rujukan,
                        ];
                DB::connection(env('DB_CONNECTION_AUDIT'))->table('projek_log')->insert($logData);

                return response()->json([
                    'code' => '200',
                    'status' => 'Success',
                    'message' => 'saved'
                ]);
            }

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

    public function fetch_vae_data($id){
        try{
                $vae_data=vae::where('Permohonan_Projek_id','=',$id)->where('row_status',1)->get();
                return response()->json([
                    'code' => '200',
                    'status' => 'Success',
                    'data'=>$vae_data
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

    public function update_vae_data(Request $request){

        try{
            // var($data);
            $data=$request->toArray();
            // var_dump($data["Acquisition_Cost"]);
            // var_dump(is_null($data["Acquisition_Cost"]));
            // $vae_update=vae::find($data["Permohonan_Projek_id"]);
            $vae_update = vae::where('Permohonan_Projek_id',$data['Permohonan_Projek_id'])->first();
            // dd($vae_update);
            $vae_update->Acquisition_Cost=$data["Acquisition_Cost"];
            $vae_update->Acquisition_Cost_score=$data["Acquisition_Cost_score"];
            $vae_update->Project_Management=$data["Project_Management"];
            $vae_update->Project_Management_score=$data["Project_Management_score"];
            $vae_update->Schedule=$data["Schedule"];
            $vae_update->Schedule_scope=$data["Schedule_scope"];
            $vae_update->Technical_Difficulty=$data["Technical_Difficulty"];
            $vae_update->Technical_Difficulty_score=$data["Technical_Difficulty_score"];
            $vae_update->Operation_Maintainance=$data["Operation_Maintainance"];
            $vae_update->Operation_Maintainance_score=$data["Operation_Maintainance_score"];
            $vae_update->Industry=$data["Industry"];
            $vae_update->Industry_score=$data["Industry_score"];
            $vae_update->ACAT_score=$data["ACAT_score"];
            $vae_update->ACAT=$data["ACAT"];
            $vae_update->dibuat_oleh=$request->user_id;
            $vae_update->dikemaskini_oleh=$request->user_id;
            $vae_update->dibuat_pada=Carbon::now()->format('Y-m-d H:i:s');
            $vae_update->dikemaskini_pada=Carbon::now()->format('Y-m-d H:i:s');
            $vae_update->update();
            if($vae_update->update()=='true'){
                $section_name='VAE';
                $user_data = DB::table('users')
                                    ->join('ref_jawatan','ref_jawatan.id', '=','users.jawatan_id')
                                    ->select('users.*','ref_jawatan.nama_jawatan')->where('users.id',$data['user_id'])->first();
                $no_rojukan_data = DB::table('projects')->select('no_rujukan')->where('id',$data['Permohonan_Projek_id'])->first();
                    $logData=[
                                'user_id' =>$data['user_id'], 
                                'section_name'=>$section_name,   
                                'projek_id'=>$data['Permohonan_Projek_id'],
                                'modul' => 'Permohonan Projek',
                                'user_ic_no' => $user_data->no_ic,
                                'user_jawatan' => $user_data->nama_jawatan,
                                'user_name' => $user_data->name,
                                'no_rujukan' => $no_rojukan_data-> no_rujukan,
                            ];
                    DB::connection(env('DB_CONNECTION_AUDIT'))->table('projek_log')->insert($logData);

                return response()->json([
                    'code' => '200',
                    'status' => 'Success'
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
