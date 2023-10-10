<?php

namespace App\Http\Controllers\Api\RP;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

use App\Models\User;
use App\Models\RP\RpPermohonan;
use App\Models\RP\RpPermohonanBahagian;
use App\Models\RP\RpPermohonanButiran;
use App\Models\RP\RpPermohonanNegeri;
use App\Models\RP\RpPermohonanDetail;
use App\Models\RP\RpPermohonanNegeriDetail;
use App\Models\RP\RpSejarahBahagian;
use App\Models\RP\RpSejarahBahagianUlasan;
use App\Models\RP\RpSejarahNegeri;
use App\Models\RP\RpSejarahNegeriUlasan;
use Illuminate\Support\Carbon;
use App\Notifications\RpNotification;
use Illuminate\Support\Facades\Validator;

use Illuminate\Support\Facades\Log;
use Jenssegers\Agent\Facades\Agent;


class BKORController extends Controller
{
    //

    public function store(Request $request)
    {
        try {
            Log::info($request);
            
            $validator = Validator::make($request->all(),[
                'rp_tajuk' => ['required', 'string'],  
                'rp_tarikh_permohonan' => ['required', 'date'],
                'bahagian_terliabt' => ['required', 'string'],
                'negeriId' => ['required', 'integer'],
                'daerahId' => ['required', 'integer'],
                'parlimenId' => ['required', 'integer'],
                'dunId' => ['required', 'integer'],
            ],[
                'rp_tajuk.required' => 'Tajuk permohonan diperlukan.',
                'rp_tarikh_permohonan.required' => 'Tarikh Terima diperlukan.',
                'bahagian_terliabt.required' => 'Bahagian terlibat diperlukan.',
                'negeriId.required' => 'Negeri diperlukan.',
                'daerahId.required' => 'Daerah diperlukan.',
                'parlimenId.required' => 'Parlimen diperlukan.',
                'dunId.required' => 'Dun diperlukan.',
            ]);

            if(!$validator->fails()) {
                $status = 'DAFTAR PERMOHONAN';
                $workflow = $request->workflow;
                if($request->currentWorkflow == 'BKOR' && $request->workflow == 'Bahagian') {
                    $status = 'ULASAN TEKNIKAL BAHAGIAN';
                }

    
                if($request->workflow == 'Batal') {
                    $status = 'BATAL OLEH BKOR';
                    $workflow = 'BKOR';
                }
                $rpProjectData = [
                    'tajuk' => $request->rp_tajuk,
                    'tarikh_permohonan' => $request->rp_tarikh_permohonan,
                    'kos' => $request->rp_kos,
                    'bkor_catatan' => $request->rp_bkor_catatan,
                    'workflow' => $workflow,
                    'no_rujukan' => $request->no_rujukan,
                    'status' => $status,
                    'rumusan_permohonan' => $request->rumusan_permohonan,
                ];
        
                if($request->rp_project_id != '') {
                    RpPermohonan::where('id',$request->rp_project_id)->update($rpProjectData);
                    $rpPermohonan = RpPermohonan::whereId($request->rp_project_id)->first();
        
                }else {
                    $rpProjectData['is_first'] = TRUE;
                    $rpPermohonan = RpPermohonan::create($rpProjectData);
                }

                
                
                if($request->currentWorkflow == 'BKOR') {
                    $this->storeBKOR($request,$rpPermohonan);
                }

                if($request->is_ulasan == 'true') {
                    $this->storeBKORUlasan($request,$rpPermohonan);
                }
    
                
                if ($request->has('rumusan_file')) {
                    $rpPermohonan->clearMediaCollection('rp_rumusan_lampiran_file');
                    $rpPermohonan->addMedia($request->file('rumusan_file'))
                            ->toMediaCollection('rp_rumusan_lampiran_file','rp_permohonan');
                }
    
                if($request->workflow == 'Selesai') { 
                    $rpPermohonan->status = 'Selesai';
                    $rpPermohonan->workflow = 'BKOR';
                    $rpPermohonan->save();
                }
    
                if($request->currentWorkflow == 'BKOR' && $request->workflow == 'Bahagian') {
                    $this->notifyAllBahagian($request->workflow,$rpPermohonan,explode(",",$request->bahagian_terliabt));
                }

                Log::info($rpPermohonan);

                if($request->currentWorkflow == 'BKOR' && $request->workflow == 'BKOR' && $rpPermohonan->is_first == 0) {
                    $rpPermohonan->status = 'RUMUSAN PERMOHONAN';
                    $rpPermohonan->save();
                }
    
            }
            else {
                return response()->json([
                    'code' => '422',
                    'status' => 'Unprocessable Entity',
                    'data' => $validator->errors(),
                ]);
            }

            return response()->json([
                'code' => '200',
                'status' => 'Success',
                'data' => $rpPermohonan,
            ]);

            

        } catch (\Throwable $th) {
            logger()->error($th->getMessage());            
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
            return response()->json([
                'code' => '500',
                'status' => 'Failed',
                'error' => $th,
            ]);
        }
    }

