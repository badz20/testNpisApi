<?php

namespace App\Http\Controllers\Api\RP;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

use App\Models\User;
use App\Models\RP\RpPermohonan;
use App\Models\RP\RpPermohonanBahagian;
use App\Models\RP\RpPermohonanDetail;
use App\Models\RP\RpSejarahBahagian;
use App\Models\RP\RpSejarahBahagianUlasan;
use App\Models\RP\RpSejarahNegeri;
use App\Models\RP\RpSejarahNegeriUlasan;
use Illuminate\Support\Carbon;
use Jenssegers\Agent\Facades\Agent;
use App\Notifications\RpNotification;


use Illuminate\Support\Facades\Log;
use Mockery\Generator\StringManipulation\Pass\Pass;
use Mockery\Undefined;

class BahagianController extends Controller
{
    //

    public function store(Request $request)
    {
        try {
            Log::info($request);
            // RpPermohonan::where('id',$request->rp_project_id)->update($rpProjectData);
            $rpPermohonan = RpPermohonan::whereId($request->rp_project_id)->first();

            $rpBahagianData = [
                'isu' => $request->isu,
                'ulasan_teknikal' => $request->ulasan_teknikal,
                'is_dimohon' => $request->is_dimohon,
                'no_rujukan' => $request->no_rujukan_dimohon,
                'cadagan_jangka_pendek' => $request->cadagan_jangka_pendek,
                'cadagan_jangka_panjang' => $request->cadagan_jangka_panjang,
                'catatan' => $request->bahagian_catatan,
                'dikemaskini_oleh' => $request->user_id,
                'dikemaskini_pada' => Carbon::now()->format('Y-m-d H:i:s')
            ];

            RpPermohonanDetail::whereId($request->bahagianDetailsId)->update($rpBahagianData);

            $RpPermohonanDetail = RpPermohonanDetail::whereId($request->bahagianDetailsId)->first();

            
            $bahagian = RpPermohonanBahagian::whereId($RpPermohonanDetail->bahagian_id)->first();
            
            if($request->status != $bahagian->status) {
                $RpPermohonanDetail->is_first = False;
                $RpPermohonanDetail->save();
            }

            $updateData = ['status' => $request->status,
                            'dikemaskini_oleh' => $request->user_id,
                            'dikemaskini_pada' => Carbon::now()->format('Y-m-d H:i:s')
                        ];
            RpPermohonanBahagian::whereId($RpPermohonanDetail->bahagian_id)->update($updateData);

            if($request->file('lampiran_files')){
                $RpPermohonanDetail->clearMediaCollection('rp_lampiran_file');
                $RpPermohonanDetail->addMedia($request->file('lampiran_files'))
                            ->toMediaCollection('rp_lampiran_file','rp_permohonan');
            }

            if($request->has('dimohon_file')){
                $RpPermohonanDetail->clearMediaCollection('rp_di_mohon_file');
                $RpPermohonanDetail->addMedia($request->file('dimohon_file'))
                            ->toMediaCollection('rp_di_mohon_file','rp_permohonan');
            }

            if($request->lampiran_file_name == '' && $request->lampiran_files == 'undefined') {
                $RpPermohonanDetail->clearMediaCollection('rp_lampiran_file');
            }

            if($request->is_ulasan == 'true') {
                $this->createBahagianUlasan($request);
            }

            // if($request->currentWorkflow == 'Bahagian' && $request->workflow == 'BKOR') {
            //     $this->createBahagianSejarah($request);
            // }

            // if($request->currentWorkflow == 'Bahagian' && $request->workflow == 'Negeri' && !$rpPermohonan->is_first) {
            //     $this->createBahagianUlasan($request);
            // }

            
            $statusCounts = RpPermohonanBahagian::selectRaw('status, COUNT(*) as count')
                ->where('rp_permohonan_id', $rpPermohonan->id)
                ->groupBy('status')
                ->get();

            $status = [];
            foreach ($statusCounts as $statusCount) {
                $status[$statusCount->status] = $statusCount->count;
            }

            if (!array_key_exists('Bahagian', $status)) {
                if (array_key_exists('Negeri', $status)) {
                        $rpPermohonan->workflow = 'Negeri';
                        $rpPermohonan->status = 'ULASAN PERMOHONAN NEGERI';
                        $rpPermohonan->save();
                        $this->notifyNegeri('Negeri',$rpPermohonan);
                }else {
                    $rpPermohonan->workflow = 'BKOR';
                    $rpPermohonan->status = 'RUMUSAN PERMOHONAN';
                    $rpPermohonan->is_first = false;
                    $rpPermohonan->save();
                    $this->notifyBKOR('BKOR',$rpPermohonan,$RpPermohonanDetail->bahagian_id);
                }
            }

            $data['details'] = $rpPermohonan;
            $data['status'] = $request->status;
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

    // public function store(Request $request)
    // {
    //     try {
    //         Log::info($request);
    //         $rpProjectData = [
    //             'tajuk' => $request->rp_tajuk,
    //             'kos' => $request->rp_kos,
    //             'workflow' => $request->workflow,
    //             'dikemaskini_oleh' => $request->user_id,
    //             'dikemaskini_pada' => Carbon::now()->format('Y-m-d H:i:s')
    //         ];

    //         $rpBahagianDetails = [
    //             'isu' => $request->isu,
    //             'ulasan_teknikal' => $request->ulasan_teknikal,
    //             'cadagan_jangka_pendek' => $request->cadagan_jangka_pendek,
    //             'cadagan_jangka_panjang' => $request->cadagan_jangka_panjang,
    //             'rp_permohonan_id' => $request->rp_project_id,
    //             'dibuat_oleh' => $request->user_id,
    //             'dikemaskini_oleh' => $request->user_id,
    //             'dikemaskini_pada' => Carbon::now()->format('Y-m-d H:i:s')
    //         ];

    //         if($request->has('is_dimohon')) {
    //             $mergeArray = ['is_dimohon' => $request->is_dimohon,
    //                             'no_rujukan' => $request->no_rujukan_dimohon,
    //                         ];
    //             $rpBahagianDetails = array_merge($rpBahagianDetails ,$mergeArray);
    //         }else {
    //             $mergeArray = ['is_first' => false ];
    //             $rpProjectData = array_merge($rpProjectData ,$mergeArray);
    //         }

            
    //         RpPermohonan::where('id',$request->rp_project_id)->update($rpProjectData);
    //         $rpPermohonan = RpPermohonan::whereId($request->rp_project_id)->first();

    //         if($request->bahagianDetailsId != '') {
    //             RpPermohonanDetail::where('rp_permohonan_id',$request->rp_project_id)->update($rpBahagianDetails);
    //             $RpPermohonanDetail = RpPermohonanDetail::where('rp_permohonan_id',$request->rp_project_id)->first();
    //         }else {
    //             $RpPermohonanDetail = RpPermohonanDetail::create($rpBahagianDetails);
    //         }

    //         if($request->file('dimohon_file')){
    //             $RpPermohonanDetail->clearMediaCollection('rp_dimohon_file');
    //             $RpPermohonanDetail->addMedia($request->file('dimohon_file'))
    //                         ->toMediaCollection('rp_dimohon_file','rp_permohonan');
    //         }

    //         if($request->has('is_dimohon')) {
    //             if(!$request->is_dimohon) {
    //                 $RpPermohonanDetail->clearMediaCollection('rp_dimohon_file');
    //             }
    //         }
            

    //         if($request->file('lampiran_files')){
    //             $RpPermohonanDetail->clearMediaCollection('rp_lampiran_file');
    //             $RpPermohonanDetail->addMedia($request->file('lampiran_files'))
    //                         ->toMediaCollection('rp_lampiran_file','rp_permohonan');
    //         }

    //         if($request->lampiran_file_name == '') {
    //             $RpPermohonanDetail->clearMediaCollection('rp_lampiran_file');
    //         }

    //         if($request->currentWorkflow == 'Negeri' && $request->workflow == 'Bahagian') {
    //             $this->createNegeriSejarah($request);
    //         }

    //         if($request->currentWorkflow == 'Bahagian' && $request->workflow == 'BKOR') {
    //             $this->createBahagianSejarah($request);
    //         }

    //         if($request->currentWorkflow == 'Bahagian' && $request->workflow == 'Negeri' && !$rpPermohonan->is_first) {
    //             $this->createBahagianUlasan($request);
    //         }

    //         if($request->currentWorkflow == 'BKOR' && $request->workflow == 'Bahagian' && !$rpPermohonan->is_first) {
    //             $this->createBKORUlasan($request);
    //         }

    //         return response()->json([
    //             'code' => '200',
    //             'status' => 'Success',
    //             'data' => $RpPermohonanDetail,
    //         ]);

    //     } catch (\Throwable $th) {
    //         logger()->error($th->getMessage());            
    //         $body = [
    //                 'application_name' => env('APP_NAME'),
    //                 'application_type' => Agent::isPhone(),
    //                 'url' => request()->fullUrl(),
    //                 'error_log' => $th->getMessage(),
    //                 'error_code' => $th->getCode(),
    //                 'ip_address' =>  request()->ip(),
    //                 'user_agent' => request()->userAgent(),
    //                 'email' => env('ERROR_EMAIL'),
    //             ];
    //         CallApi($body);
    //         return response()->json([
    //             'code' => '500',
    //             'status' => 'Failed',
    //             'error' => $th,
    //         ]);
    //     }
    // }

    private function createNegeriSejarah($request)
    {
        try {
                $rpSejarahNegeri = RpSejarahNegeri::create([
                    'rp_permohonan_id' => $request->rp_project_id,
                    'isu' => $request->isu,
                    'ulasan_teknical' => $request->ulasan_teknikal,
                    'cadangan_jangka_pendek' => $request->cadagan_jangka_pendek,
                    'cadangan_jangka_panjang' => $request->cadagan_jangka_panjang,
                    'lampiran' => $request->lampiran_file_name,
                    'tarikh_maklumbalas' => Carbon::now()->format('Y-m-d'),
                ]);

                if($request->file('lampiran_files')){
                    $rpSejarahNegeri->addMedia($request->file('lampiran_files'))
                                ->toMediaCollection('rp_lampiran_file','rp_permohonan');
                }else {
                    if($request->lampiran_file_name != '') {
                        $RpPermohonanDetail = RpPermohonanDetail::latest()->first();
                        $sourceMedia = $RpPermohonanDetail->getMedia('rp_lampiran_file')->first();
                        $copiedMedia = $sourceMedia->copy($rpSejarahNegeri, 'rp_lampiran_file','rp_permohonan');
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
                CallApi($body);
                return response()->json([
                    'code' => '500',
                    'status' => 'Failed',
                    'error' => $th,
                ]);
            }
    }

    private function createBahagianSejarah($request)
    {
        try{
                $rpSejarahBahagian = RpSejarahBahagian::create([
                    'rp_permohonan_id' => $request->rp_project_id,
                    'isu' => $request->isu,
                    'ulasan_teknical' => $request->ulasan_teknikal,
                    'cadangan_jangka_pendek' => $request->cadagan_jangka_pendek,
                    'cadangan_jangka_panjang' => $request->cadagan_jangka_panjang,
                    'lampiran' => $request->lampiran_file_name,
                    'tarikh_maklumbalas' => Carbon::now()->format('Y-m-d'),
                ]);

                if($request->file('lampiran_files')){
                    $rpSejarahBahagian->addMedia($request->file('lampiran_files'))
                                ->toMediaCollection('rp_lampiran_file','rp_lampiran_file','rp_permohonan');
                }else {
                    if($request->lampiran_file_name != '') {
                        $rpSejarahNegeri = RpSejarahNegeri::latest()->first();
                        $sourceMedia = $rpSejarahNegeri->getMedia('rp_lampiran_file')->first();
                        $copiedMedia = $sourceMedia->copy($rpSejarahBahagian, 'rp_permohonan');
                        $rpSejarahBahagian->save();
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
                CallApi($body);
                return response()->json([
                    'code' => '500',
                    'status' => 'Failed',
                    'error' => $th,
                ]);
            }
    }

    private function createBahagianUlasan($request)
    {

        $bahagian_details = RpPermohonanDetail::whereId($request->bahagianDetailsId)->with('bahagian')->first();

        RpSejarahNegeriUlasan::create([
            'rp_permohonan_id' => $request->rp_project_id,
            'tarikh_catatan' => Carbon::now()->format('Y-m-d'),
            'catatan' => $request->ulasan_data,
            'bahagian_id' => $bahagian_details->bahagian->bahagian_id
        ]);
    }

    private function createBKORUlasan($request)
    {
        RpSejarahBahagianUlasan::create([
            'rp_permohonan_id' => $request->rp_project_id,
            'tarikh_catatan' => Carbon::now()->format('Y-m-d'),
            'catatan' => $request->catatan_ulasan,
        ]);
    }

    private function notifyNegeri($worflow,$rp_permohonan)
    {
        $negeri_users = User::whereNull('bahagian_id')
                            ->where('negeri_id',$rp_permohonan->negeris->negeri_id)
                            ->get();
                                    
        foreach ($negeri_users as $user) {
            $user->notify(new RpNotification($worflow, $rp_permohonan));
        }
    }

    private function notifyBKOR($worflow,$rp_permohonan,$bahagian_id)
    {
        $bkor = \App\Models\refBahagian::where('acym','BKOR')->first();
        $bkor_users = User::whereNull('negeri_id')
                            ->whereNotNull('bahagian_id')
                            ->where('bahagian_id',$bkor->id)
                            ->get();
                                    
        foreach ($bkor_users as $user) {
            $user->notify(new RpNotification($worflow, $rp_permohonan,$bahagian_id));
        }
    
    }

}
