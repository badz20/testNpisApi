<?php

namespace App\Http\Controllers\Api\VM;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Models\PemantauanProject;
use Illuminate\Validation\Rule;
use App\Models\VM\VmUlasan;
use App\Models\VM\VmUlasanHistory;
use App\Models\VM\VmMakmalKajianNilai;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Jenssegers\Agent\Facades\Agent;


class UlasanController extends Controller
{
    //
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
                    $data['Ulasan'] = VmUlasan::where('type','VE')->where('pp_id',$request->pp_id)->with('va')->get();
                    $data['UlasanHistory'] = VmUlasanHistory::where('type','VE')->where('pp_id',$request->pp_id)->with('va')->get();
                    $va = VmMakmalKajianNilai::where('type','VE')->where('pp_id',$request->pp_id)->first();
                    $project = PemantauanProject::whereId($request->pp_id)->first();
                    if($va) {
                        $memo_media = $va->getFirstMedia('memo');
                        $data['memo'] = $memo_media;
                        $data['va'] = $va;
                    }else {
                        $data['memo'] = [];
                        $data['va'] = [];
                    }
    
                    $data['project'] = $project;
                    
                    
                }else {
                    $data['Ulasan'] = VmUlasan::where('type','VE')->get();
                    $data['UlasanHistory'] = VmUlasanHistory::where('type','VE')->get();
                }
            }
            else
            {
                if($request->has('pp_id')){
                    $data['Ulasan'] = VmUlasan::where('type','VA')->where('pp_id',$request->pp_id)->with('va')->get();
                    $data['UlasanHistory'] = VmUlasanHistory::where('type','VA')->where('pp_id',$request->pp_id)->with('va')->get();
                    $va = VmMakmalKajianNilai::where('type','VA')->where('pp_id',$request->pp_id)->first();
                    $project = PemantauanProject::whereId($request->pp_id)->first();
                    if($va) {
                        $memo_media = $va->getFirstMedia('memo');
                        $data['memo'] = $memo_media;
                        $data['va'] = $va;
                    }else {
                        $data['memo'] = [];
                        $data['va'] = [];
                    }
    
                    $data['project'] = $project;
                    
                    
                }else {
                    $data['Ulasan'] = VmUlasan::where('type','VA')->get();
                    $data['UlasanHistory'] = VmUlasanHistory::where('type','VA')->get();
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
           // Log::info($request);
            $validator = Validator::make($request->all(),[
                'pp_id' => ['required', 'string', 'max:255'],
                'user_id' => ['required', 'string', 'max:255'],
                'memo' => Rule::requiredIf($request->existing_file == 'false'),
                // 'ulasan' => ['required', 'array', 'max:255'],
            ], [
                'memo.required' => 'Sila muat naik memo.',
            ]);

            if(!$validator->fails()) {
                $filename = '';
                if($request->file('memo')){
                    $filename = $request->file('memo')->getClientOriginalName();
                }else {
                    $filename = $request->existing_filename;
                }

                $permantuan_project = PemantauanProject::whereId($request->pp_id)->first();

                if($request->type=='VE')
                {
                    if($permantuan_project->status_perlaksanaan == $request->status && $request->status == '32') {
                        if($request->has('ulasan')) {
                            $this->createUlasan($request);
                        }
                    }else {                    
                        if($request->has('ulasan')) {
                            if(trim($request->status) == '33') {
                                $this->revisionUlasan($request);
                                if($permantuan_project->status_perlaksanaan != $request->status) {
                                    $this->createUlasanHistory($request);
                                }
                                
                            }

                            if(trim($request->status) == '35') {
                                $this->revisionUlasan($request);
                                $this->updateUlasanHistory($request);
                            }

                            if(trim($request->status) == '34') {
                                $this->revisionUlasan($request);
                                if($permantuan_project->status_perlaksanaan != $request->status) {
                                    $this->createUlasanHistory($request);
                                }
                                
                            }
                        }

                        if($request->status !=36)
                        {
                            PemantauanProject::where('id', $request->pp_id)->update([
                                'status_perlaksanaan' => $request->status,
                                //'current_status' => 0
                            ]);
                        }
                        else
                        {
                            PemantauanProject::where('id', $request->pp_id)->update([
                                'status_perlaksanaan' => $request->status,
                                'current_status' => 24
                            ]);

                        }
                    }
                }
                else
                { 
                    if($permantuan_project->status_perlaksanaan == $request->status && $request->status == '27') { 
                        if($request->has('ulasan')) {
                            $this->createUlasan($request);
                        }
                    }else {                    
                        if($request->has('ulasan')) {
                            if(trim($request->status) == '28') { 
                                $this->revisionUlasan($request);
                                if($permantuan_project->status_perlaksanaan != $request->status) {
                                    $this->createUlasanHistory($request);
                                }
                                
                            }

                            if(trim($request->status) == '30') { 
                                $this->revisionUlasan($request);
                                $this->updateUlasanHistory($request);
                            }

                            if(trim($request->status) == '29') { 
                                $this->revisionUlasan($request);
                                if($permantuan_project->status_perlaksanaan != $request->status) {
                                    $this->createUlasanHistory($request);
                                }
                                
                            }
                        }

                        if($request->status !=28)
                        {
                            PemantauanProject::where('id', $request->pp_id)->update([
                                'status_perlaksanaan' => $request->status,
                                //'current_status' => 0
                            ]);
                        }
                        else
                        {
                            PemantauanProject::where('id', $request->pp_id)->update([
                                'status_perlaksanaan' => $request->status,
                                'current_status' => 24
                            ]);

                        }
                        
                    }

                }

                
                if($request->pp_id) {
                    if($request->type=='VE')
                    {
                        $data = VmMakmalKajianNilai::where('type','VE')->where('pp_id', $request->pp_id)->first();
                    }
                    else
                    {
                        $data = VmMakmalKajianNilai::where('type','VA')->where('pp_id', $request->pp_id)->first();
                    }
                }

                if($request->file('memo')) {
                    if($data->getMedia('memo')->last()) {
                        $data->getMedia('memo')->last()->delete();
                    }
                    // $data->clearMediaCollection('memo');
                    $data->addMedia($request->file('memo'))
                              ->toMediaCollection('memo','vm');
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

    private function submitUlasan($request)
    {

    }

    private function revisionSubmitUlasan($request)
    {

    }
    
    private function revisionUlasan($request)
    { 
        try{
            if($request->type=='VE')
            {
                $va = VmMakmalKajianNilai::where('type','VE')->where('pp_id',$request->pp_id)->first();
                $type_data='VE';
            }
            else
            {
                $va = VmMakmalKajianNilai::where('type','VA')->where('pp_id',$request->pp_id)->first();
                $type_data='VA';
            }
            

            foreach($request->ulasan as $ulasan){ 
                $ulasan_json = json_decode($ulasan,TRUE);
                $ulasan_id = $ulasan_json['id'];
                
                if($ulasan_json['id'] != '') { 
                    if($va->is_revision) {
                        //Log::info($ulasan_json);
                        $ulasan_project = VmUlasan::whereId($ulasan_json['id'])->update([
                            'perkara' => $ulasan_json['perkara'],
                            'catatan' => $ulasan_json['catatan'],
                            'is_complete' =>  0,
                            'status' =>  $ulasan_json['status'],
                            'dikemaskini_oleh' => $request->user_id,
                            'dikemaskini_pada' => Carbon::now()->format('Y-m-d H:i:s'),
                            'type' => $type_data,
                        ]);
                    }else {
                        $ulasan_project = VmUlasan::whereId($ulasan_json['id'])->update([
                            'perkara' => $ulasan_json['perkara'],
                            'catatan' => $ulasan_json['catatan'],
                            'dikemaskini_oleh' => $request->user_id,
                            'dikemaskini_pada' => Carbon::now()->format('Y-m-d H:i:s'),
                            'type' => $type_data,
                        ]);   
                    }
                    
                }else {
                    $data = VmUlasan::create([       
                        'pp_id' => $request->pp_id,
                        'perkara' => $ulasan_json['perkara'],
                        'catatan' => $ulasan_json['catatan'],
                        'is_complete' =>  0,
                        'status' =>  $ulasan_json['status'],
                        'row_status' => 1,
                        'is_hidden' => 0,                    
                        'created_at' =>  Carbon::now()->format('Y-m-d H:i:s'),            
                        'dibuat_pada' => Carbon::now()->format('Y-m-d H:i:s'),
                        'dibuat_oleh' => $request->user_id,
                        'dikemaskini_oleh' => $request->user_id,
                        'dikemaskini_pada' => Carbon::now()->format('Y-m-d H:i:s'),
                        'type' => $type_data,
                    ]);
                }
            }

            $va->is_revision = true;
            $va->status = $request->status;
            $va->save();
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

    private function createUlasanHistory($request)
    {
        try{
                if($request->type=='VE')
                {
                    $allUlasan = VmUlasan::where('type','VE')->where('pp_id',$request->pp_id)->get();
                    $type_data='VE';
                }
                else
                {
                    $allUlasan = VmUlasan::where('type','VA')->where('pp_id',$request->pp_id)->get();
                    $type_data='VA';
                }

                //Log::info("ulusanall"); Log::info($allUlasan);

                foreach($allUlasan as $ulusan) { Log::info("ulusanid"); Log::info($ulusan->id);
                    $ulusan_data=\App\Models\VM\VmUlasanHistory::where('ulasan_id',$ulusan->id)->delete(); //Log::info("ulusandata"); Log::info($ulusan_data);
                    //$ulusan_data->delete();
                }
                

                foreach($allUlasan as $ulusan) {
                    //if($ulusan->is_complete == 0 && $ulusan->status != 'Selesai Pindaan') {
                        $history = VmUlasanHistory::create([       
                            'pp_id' => $request->pp_id,
                            'perkara' => $ulusan->perkara,
                            'catatan' => $ulusan->catatan,
                            'is_complete' =>  0,
                            'is_submitted' =>  0,
                            'status' =>  $ulusan->status,
                            'ulasan_id' => $ulusan->id,
                            'row_status' => 1,
                            'is_hidden' => 0,                    
                            'tarikh_hantar' =>  Carbon::now()->format('Y-m-d'),
                            'created_at' =>  Carbon::now()->format('Y-m-d H:i:s'),            
                            'dibuat_pada' => Carbon::now()->format('Y-m-d H:i:s'),
                            'dibuat_oleh' => $request->user_id,
                            'dikemaskini_oleh' => $request->user_id,
                            'dikemaskini_pada' => Carbon::now()->format('Y-m-d H:i:s'),
                            'type' => $type_data
                        ]);
                    //}

                    if($request->type=='VE')
                    {
                        $history = VmUlasanHistory::where('type','VE')->where('ulasan_id',$ulusan->id)->first();
                    }
                    else
                    {
                        $history = VmUlasanHistory::where('type','VA')->where('ulasan_id',$ulusan->id)->first();
                    }

                    if($history->status != $ulusan->status) {
                        $history->status = $ulusan->status;
                        $history->save();
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

    private function updateUlasanHistory($request)
    {    
        try{
              if($request->sejarah){
                    foreach($request->sejarah as $sejarahId)  { 

                        if($request->type=='VE')
                        {
                            $history = VmUlasanHistory::where('type','VE')->whereId($sejarahId)->first();
                        }
                        else
                        {
                            $history = VmUlasanHistory::where('type','VA')->whereId($sejarahId)->first();
                        }
                        if(!$history->is_submitted) {
                            $history->is_submitted = true;
                            $history->tarikh_maklumbalas = Carbon::now()->format('Y-m-d');
                            $history->dikemaskini_oleh = $request->user_id;
                            $history->dikemaskini_pada = Carbon::now()->format('Y-m-d H:i:s');
                            $history->update();
                        }

                        // $history = VmUlasanHistory::whereId($sejarahId)->update([    
                        //     'is_submitted' =>  1,        
                        //     'tarikh_maklumbalas' =>  Carbon::now()->format('Y-m-d'),
                        //     'dikemaskini_oleh' => $request->user_id,
                        //     'dikemaskini_pada' => Carbon::now()->format('Y-m-d H:i:s'),
                        // ]);
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

    private function createUlasan($request)
    {
        try{
            if($request->type=='VE')
            {
                $existing_ulasan_id = VmUlasan::where('type','VE')->where('pp_id',$request->pp_id)->pluck('id')->toArray();
                $type_data='VE';
            }
            else
            {
                $existing_ulasan_id = VmUlasan::where('type','VA')->pluck('id')->toArray();
                $type_data='VA';
            }
            $current_ulasan_id = [];


            foreach($request->ulasan as $ulasan){
                $ulasan_json = json_decode($ulasan,TRUE);
                if($ulasan_json['id'] != '') {
                    array_push($current_ulasan_id,$ulasan_json['id']) ;
                }
            }

            $delete_ulasan_id = [];
            
            foreach($existing_ulasan_id as $ulasan_id){
                if (!in_array($ulasan_id, $current_ulasan_id)) {
                    array_push($delete_ulasan_id,$ulasan_id);
                }

            }
            
            VmUlasan::whereIn('id',$delete_ulasan_id)->delete();

            foreach($request->ulasan as $ulasan){
                $ulasan_json = json_decode($ulasan,TRUE);

                $ulasan_id = $ulasan_json['id'];
                if($ulasan_json['id'] == 'undefined') {
                    continue;
                }
                if($ulasan_json['id'] != '') {
                    $ulasan_project = VmUlasan::whereId($ulasan_json['id'])->update([
                        'perkara' => $ulasan_json['perkara'],
                        'catatan' => $ulasan_json['catatan'],
                        'dikemaskini_oleh' => $request->user_id,
                        'dikemaskini_pada' => Carbon::now()->format('Y-m-d H:i:s'),
                        'type' => $type_data,
                    ]);
                }else {
                   // Log::info("create");
                    $data = VmUlasan::create([       
                        'pp_id' => $request->pp_id,
                        'perkara' => $ulasan_json['perkara'],
                        'catatan' => $ulasan_json['catatan'],
                        'is_complete' =>  0,
                        'status' =>  'Perlu Pindaan',
                        'row_status' => 1,
                        'is_hidden' => 0,                    
                        'created_at' =>  Carbon::now()->format('Y-m-d H:i:s'),            
                        'dibuat_pada' => Carbon::now()->format('Y-m-d H:i:s'),
                        'dibuat_oleh' => $request->user_id,
                        'dikemaskini_oleh' => $request->user_id,
                        'dikemaskini_pada' => Carbon::now()->format('Y-m-d H:i:s'),
                        'type' => $type_data,
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

    public function downloadLampiranDoc(Request $request)
    {
        try{
                $id = $request->id;
                //Log::info($request);
                $doc = Media::whereId($id)->first();
                //Log::info($doc);
                $mediaItem = $doc;
                
                
                
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

    public function selasaiVM(Request $request)
    {
        try{
            PemantauanProject::where('id', $request->pp_id)->update([
                'status_perlaksanaan' => $request->status,
                'dikemaskini_oleh' => $request->user_id,
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