    private function storeBKORUlasan($request,$rpPermohonan)
    {
        $bahagianUlasanData = json_decode($request->bkorUlasanJsonData, true);

        foreach ($bahagianUlasanData as $key => $value) {
            $bahagianId = $key;
            $ulasan = $value;
            
            RpSejarahBahagianUlasan::create([
                'rp_permohonan_id' => $rpPermohonan->id,
                'tarikh_catatan' => Carbon::now()->format('Y-m-d'),
                'catatan' => $ulasan,
                'bahagian_id' => $bahagianId
            ]);

            RpPermohonanBahagian::where('bahagian_id',$bahagianId)
                                ->where('rp_permohonan_id',$rpPermohonan->id)
                                ->update([
                                    'status' => 'Bahagian'
                                ]);
        }
    }

    public function storeBKOR($request, $rpPermohonan)
    {
        $fileNumber = 0;

        $negeriData = ['negeri_id' => $request->negeriId,
                        'daerah_id' => $request->daerahId,
                        'parliment_id' => $request->parlimenId,
                        'dun_id' => $request->dunId,
                        // 'rp_permohonan_id' => $rpPermohonan->id,
                        'dibuat_oleh' => $request->user_id,
                        'dikemaskini_oleh' => $request->user_id,
                        'dikemaskini_pada' => Carbon::now()->format('Y-m-d H:i:s')];
        
        
        RpPermohonanNegeri::updateOrCreate(['rp_permohonan_id' => $rpPermohonan->id,],
            $negeriData);

        $butiranArrayData = json_decode($request->butiranJsonData, true);
        
        $mergeArray = ['rp_permohonan_id' => $rpPermohonan->id,
            'dibuat_oleh' => $request->user_id,
            'dikemaskini_oleh' => $request->user_id,
            'dikemaskini_pada' => Carbon::now()->format('Y-m-d H:i:s')];

        $db_existing_butiran = RpPermohonanButiran::where('rp_permohonan_id', $rpPermohonan->id)->pluck('id')->toArray();
        $current_butiran = [];
        foreach($butiranArrayData as $butiranData){
            $butiranData = array_merge($butiranData ,$mergeArray);
            $filteredArray = Arr::except($butiranData, ['is_file','id']);
            if($butiranData['id'] != '') {
                array_push($current_butiran,$butiranData['id']);
                RpPermohonanButiran::whereId($butiranData['id'])->update($filteredArray);
                $rpPermohonanButiran = RpPermohonanButiran::whereId($butiranData['id'])->first();
                if($butiranData['is_file']) {
                    if($request->file('files')){
                        $mediaCollectionName =  $rpPermohonanButiran->id .'_permohonan_butiran_'.  $butiranData['order_no'];
                        
                        $rpPermohonanButiran->clearMediaCollection($mediaCollectionName);
                        $rpPermohonanButiran->addMedia($request->file('files')[$fileNumber])
                                  ->toMediaCollection($mediaCollectionName,'rp_permohonan');
                    }

                    $fileNumber = $fileNumber + 1;
                }
            }else {
                $rpPermohonanButiran = RpPermohonanButiran::create($filteredArray);

                if($butiranData['is_file']) {
                    if($request->file('files')){
                        $mediaCollectionName =  $rpPermohonanButiran->id .'_permohonan_butiran_'.  $butiranData['order_no'];
                        
                        $rpPermohonanButiran->clearMediaCollection($mediaCollectionName);
                        $rpPermohonanButiran->addMedia($request->file('files')[$fileNumber])
                                  ->toMediaCollection($mediaCollectionName,'rp_permohonan');
                    }

                    $fileNumber = $fileNumber + 1;
                }
            }
        }

        

        $existing_bahagian = RpPermohonanBahagian::where('rp_permohonan_id', $rpPermohonan->id)->pluck('bahagian_id')->toArray();
        $bahagianTerlibat = explode(",",$request->bahagian_terliabt);

        $new_bahagian = array_diff($bahagianTerlibat, $existing_bahagian);
        $deleted_bahagian = array_diff($existing_bahagian, $bahagianTerlibat);
        $deleted_butiran = array_diff($db_existing_butiran, $current_butiran);

        RpPermohonanButiran::whereIn('id', $deleted_butiran)->delete();
        deleteMedia($deleted_butiran, 'App\Models\RP\RpPermohonanButiran');
        RpPermohonanBahagian::whereIn('bahagian_id', $deleted_bahagian)->delete();
        // RpPermohonanDetail::where('rp_permohonan_id',$rpPermohonan->id)->whereIn('bahagian_id', $deleted_bahagian)->delete();
        
        
        $bahagianCatatanData = json_decode($request->bkorCatatanJsonData, true);

        if($request->is_ulasan == 'true') {
            $status = 'BKOR';
        }else {
            $status = $request->workflow;
        }
        
        foreach($bahagianTerlibat as $bahagian_terlibat){
            $rp_bahagian = RpPermohonanBahagian::updateOrCreate([
                'rp_permohonan_id' => $rpPermohonan->id,
                'bahagian_id' => $bahagian_terlibat],[
                'dibuat_oleh' => $request->user_id,
                'dikemaskini_oleh' => $request->user_id,
                'status' => $status,
                'bkor_catatan' => $bahagianCatatanData[$bahagian_terlibat],
                'dikemaskini_pada' => Carbon::now()->format('Y-m-d H:i:s'),
            ]);

            $rp_bahagian_detail = RpPermohonanDetail::updateOrCreate([
                'rp_permohonan_id' => $rpPermohonan->id,
                'is_first' => True,
                'is_readonly' => False,
                'bahagian_id' => $rp_bahagian->id],[
                'dibuat_oleh' => $request->user_id,
                'dikemaskini_oleh' => $request->user_id,
                'dikemaskini_pada' => Carbon::now()->format('Y-m-d H:i:s'),
            ]);

            RpPermohonanNegeriDetail::updateOrCreate([
                'rp_permohonan_id' => $rpPermohonan->id,
                'bahagian_id' => $rp_bahagian_detail->id],[
                'dibuat_oleh' => $request->user_id,
                'dikemaskini_oleh' => $request->user_id,
                'dikemaskini_pada' => Carbon::now()->format('Y-m-d H:i:s'),
            ]);
        }
    }

