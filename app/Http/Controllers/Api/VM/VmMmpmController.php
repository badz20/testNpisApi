<?php

namespace App\Http\Controllers\Api\VM;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use App\Models\VM\VmMmpm;
use Illuminate\Support\Carbon;
use Jenssegers\Agent\Facades\Agent;
use \App\Models\PemantauanProject;



class VmMmpmController extends Controller
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
                    $data['vm'] = VmMmpm::where('type','VE')->where('pp_id',$request->pp_id)->where('row_status',1)->with(['va','project'])->get();
                    $data['vm_data'] = VmMmpm::where('type','VE')->where('pp_id',$request->pp_id)->where('row_status',1)->first();
                }else {
                    $data['vm'] = VmMmpm::where('type','VE')->where('row_status',1)->get();
                    $data['vm_data'] = VmMmpm::where('type','VE')->where('row_status',1)->first();
                }
            }
            else
            {
                if($request->has('pp_id')){
                    $data['vm'] = VmMmpm::where('type','VA')->where('pp_id',$request->pp_id)->where('row_status',1)->with(['va','project'])->get();
                    $data['vm_data'] = VmMmpm::where('type','VA')->where('pp_id',$request->pp_id)->where('row_status',1)->first();

                }else {
                    $data['vm'] = VmMmpm::where('type','VA')->where('row_status',1)->get();
                    $data['vm_data'] = VmMmpm::where('type','VA')->where('row_status',1)->first();
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
        try { 
            $validator = Validator::make($request->all(),[
                'tarikh_cadangan' => ['required', 'string', 'max:255'],
                'tarikh_pra_makmal_sebenar' => ['required', 'string', 'max:255'],
                'keputusan_mesyuarat' => ['required', 'string', 'max:255'],
                'surat_jemputan_mesyuarat' => ['required', 'max:5000','mimes:doc,docs,docx,pdf,zip'],
                'minit_mesyuara' => ['required', 'max:5000','mimes:doc,docs,docx,pdf,zip'],
            ], [
                'required' => 'Medan :attribute diperlukan.',
            ]);

            if(!$validator->fails()) {      
                if($request->id) {
                    $data = VmMmpm::where('id', $request->id)->update([
                        'pra_makmal_sebenar' => $request->tarikh_pra_makmal_sebenar,
                        'keputusan_mesyuarat' => $request->keputusan_mesyuarat,
                        'sjm_file_name' => $request->file('surat_jemputan_mesyuarat')->getClientOriginalName(),
                        'mm_file_name' => $request->file('minit_mesyuara')->getClientOriginalName(),
                        'dikemaskini_oleh' => $request->user_id,
                        'dikemaskini_pada' => Carbon::now()->format('Y-m-d H:i:s'),
                        'type' => $request->type,
                    ]);
                }else {               
                    
                    
                $data = VmMmpm::create([       
                        'pp_id' => $request->pp_id,
                        'cadangan_pra_makmal' => $request->tarikh_cadangan,
                        'pra_makmal_sebenar' => $request->tarikh_pra_makmal_sebenar,
                        'keputusan_mesyuarat' => $request->keputusan_mesyuarat,
                        'sjm_file_name' => $request->file('surat_jemputan_mesyuarat')->getClientOriginalName(),
                        'mm_file_name' => $request->file('minit_mesyuara')->getClientOriginalName(),
                        'row_status' => 1,
                        'is_hidden' => 0,                    
                        'created_at' =>  Carbon::now()->format('Y-m-d H:i:s'),            
                        'dibuat_pada' => Carbon::now()->format('Y-m-d H:i:s'),
                        'dibuat_oleh' => $request->user_id,
                        'dikemaskini_oleh' => $request->user_id,
                        'dikemaskini_pada' => Carbon::now()->format('Y-m-d H:i:s'),
                        'type' => $request->type,
                    ]);
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
                else
                {
                    $va_data= PemantauanProject::where('id',$request->pp_id)->first();
                    $va_data->va_status = $status_new;
                    $va_data->current_status = $status_new;
                    $va_data->update();
                }

                if($request->id) {
                    $data = VmMmpm::where('id', $request->id)->first();
                }

                if($request->file('surat_jemputan_mesyuarat')) {
                    $data->clearMediaCollection('surat_jemputan_mesyuarat');
                    $data->addMedia($request->file('surat_jemputan_mesyuarat'))
                              ->toMediaCollection('surat_jemputan_mesyuarat', 'vm_mmpm');
                }

                if($request->file('minit_mesyuara')){
                    $data->clearMediaCollection('minit_mesyuara');
                    $data->addMedia($request->file('minit_mesyuara'))
                              ->toMediaCollection('minit_mesyuara','vm_mmpm');
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

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(VmMmpm $VmMmpm)
    {
        //
        try { 
            
            return response()->json([
                'code' => '200',
                'status' => 'Success',
                'data' => $VmMmpm,
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
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        //

        try { 
            $data = VmMmpm::where('id', $request->mmpm_id)->update([
                'row_status' => 0,
                'dikemaskini_oleh' => $request->user_id,
                'dikemaskini_pada' => Carbon::now()->format('Y-m-d H:i:s'),
            ]);

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

    public function downloadDoc(Request $request)
    {
        try{
                $id = $request->id;
                $type = $request->type;
                Log::info($request);
                $doc = VmMmpm::whereId($id)->first();
                Log::info($doc);
                if($type == 1) {
                    $mediaItem = $doc->getFirstMedia('surat_jemputan_mesyuarat');
                }else {
                    $mediaItem = $doc->getFirstMedia('minit_mesyuara');
                }
                
                
                return response()->download($mediaItem->getPath(), $mediaItem->file_name);
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

