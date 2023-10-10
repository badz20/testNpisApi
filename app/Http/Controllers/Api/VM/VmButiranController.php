<?php

namespace App\Http\Controllers\Api\VM;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use App\Models\VM\VmButiran;
use App\Models\VM\VmButiranFasilitator;
use Illuminate\Support\Carbon;
use Jenssegers\Agent\Facades\Agent;
use App\Models\PemantauanProject;



class VmButiranController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //
        try {
            //code...
            if($request->type=='VE')
            {
                if($request->has('pp_id')){
                    $data = VmButiran::where('type','VE')->where('pp_id',$request->pp_id)->where('row_status',1)
                            ->with(['fasilitators','fasilitators.fasilitator','fasilitators.fasilitator.gredJawatan','fasilitators.fasilitator.bahagian'])
                            ->get();
                }else {
                    $data = VmButiran::where('type','VE')->where('row_status',1)->get();
                }
            }
            elseif($request->type=='VR'){
                if($request->has('pp_id')){
                    $data = VmButiran::where('type','VR')->where('pp_id',$request->pp_id)->where('row_status',1)
                            ->with(['fasilitators','fasilitators.fasilitator','fasilitators.fasilitator.gredJawatan','fasilitators.fasilitator.bahagian'])
                            ->get();
                }else {
                    $data = VmButiran::where('type','VR')->where('row_status',1)->get();
                }
            }
            else
            {
                if($request->has('pp_id')){
                    $data = VmButiran::where('type','VA')->where('pp_id',$request->pp_id)->where('row_status',1)
                            ->with(['fasilitators','fasilitators.fasilitator','fasilitators.fasilitator.gredJawatan','fasilitators.fasilitator.bahagian'])
                            ->get();
                }else {
                    $data = VmButiran::where('type','VA')->where('row_status',1)->get();
                }
            }
            //$data = refNegeri::with('updatedBy')->get();
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

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
        // dd($request);
        try { 
            
            $validator = Validator::make($request->all(),[
                'cadangan_makmal' => ['required', 'string', 'max:255'],
                'tahun_makmal' => ['required', 'string', 'max:255'],
                'lawatan_tapak' => ['required', 'string', 'max:255'],
                'makmal_sebenar' => ['required', 'string', 'max:255'],
                'negeri' => ['required', 'string', 'max:255'],
                'user_id' => ['required', 'string', 'max:255'],
                'pp_id' => ['required', 'string', 'max:255'],
                'fasilitator' => ['required', 'array', 'max:255'],
                'kos_pelakas_selepas_makmal' => ['required', 'string', 'max:255'],
            ]);
            
            if(!$validator->fails()) {      
                if($request->id) {
                    // $data = VmButiran::where('id', $request->id)->update([
                    //     'pra_makmal_sebenar' => $request->keputusan_mesyuarat,
                    //     'keputusan_mesyuarat' => $request->keputusan_mesyuarat,
                    //     'sjm_file_name' => $request->file('surat_jemputan_mesyuarat')->getClientOriginalName(),
                    //     'mm_file_name' => $request->file('minit_mesyuara')->getClientOriginalName(),
                    //     'dikemaskini_oleh' => $request->user_id,
                    //     'dikemaskini_pada' => Carbon::now()->format('Y-m-d H:i:s'),
                    // ]);
                }else { 
                    if($request->CurrentDate && $request->type=='VR'){
                        $currentYear=$request->CurrentDate;
                    }              
                    else{
                        $currentYear=$request->tahun_makmal;
                    }
                    
                    $data = VmButiran::create([       
                            'pp_id' => $request->pp_id,
                            'cadangan_makmal' => $request->cadangan_makmal,
                            'negeri' => $request->negeri,
                            'makmal_sebenar' => $request->makmal_sebenar,
                            'lawatan_tapak' => $request->lawatan_tapak,
                            'mesyuarat_date' => $request->mesyuarat_date,
                            'kos_sebelum_makmal' => $request->kos_sebelum_makmal,
                            'kos_pelakas_selepas_makmal' => $request->kos_pelakas_selepas_makmal,
                            'tahun_makmal' => $request->tahun_makmal,
                            'row_status' => 1,
                            'is_hidden' => 0,                    
                            'created_at' =>  Carbon::now()->format('Y-m-d H:i:s'),            
                            'dibuat_pada' => Carbon::now()->format('Y-m-d H:i:s'),
                            'dibuat_oleh' => $request->user_id,
                            'dikemaskini_oleh' => $request->user_id,
                            'dikemaskini_pada' => Carbon::now()->format('Y-m-d H:i:s'),
                            'type' => $request->type,
                        ]);
                    $this->createFasilitators($request,$data);
                
                }

                $user = \App\Models\User::where('id',$request->user_id)->with('bahagian')->first(); //dd($user);
                if($user->bahagian->acym == 'BKOR' || $user->bahagian->acym == 'BPK') 
                {
                    $status_new=32;
                } else {
                    $status_new=24;
                }

                if($request->type=='VE')
                {
                    $ve_data= PemantauanProject::where('id',$request->pp_id)->first();
                    $ve_data->ve_status = $status_new;
                    $ve_data->current_status = $status_new;
                    $ve_data->update();
                }
                else if($request->type=='VR')
                {
                    $vr_data= PemantauanProject::where('id',$request->pp_id)->first();
                    $vr_data->vr_status = 24;
                    $vr_data->current_status = 24;
                    $vr_data->update();
                }
                else
                {
                    $va_data= PemantauanProject::where('id',$request->pp_id)->first();
                    $va_data->va_status = $status_new;
                    $va_data->current_status = $status_new;
                    $va_data->update();
                }
                
                return response()->json([
                    'code' => '200',
                    'status' => 'Sucess',
                    'data' => $data,
                ]);
            }else {                
                return response()->json([
                    'code' => '422',
                    'status' => 'Unprocessable Entity',
                    'data' => $validator->errors(),
                ]);
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

    private function createFasilitators($request, $butiran)
    {
        foreach($request->fasilitator as $fasilitator){
            $fasilitator_json = json_decode($fasilitator,TRUE);
            $data = VmButiranFasilitator::create([       
                'pp_id' => $request->pp_id,
                'butiran_id' => $butiran->id,
                'fasilitator_id' => $fasilitator_json['fas_value'],
                'fasilitator_type' => $fasilitator_json['fas_type'],
                'row_status' => 1,
                'is_hidden' => 0,                    
                'created_at' =>  Carbon::now()->format('Y-m-d H:i:s'),            
                'dibuat_pada' => Carbon::now()->format('Y-m-d H:i:s'),
                'dibuat_oleh' => $request->user_id,
                'dikemaskini_oleh' => $request->user_id,
                'dikemaskini_pada' => Carbon::now()->format('Y-m-d H:i:s'),
            ]);
        }
    }
}
