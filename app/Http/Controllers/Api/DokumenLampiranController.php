<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use \App\Models\DokumenLampiran;
use \App\Models\MaklumatPelakasanaanMakmal;
use \App\Models\vm_tandatangan;
use \App\Models\vr_tandatangan;
use \App\Models\projectLog;
use \App\Models\Penjilidan;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Stroage;
use Illuminate\Support\Facades\Response;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Jenssegers\Agent\Facades\Agent;




class DokumenLampiranController extends Controller
{
    
    public function list($id)
    {
        try {
            
            
            $data_logical = \App\Models\DokumenLampiran::with('media')->where('permohonan_projek_id', $id)  
            ->where('lfm_dokumen','!=', NULL)
            ->where('row_status', 1)
            ->orderBy('id','DESC')->get();

            //print_r($data_logical[0]->getMedia('document_lampiran')->first());exit;


            $data_borang = \App\Models\DokumenLampiran::where('permohonan_projek_id', $id)  
            ->where('perakuan_pengesahan_dokumen','!=', NULL)
            ->where('row_status', 1)
            ->first();

            $data_other = \App\Models\DokumenLampiran::with('media')->where('permohonan_projek_id', $id)  
            ->where('lfm_dokumen','=', NULL)
            ->where('perakuan_pengesahan_dokumen','=', NULL)
            ->where('row_status', 1)
            ->orderBy('id','DESC')->get();


           if(count($data_logical)>0)
           {
                $data['logical'] = $data_logical[0];
                $data['logical_image'] = $data_logical[0]->getMedia('document_lampiran')->first();
           }
           else
           {
                $data['logical'] = [];
                $data['logical_image'] = [];
           }
           if(count($data_other)>0)
           {
                $data['other'] = $data_other[0];
                $data['other_image'] = $data_other[0]->getMedia('document_lampiran')->first();
           }
           else
           {
                $data['other'] = [];
                $data['other_image'] = [];
           }

            $data['borang'] = $data_borang;
                         
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

    public function addDocumentlampiran(Request $request)
    {

        try {
            if($request->activity=='logical')
            {
                $lampiran_data = \App\Models\DokumenLampiran::where('permohonan_projek_id', $request->id)
                                                            ->where('lfm_dokumen','!=', NULL)
                                                            ->first();    
                $data=$request->toArray();  

                $file_name= $request->document; 
            
                $original_file_name = $file_name->getClientOriginalName();
                $extension       = $file_name->getClientOriginalExtension();
                $fileWithoutExt  = str_replace(".","",basename($original_file_name, $extension));  
                $updated_fileName = $fileWithoutExt."_".rand(0,99).".".$extension;

                if($lampiran_data)
                {
                    $lampiran_data->lfm_dokumen_nama = $updated_fileName;
                    $lampiran_data->lfm_dokumen = $request->document;
                    $lampiran_data->dibuat_oleh=$request->user_id;
                    $lampiran_data->dikemaskini_oleh=$request->user_id;
                    $lampiran_data->dibuat_pada=Carbon::now()->format('Y-m-d H:i:s');
                    $lampiran_data->dikemaskini_pada=Carbon::now()->format('Y-m-d H:i:s');
                    $lampiran_data->row_status=1;
                    $lampiran_data->update();

                    if($request->file('document')) {
                        $lampiran_data->clearMediaCollection('document_lampiran');
                        $lampiran_data
                        ->addMedia($request->file('document'))
                        ->toMediaCollection('document_lampiran');
                    }
                    $section_name='Dokumen - add';
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
                        'message' => 'updated',
                        'data'=> $lampiran_data
                    ]);

                }
                else
                {
                    $lamp_data= new DokumenLampiran;  
                    $lamp_data->permohonan_projek_id = $data['id'];
                    $lamp_data->lfm_dokumen_nama = $updated_fileName;
                    $lamp_data->lfm_dokumen = $request->document; 
                    $lamp_data->dibuat_oleh=$request->user_id;
                    $lamp_data->dikemaskini_oleh=$request->user_id;
                    $lamp_data->dibuat_pada=Carbon::now()->format('Y-m-d H:i:s');
                    $lamp_data->dikemaskini_pada=Carbon::now()->format('Y-m-d H:i:s');
                    $lamp_data->row_status=1;
                    $lamp_data->save();

                    if($request->file('document')) {
                        $lamp_data
                        ->addMedia($request->file('document'))
                        ->toMediaCollection('document_lampiran');
                    }
                    $section_name='Dokumen - add';
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
                        'message' => 'saved',
                        'data'=> $lamp_data
                    ]);
                }
            }
            else
            {
                $lampiran_data = \App\Models\DokumenLampiran::where('permohonan_projek_id', $request->id)
                                                            ->where('lain_lain_dokumen1','!=', NULL)
                                                            ->first(); 

                $data=$request->toArray();  

                $file_name= $request->document; 
                $original_file_name = $file_name->getClientOriginalName();
                $extension       = $file_name->getClientOriginalExtension();
                $fileWithoutExt  = str_replace(".","",basename($original_file_name, $extension));  
                $updated_fileName = $fileWithoutExt."_".rand(0,99).".".$extension;
                                                            
                if($lampiran_data)
                {
                    $lampiran_data->lain_lain_dokumen_nama1 = $updated_fileName;
                    $lampiran_data->lain_lain_dokumen1 = $request->document;
                    $lampiran_data->dibuat_oleh=$request->user_id;
                    $lampiran_data->dikemaskini_oleh=$request->user_id;
                    $lampiran_data->dibuat_pada=Carbon::now()->format('Y-m-d H:i:s');
                    $lampiran_data->dikemaskini_pada=Carbon::now()->format('Y-m-d H:i:s');
                    $lampiran_data->row_status=1;
                    $lampiran_data->update();

                    if($request->file('document')) {
                        $lampiran_data->clearMediaCollection('document_lampiran');
                        $lampiran_data
                        ->addMedia($request->file('document'))
                        ->toMediaCollection('document_lampiran');
                    }

                    return response()->json([
                        'code' => '200',
                        'status' => 'Success',
                        'message' => 'updated',
                        'data'=> $lampiran_data
                    ]);
                                                
                }
                else
                {
                    $lamp_data_2= new DokumenLampiran;  
                    $lamp_data_2->permohonan_projek_id = $data['id'];
                    $lamp_data_2->lain_lain_dokumen_nama1 = $updated_fileName;
                    $lamp_data_2->lain_lain_dokumen1 = $request->document;
                    $lamp_data_2->dibuat_oleh=$request->user_id;
                    $lamp_data_2->dikemaskini_oleh=$request->user_id;
                    $lamp_data_2->dibuat_pada=Carbon::now()->format('Y-m-d H:i:s');
                    $lamp_data_2->dikemaskini_pada=Carbon::now()->format('Y-m-d H:i:s');
                    $lamp_data_2->row_status=1;
                    $lamp_data_2->save();

                    if($request->file('document')) {
                        $lamp_data_2
                        ->addMedia($request->file('document'))
                        ->toMediaCollection('document_lampiran');
                    }

                    return response()->json([
                        'code' => '200',
                        'status' => 'Success',
                        'message' => 'saved',
                        'data'=> $lamp_data_2
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

    public function addLainDocument(Request $request)
    { 

        try { 
            $file_name= $request->document; 
            
            $original_file_name = $file_name->getClientOriginalName();
            $extension       = $file_name->getClientOriginalExtension();
            $fileWithoutExt  = str_replace(".","",basename($original_file_name, $extension));  
            $updated_fileName = $fileWithoutExt."_".rand(0,99).".".$extension;

                $lampirian= new DokumenLampiran;  
                $lampirian->permohonan_projek_id = $request->id;
        
                if($request->rowCount==0)
                {
                    $lampirian->lain_lain_dokumen_nama1  = $updated_fileName;
                    $lampirian->lain_lain_dokumen1       = $request->document;
                    $lampirian->lain_katerangan_documen1 = $request->keterangan;
                }
                else if($request->rowCount==1)
                {
                    $lampirian->lain_lain_dokumen_nama2  = $updated_fileName;
                    $lampirian->lain_lain_dokumen2       = $request->document;  
                    $lampirian->lain_katerangan_documen2 = $request->keterangan;  
                }
                else if($request->rowCount==2)
                {
                    $lampirian->lain_lain_dokumen_nama3  = $updated_fileName;
                    $lampirian->lain_lain_dokumen3       = $request->document;
                    $lampirian->lain_katerangan_documen3 = $request->keterangan;
                }
                else if($request->rowCount==3)
                {
                    $lampirian->lain_lain_dokumen_nama4  = $updated_fileName;
                    $lampirian->lain_lain_dokumen4       = $request->document;  
                    $lampirian->lain_katerangan_documen4 = $request->keterangan;
                }
                else
                {
                    $lampirian->lain_lain_dokumen_nama5  = $updated_fileName;
                    $lampirian->lain_lain_dokumen5       = $request->document;    
                    $lampirian->lain_katerangan_documen5 = $request->keterangan;
                }
        
                $lampirian->dibuat_oleh=$request->user_id;
                $lampirian->dikemaskini_oleh=$request->user_id;
                $lampirian->dibuat_pada=Carbon::now()->format('Y-m-d H:i:s');
                $lampirian->dikemaskini_pada=Carbon::now()->format('Y-m-d H:i:s');
                $lampirian->row_status=1;
                $lampirian->save();
        
                if($request->file('document')) {
                    $lampirian
                    ->addMedia($request->file('document'))
                    ->toMediaCollection('document_lampiran');
                }
        
                return response()->json([
                    'code' => '200',
                    'status' => 'Success',
                    'message' => 'saved',
                    'data'=> $lampirian
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


    public function deleteDocumentlampiran(Request $request)
    {
        try {
                $section_name='Dokumen - remove';
                        $user_data = DB::table('users')
                                        ->join('ref_jawatan','ref_jawatan.id', '=','users.jawatan_id')
                                        ->select('users.*','ref_jawatan.nama_jawatan')->where('users.id',$request->user_id)->first();
                        $no_rojukan_data = DB::table('projects')->select('no_rujukan')->where('id',$request->project_id)->first();
                        $logData=[
                                    'user_id' =>$request->user_id, 
                                    'section_name'=>$section_name,   
                                    'projek_id'=>$request->project_id,
                                    'modul' => 'Permohonan Projek',
                                    'user_ic_no' => $user_data->no_ic,
                                    'user_jawatan' => $user_data->nama_jawatan,
                                    'user_name' => $user_data->name,
                                    'no_rujukan' => $no_rojukan_data-> no_rujukan,
                                ];
                        DB::connection(env('DB_CONNECTION_AUDIT'))->table('projek_log')->insert($logData);

                return DokumenLampiran::where('id', $request->id)->update([
                    'dikemaskini_oleh' => $request->user_id,
                    'dikemaskini_pada' => Carbon::now()->format('Y-m-d H:i:s'),
                    'dibuat_oleh' => $request->user_id,
                    'dibuat_pada' => Carbon::now()->format('Y-m-d H:i:s'),
                    'row_status'=>0
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

    function downloadImg(Request $request ,Media $mediaItem){
        
        try {
                $id = $request->id;
                $doc = DokumenLampiran::where('id','=',$id)->first();
                $mediaItem = $doc->getFirstMedia('document_lampiran');
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

    public function kemukafileDownload(Request $request ,Media $mediaItem){
        try {
                $id = $request->id;
                $doc = MaklumatPelakasanaanMakmal::where('id','=',$id)->first();
                // dd($doc);
                $mediaItem = $doc->getFirstMedia('kemuka_file_name');
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
    public function terimafileDownload(Request $request ,Media $mediaItem){
        try {
                $id = $request->id;
                $doc = MaklumatPelakasanaanMakmal::where('id','=',$id)->first();
                // dd($doc);
                $mediaItem = $doc->getFirstMedia('terima_file_name');
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
    public function docFormat(){
        try{
            $image='TEMPLATE-LFM.xls';
            $imagePath =storage_path($image);
            $headers = array('Content-Type'=> '	application/vnd.ms-excel');
            return response()->download($imagePath, $image, $headers);
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
    public function previewfile(Request $request ,Media $mediaItem){
        try{
            $id = $request->id;
            $doc = vm_tandatangan::where('id','=',$id)->first();
            // dd($doc);
            $mediaItem = $doc->getFirstMedia('terima_file_name');
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

    public function vr_filedownload(Request $request ,Media $mediaItem){
        try{
            $id = $request->id;
            $doc = vr_tandatangan::where('id','=',$id)->first();
            // dd($doc);
            $mediaItem = $doc->getFirstMedia('terima_file_name');
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

    public function previewPenjilidanfile(Request $request ,Media $mediaItem){
        try{
            $id = $request->id;
            $doc = Penjilidan::where('id','=',$id)->where('type',$request->type)->where('peranan',$request->pearanan)->with('media')->first();
            //dd($doc);
            $mediaItem = $doc->getMedia('Kemukakan')->first(); //dd($mediaItem);
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