    public function storeNegeri(Request $request)
    {
        try {
            Log::info($request);
            
            // $status = 'ULASAN PERMOHONAN NEGERI';

            // if($request->workflow == 'Bahagian') {
            //     $status = 'SEMAKAN MAKLUMBALAS NEGERI';
            // }
            // $rpProjectData = [
            //     'workflow' => $request->workflow,
            //     'status' => $status
            // ];

            
    
            
            // RpPermohonan::where('id',$request->rp_project_id)->update($rpProjectData);
            $rpPermohonan = RpPermohonan::whereId($request->rp_project_id)->first();

            
            // if (!array_key_exists('Bahagian', $status)) {
            //     if (array_key_exists('Negeri', $status)) {
            //             $rpPermohonan->workflow = 'Negeri';
            //             $rpPermohonan->status = 'ULASAN PERMOHONAN NEGERI';
            //             $rpPermohonan->save();
            //             // $this->notifyNegeri('Negeri',$rpPermohonan);
            //     }
                
            //     if (array_key_exists('Negeri', $status)) {
            //         $rpPermohonan->workflow = 'BKOR';
            //         $rpPermohonan->status = 'RUMUSAN PERMOHONAN';
            //         $rpPermohonan->is_first = false;
            //         $rpPermohonan->save();
            //         // $this->notifyBKOR('BKOR',$rpPermohonan,$RpPermohonanDetail->bahagian_id);
            //     }
            // }
            
            $negeriData = json_decode($request->negeriJsonData, true);
            $fileCounter = 0;
            foreach($negeriData as $negeri){
                $RpPermohonanNegeriDetail = RpPermohonanNegeriDetail::where('rp_permohonan_id',$rpPermohonan->id)
                                        ->where('bahagian_id',$negeri['bahagian_id'])->first();
                $RpPermohonanNegeriDetail->isu = $negeri['isu'];
                $RpPermohonanNegeriDetail->ulasan_teknikal = $negeri['ulasan_teknikal'];
                $RpPermohonanNegeriDetail->cadagan_jangka_pendek = $negeri['cadagan_jangka_pendek'];
                $RpPermohonanNegeriDetail->cadagan_jangka_panjang = $negeri['cadagan_jangka_panjang'];
                $RpPermohonanNegeriDetail->dikemaskini_oleh = $request->user_id;
                $RpPermohonanNegeriDetail->dikemaskini_pada = Carbon::now()->format('Y-m-d H:i:s');

                $RpPermohonanNegeriDetail->save();

                $sejarah_file = false;
                $file = '';
                if($negeri['file']) {
                    $sejarah_file = true;
                    $file = $request->file('files')[$fileCounter];
                    $RpPermohonanNegeriDetail->clearMediaCollection('rp_negeri_lampiran_file');
                    $RpPermohonanNegeriDetail->addMedia($request->file('files')[$fileCounter])
                            ->toMediaCollection('rp_negeri_lampiran_file','rp_permohonan');
                        $fileCounter = $fileCounter + 1;   
                }

                if(!$negeri['file'] && $negeri['lampiran_file_name'] == '') {
                    $RpPermohonanNegeriDetail->clearMediaCollection('rp_negeri_lampiran_file');
                }

                if($request->workflow == 'Bahagian') {
                    $bahagian_details = RpPermohonanDetail::whereId($RpPermohonanNegeriDetail->bahagian_id)->with('bahagian')->first();

                    RpPermohonanBahagian::whereId($bahagian_details->bahagian->id)->update([
                        'status' => 'Bahagian'
                    ]);

                    $this->createNegeriSejarah($request,$negeri,$sejarah_file,$file);
                }
            };

            $statusCounts = RpPermohonanBahagian::selectRaw('status, COUNT(*) as count')
                ->where('rp_permohonan_id', $rpPermohonan->id)
                ->groupBy('status')
                ->get();

            $status = [];
            foreach ($statusCounts as $statusCount) {
                $status[$statusCount->status] = $statusCount->count;
            }
            
            if (!array_key_exists('Negeri', $status)) {
                $rpPermohonan->workflow = 'Bahagian';
                $rpPermohonan->status = 'ULASAN TEKNIKAL BAHAGIAN';
                $rpPermohonan->save();
            }
            if (!array_key_exists('Bahagian', $status) && !array_key_exists('BKOR', $status)) {
                $rpPermohonan->workflow = 'Negeri';
                $rpPermohonan->status = 'ULASAN PERMOHONAN NEGERI';
                $rpPermohonan->save();
            }

            if (!array_key_exists('Negeri', $status) && !array_key_exists('BKOR', $status)) { 
                $rpPermohonan->workflow = 'Bahagian';
                $rpPermohonan->status = 'ULASAN TEKNIKAL BAHAGIAN';
                $rpPermohonan->save();
            }

            if (!array_key_exists('Bahagian', $status) && !array_key_exists('Negeri', $status)) {
                $rpPermohonan->workflow = 'BKOR';
                $rpPermohonan->status = 'RUMUSAN PERMOHONAN';
                $rpPermohonan->save();
            }

            // if($request->workflow == 'Bahagian') {
            //     RpPermohonanBahagian::where('rp_permohonan_id', $rpPermohonan->id)
            //                     ->where('status','Negeri')
            //                     ->update(['status' => 'Bahagian']);

            //     $this->notifyBahagian('Bahagian',$rpPermohonan);

            // }

            

            return response()->json([
                'code' => '200',
                'status' => 'Success',
                'data' => $rpPermohonan,
            ]);

        } catch (\Throwable $th) {
            logger()->error($th->getMessage());            
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
            return response()->json([
                'code' => '500',
                'status' => 'Failed',
                'error' => $th,
            ]);
        }
    }

