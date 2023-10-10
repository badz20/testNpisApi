<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Carbon;
use \App\Models\Project;
use \App\Models\projectLog;
use \App\Models\ProjeckCiOutput;
use \App\Models\ProjeckCiOutcome;
use \App\Models\ProjeckCiImpak;
use Illuminate\Support\Facades\DB;
use Jenssegers\Agent\Facades\Agent;


class ProjectCIController extends Controller
{
    //

    public function index($id)
    {
        try {
            //code...
            $result['output'] = ProjeckCiOutput::where('project_id',$id)->where('row_status',1)->get();
            $result['outcome'] = ProjeckCiOutcome::where('project_id',$id)->where('row_status',1)->get();
            $result['impak'] = ProjeckCiImpak::where('project_id',$id)->where('row_status',1)->get();
            $result['project'] = Project::whereId($id)->first();

            
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

    public function store(Request $request)
    {
        // dd($request->all());
        //$project_id = $request->id;

        
        // }
        try {
            $project_id = $request->id;
            
            // foreach ($request->impak as $impak) { 
            //     $data = json_decode($impak, TRUE);
            //     return response()->json([
            //     'code' => '500',
            //     'status' => 'Success',
            //     'data' => floatval(str_replace(",","", $data['kuantiti'])),
            //     // 'data' => $data['kuantiti'],
            // ]);
            // }
            // return response()->json([
            //     'code' => '500',
            //     'status' => 'Success',
            //     'data' => $request->ci,
            // ]);
            ProjeckCiOutput::where('project_id',$request->id)->update(['row_status' => 0]);
            ProjeckCiOutcome::where('project_id',$request->id)->update(['row_status' => 0]);
            ProjeckCiImpak::where('project_id',$request->id)->update(['row_status' => 0]);

            if($request->output){
                ProjeckCiOutput::where('project_id',$request->id)->update(['row_status' => 0]);
                foreach ($request->output as $output) {                 
                    $output = ProjeckCiOutput::create([   
                        'project_id' => $project_id,
                        'keterangan' => $output,
                        'dibuat_oleh' => $request->user_id
                    ]);
                }
            }

            if($request->outcome){
                ProjeckCiOutcome::where('project_id',$request->id)->update(['row_status' => 0]);
                foreach ($request->outcome as $outcome) {                 
                    $output = ProjeckCiOutcome::create([   
                        'project_id' => $project_id,
                        'keterangan' => $outcome,
                        'dibuat_oleh' => $request->user_id
                    ]);
                }
            }

            if($request->impak){
                ProjeckCiImpak::where('project_id',$request->id)->update(['row_status' => 0]);
                foreach ($request->impak as $impak) { 
                    $data = json_decode($impak, TRUE);
                    $output = ProjeckCiImpak::create([   
                        'project_id' => $project_id,
                        'keterangan' => $data['keteranga'],
                        'kuantiti' =>  str_replace(",","", $data['kuantiti']),
                        'nilai' =>  str_replace(",","", $data['nilai']),
                        'penerangan' => $data['penerangan'],
                        'jangka_masa_impak' => str_replace(",","", $data['jangka']),
                        'jumlah_impak' => str_replace(",","", $data['jumlah']),
                        // 'kuantiti' =>  floatval(str_replace(",","", $data['kuantiti'])) ,
                        // 'nilai' =>  floatval(str_replace(",","", $data['nilai'])),
                        // 'penerangan' => $data['penerangan'],
                        // 'jangka_masa_impak' => floatval(str_replace(",","", $data['jangka'])),
                        // 'jumlah_impak' => floatval(str_replace(",","", $data['jumlah'])),
                        'dibuat_oleh' => $request->user_id
                    ]);
                }
            }

            $kewanganDetails = \App\Models\KewanganProjekDetails::where('permohonan_projek_id',$project_id)->first();

            $impak_keseluruhan = 0;
            $ci = 0;
            if(str_replace(",","",$request->total_jumlah) != "") {
                $impak_keseluruhan = str_replace(",","",$request->total_jumlah);
            }

            if(str_replace(",","",$request->ci) != "") {
                $ci = str_replace(",","",$request->ci);
            }

            if($kewanganDetails) {
                \App\Models\KewanganProjekDetails::where('permohonan_projek_id',$request->id)
                ->update([
                    'impak_keseluruhan' => $impak_keseluruhan,
                    'ci' => $ci,
                ]);
            }
            
            $section_name='Creativity index';

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

            
 
            return response()->json([
                    'code' => '200',
                    'status' => 'Success',
                    'data' => 'updated',
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
