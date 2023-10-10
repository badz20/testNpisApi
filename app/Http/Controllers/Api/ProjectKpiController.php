<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use \App\Models\ProjectKpi;
use \App\Models\OutputUnit;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Jenssegers\Agent\Facades\Agent;


class ProjectKpiController extends Controller
{
    public function listProjectKpi($id)
    {
        try {
            $project_kpi = \App\Models\ProjectKpi::
            with(['OutputUnit'=> function ($query) {
                $query->select('id', 'nama_unit');
            }])
            ->where('project_id', $id)        
            ->where('row_status', 1)
            ->get();

            
            return response()->json([
                'code' => '200',
                'status' => 'Success',
                'data' => $project_kpi,
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

    public function addProjectKpi(Request $request){
        
        try{

                $data=$request->toArray();
                $kpi_data= new ProjectKpi;
                $kpi_data->project_id=$data["project_id"];
                $kpi_data->kuantiti=$data["kuantiti"];
                $kpi_data->unit=$data["unit"];
                $kpi_data->penerangan=$data["penerangan"];
                $kpi_data->yr_1=$data["yr_1"];
                $kpi_data->yr_2=$data["yr_2"];
                $kpi_data->yr_3=$data["yr_3"];
                $kpi_data->yr_4=$data["yr_4"];
                $kpi_data->yr_5=$data["yr_5"];
                $kpi_data->yr_6=$data["yr_6"];
                $kpi_data->yr_7=$data["yr_7"];
                $kpi_data->yr_8=$data["yr_8"];
                $kpi_data->yr_9=$data["yr_9"];
                $kpi_data->yr_10=$data["yr_10"];
                $kpi_data->dibuat_oleh=$request->user_id;
                $kpi_data->dikemaskini_oleh=$request->user_id;
                $kpi_data->dibuat_pada=Carbon::now()->format('Y-m-d H:i:s');
                $kpi_data->dikemaskini_pada=Carbon::now()->format('Y-m-d H:i:s');
                $kpi_data->save();
    
                if($kpi_data->save()=='true'){
                    return response()->json([
                        'code' => '200',
                        'status' => 'Success',
                        'message' => 'saved'
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

    public function deleteProjectKpi(Request $request)
    {
        // print_r($request->kpi_id);exit;
        try {
            $project_kpi = \App\Models\ProjectKpi::where('id',$request->kpi_id)->update(['row_status' => '0']);

            return response()->json([
                'code' => '200',
                'status' => 'Sucess',
                'data' => $project_kpi,
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

    public function getProjectKpi(Request $request)
    {
        try {
            $data['project_kpi'] = \App\Models\ProjectKpi::where('id',$request->id)->get();
            $data['kpi_unit'] =  \App\Models\OutputUnit::where('IsActive','=',1)->get();
            $data['project_data'] = \App\Models\Project::where('id',$request->project_id)->get();


            return response()->json([
                'code' => '200',
                'status' => 'Sucess',
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

    public function updateProjectKpi(Request $request)
    {
        try {
                $kpi_data = \App\Models\ProjectKpi::where('id',$request->id)->first();

                $data=$request->toArray();

                $kpi_data->project_id=$data["project_id"];
                $kpi_data->kuantiti=$data["kuantiti"];
                $kpi_data->unit=$data["unit"];
                $kpi_data->penerangan=$data["penerangan"];
                $kpi_data->yr_1=$data["yr_1"];
                $kpi_data->yr_2=$data["yr_2"];
                $kpi_data->yr_3=$data["yr_3"];
                $kpi_data->yr_4=$data["yr_4"];
                $kpi_data->yr_5=$data["yr_5"];
                $kpi_data->yr_6=$data["yr_6"];
                $kpi_data->yr_7=$data["yr_7"];
                $kpi_data->yr_8=$data["yr_8"];
                $kpi_data->yr_9=$data["yr_9"];
                $kpi_data->yr_10=$data["yr_10"];
                $kpi_data->dikemaskini_oleh=$data['user_id'];
                $kpi_data->dikemaskini_pada=Carbon::now()->format('Y-m-d H:i:s');
                $kpi_data->update();

                return response()->json([
                    'code' => '200',
                    'status' => 'Sucess',
                    'data' => $kpi_data,
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