    public function index(Request $request)
    {
        try {

            $user = User::whereId($request->user_id)->first();
            
            $rpPermohonan = [];
            if($user->bahagian->acym === 'BKOR') {
                $rpPermohonan = RpPermohonan::with(['bahagians','bahagians.bahagian','negeris','negeris.negeri','butirans'])
                                // ->where('workflow','BKOR')
                                ->get();
            }

            if($user->negeri_id == null && $user->bahagian_id != null && $user->bahagian->acym != 'BKOR') {
                $projectIds = RpPermohonanBahagian::where('bahagian_id',$user->bahagian_id)
                                        // ->where('status','Bahagian')
                                        ->pluck('rp_permohonan_id')->toArray();
                $rpPermohonan = RpPermohonan::with(['bahagians','bahagians.bahagian','negeris','negeris.negeri','butirans'])
                                ->whereIn('id',$projectIds)
                                // ->where('workflow','Bahagian')
                                ->get();
                
            }

            if($user->negeri_id != null && $user->bahagian_id == null && $user->bahagian->acym != 'BKOR') {
                $projectIds = RpPermohonanNegeri::where('negeri_id',$user->negeri_id)->pluck('rp_permohonan_id')->toArray();
                $rpPermohonan = RpPermohonan::with(['bahagians','bahagians.bahagian','negeris','negeris.negeri','butirans'])
                                ->whereIn('id',$projectIds)
                                // ->whereIn('workflow',['Negeri','Bahagian'])
                                ->get();
            }

            $data['negeri'] = getAllNegeri();
            $data['bahagian'] = getAllBahagian();
            $data['jenis_permohonan'] = RpPermohonanButiran::distinct()->pluck('jenis_permohonan');
            $data['tahun'] = RpPermohonan::distinct()->selectRaw('YEAR(tarikh_permohonan) as year')
                                    ->pluck('year');;
            $data['status'] = RpPermohonan::distinct()->pluck('status');
            $data['rpPermohonan'] = $rpPermohonan;


            // $rpPermohonan = RpPermohonan::with(['bahagians','bahagians.bahagian','negeris','negeris.negeri','butirans'])->get();

            return response()->json([
                'code' => '200',
                'status' => 'Success',
                'data' => $data,
            ]);

        } catch (\Throwable $th) {
            logger()->error($th->getMessage());            
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
            return response()->json([
                'code' => '500',
                'status' => 'Failed',
                'error' => $th,
            ]);
        }
    }

    public function filteredIndex(Request $request)
    {
        try {
            $user = User::whereId($request->user_id)->first();
            
            if($user->bahagian->acym === 'BKOR') {
                $rpPermohonan = RpPermohonan::with(['bahagians','bahagians.bahagian','negeris','negeris.negeri','butirans'])
                                ->where('workflow','BKOR');
            }

            if($user->negeri_id == null && $user->bahagian_id != null && $user->bahagian->acym != 'BKOR') {
                $projectIds = RpPermohonanBahagian::where('bahagian_id',$user->bahagian_id)
                                        ->where('status','Bahagian')
                                        ->pluck('rp_permohonan_id')->toArray();
                $rpPermohonan = RpPermohonan::with(['bahagians','bahagians.bahagian','negeris','negeris.negeri','butirans'])
                                ->whereIn('id',$projectIds)
                                ->where('workflow','Bahagian');
                
            }

            if($user->negeri_id != null && $user->bahagian_id == null && $user->bahagian->acym != 'BKOR') {
                $projectIds = RpPermohonanNegeri::where('negeri_id',$user->negeri_id)->pluck('rp_permohonan_id')->toArray();
                $rpPermohonan = RpPermohonan::with(['bahagians','bahagians.bahagian','negeris','negeris.negeri','butirans'])
                                ->whereIn('id',$projectIds)
                                ->where('workflow','Negeri');
            }

            if($request->query_status != null) {
                $rpPermohonan->where('status', $request->query_status);
            }


            if($request->query_tahun != null) {
                $rpPermohonan->whereYear('tarikh_permohonan', $request->query_tahun);
            }

            if($request->query_tajuk != null) {
                $rpPermohonan->where('tajuk', 'like', $request->query_tajuk . '%');
            }

            if($request->query_negeri != null) {
                $rpPermohonan->whereHas('negeris', function ($query) use ($request) {
                    $query->where('negeri_id', $request->query_negeri);
                }) ;
            }

            if($request->query_bahagian != null) {
                $rpPermohonan->whereHas('bahagians', function ($query) use ($request) {
                    $query->where('bahagian_id', $request->query_bahagian);
                }) ;
            }

            if($request->query_jenis != null) {
                $rpPermohonan->whereHas('butirans', function ($query) use ($request) {
                    $query->where('jenis_permohonan', $request->query_jenis);
                }) ;
            }

            $result = $rpPermohonan->get();

            return response()->json([
                'code' => '200',
                'status' => 'Success',
                'data' => $result,
            ]);

        } catch (\Throwable $th) {
            logger()->error($th->getMessage());            
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
            return response()->json([
                'code' => '500',
                'status' => 'Failed',
                'error' => $th,
            ]);
        }
    }

    public function edit($id)
    {
        try {
            $rpPermohonan = RpPermohonan::whereId($id)
                            ->with(['bahagians','bahagians.bahagian','bahagians.bahagianDetail','bahagians.bahagianDetail.negeri','media',
                                    'bahagians.bahagianDetail.media','negeris','negeris.negeri','negeris.daerah','negeris.parliment',
                                    'negeris.dun','butirans','butirans.media','bahagians.bahagianDetail.negeri.media',
                                    'sejarahNegeri','sejarahNegeri.media','sejarahNegeriUlasan','sejarahNegeriUlasan.bahagian','sejarahBahagian','sejarahBahagianUlasan','sejarahBahagianUlasan.bahagian'])
                            ->first();

            return response()->json([
                'code' => '200',
                'status' => 'Success',
                'data' => $rpPermohonan,
            ]);

        } catch (\Throwable $th) {
            logger()->error($th->getMessage());            
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
            return response()->json([
                'code' => '500',
                'status' => 'Failed',
                'error' => $th,
            ]);
        }
    }

    private function createNegeriSejarah($request,$negeri,$sejarah_file,$file = '')
    {
        try {
                $rpSejarahNegeri = RpSejarahNegeri::create([
                    'rp_permohonan_id' => $request->rp_project_id,
                    'isu' => $negeri['isu'],
                    'bahagian_id' => $negeri['bahagian_id'],
                    'ulasan_teknical' => $negeri['ulasan_teknikal'],
                    'cadangan_jangka_pendek' => $negeri['cadagan_jangka_pendek'],
                    'cadangan_jangka_panjang' => $negeri['cadagan_jangka_panjang'],
                    'lampiran' => $negeri['lampiran_file_name'],
                    'tarikh_maklumbalas' => Carbon::now()->format('Y-m-d'),
                ]);

                if($sejarah_file){
                    $rpSejarahNegeri->addMedia($file)
                                ->toMediaCollection('rp_negeri_lampiran_file','rp_permohonan');
                }else {
                    if($negeri['lampiran_file_name'] != '') {
                        $RpPermohonanNegeriDetail = RpPermohonanNegeriDetail::where('rp_permohonan_id',$request->rp_project_id)
                                                    ->where('bahagian_id',$negeri['bahagian_id'])
                                                    ->first();
                        $sourceMedia = $RpPermohonanNegeriDetail->getMedia('rp_negeri_lampiran_file')->first();
                        $copiedMedia = $sourceMedia->copy($rpSejarahNegeri, 'rp_negeri_lampiran_file','rp_permohonan');
                        $rpSejarahNegeri->save();
                    }
                }
            } catch (\Throwable $th) {
                logger()->error($th->getMessage());            
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
                // CallApi($body);
                return response()->json([
                    'code' => '500',
                    'status' => 'Failed',
                    'error' => $th,
                ]);
            }
    }

    private function notifyAllBahagian($workflow,$rp_permohonan,$bahagians)
    {
        $bahagian_users = User::whereNull('negeri_id')
                                ->whereNotNull('bahagian_id')
                                ->whereIn('bahagian_id',$bahagians)
                                ->with('bahagian')
                                ->get();

        foreach ($bahagian_users as $user) {
            $user->notify(new RpNotification($workflow, $rp_permohonan,$user->bahagian->id));
        }
    }

    private function notifyBahagian($workflow,$rp_permohonan)
    {

        $bahagians = RpPermohonanBahagian::where('rp_permohonan_id',$rp_permohonan->id)
                        ->where('status','Bahagian')
                        ->pluck('bahagian_id')
                        ->toArray();
        $bahagian_users = User::whereNull('negeri_id')
                                ->whereNotNull('bahagian_id')
                                ->whereIn('bahagian_id',$bahagians)
                                ->with('bahagian')
                                ->get();

        foreach ($bahagian_users as $user) {
            $user->notify(new RpNotification($workflow, $rp_permohonan,$user->bahagian->id));
        }
    }
}