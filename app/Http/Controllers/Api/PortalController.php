<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CmsHeader;
use App\Models\CmsFooter;
use App\Models\CmsKandungan;
use App\Models\CmsPengenalan;
use App\Models\CmsPengumuman;
use App\Models\ModelBreakingNews;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Jenssegers\Agent\Facades\Agent;


class PortalController extends Controller
{
    //
    public function listHeader()
    {
        try {
            $result = CmsKandungan::where('unique_key','header')->with('media')->first();
            // if($data->is_video) {                
            //     $media = $data->getMedia('video');
            // }else {                
            //     $media = $data->getMedia('image');
            // }
            // $data['media_details'] = $media; 
            //$result = \App\Models\CmsHeader::whereId(1)->first();

            $logo1 = $result->getMedia('logo_1')->first();
            $logo2 = $result->getMedia('logo_2')->first();
            $logo3 = $result->getMedia('logo_3')->first();
            
            
            $result['logo1'] = '';
            $result['logo2'] = '';
            $result['logo3'] = '';
            if($logo1){
                $result['logo1'] = $logo1->getUrl();
            }

            if($logo2){
                $result['logo2'] = $logo2->getUrl();
            }

            if($logo3){
                $result['logo3'] = $logo3->getUrl();
            }
            
            
            
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

    public function storeHeader(Request $request)
    {
        try {
            //code...
            // dd($request->all());
            // dump($request->file('Logo_1'));
            // dump($request->file('Logo_2'));
            // dd($request->file('Logo_3'));
            $result = CmsKandungan::updateOrCreate(
                ['unique_key' => 'header'],
                [
                    'tajuk' => 'header',
                    'keterangan' => '',
                    'is_video' => 0,
                    'json_values' => null,
                    //'json_values' => json_encode($json_values),
                    'dibuat_oleh' => $request->user_id,
                    'dikemaskini_oleh' => $request->user_id,
                    'dikemaskini_pada' => Carbon::now()->format('Y-m-d H:i:s')
                ]
            );
            $logo1 = $result->getMedia('logo_1')->first();
            $logo2 = $result->getMedia('logo_2')->first();
            $logo3 = $result->getMedia('logo_3')->first();

            // $header = \App\Models\CmsHeader::whereId(1)->first();
            // $logo1 = $header->getMedia('logo_1')->first();
            // $logo2 = $header->getMedia('logo_2')->first();
            // $logo3 = $header->getMedia('logo_3')->first();
            if(!$request->logo1_src) {
                if($logo1){
                    $logo1->delete(); 
                }
            }

            if($request->file('Logo_1')) {
                
                $result
                ->addMedia($request->file('Logo_1'))
                ->toMediaCollection('logo_1');
            }

            if(!$request->logo2_src) {
                if($logo2){
                    $logo2->delete(); 
                }
            }
            if($request->file('Logo_2')) {
                
                $result
                ->addMedia($request->file('Logo_2'))
                ->toMediaCollection('logo_2');
            }

            if(!$request->logo3_src) {
                if($logo3){
                    $logo3->delete(); 
                }
            }

            if($request->file('Logo_3')) {
                
                $result
                ->addMedia($request->file('Logo_3'))
                ->toMediaCollection('logo_3');
            }

            // if($request->file('Logo_2')) {
            //     if($logo2){
            //         $logo2->delete(); 
            //     }
            //     $result
            //     ->addMedia($request->file('Logo_2'))
            //     ->toMediaCollection('logo_2');
            // }else {
            //     if($logo2){
            //         $logo2->delete(); 
            //     }
            // }

            // if($request->file('Logo_3')) {
            //     if($logo3){
            //         $logo3->delete(); 
            //     }
            //     $result
            //     ->addMedia($request->file('Logo_3'))
            //     ->toMediaCollection('logo_3');
            // }else {
            //     if($logo3){
            //         $logo3->delete(); 
            //     }
            // }

            // $header->save();
            // $logo1 = $header->getMedia('logo_1')->first();
            // $logo2 = $header->getMedia('logo_2')->first();
            // $logo3 = $header->getMedia('logo_3')->first();
            
            // $header->header_navbar_logo_1 = $logo1->getUrl();
            // $header->header_navbar_logo_2 = $logo2->getUrl();
            // $header->header_navbar_logo_3 = $logo3->getUrl();
            // $header->save();
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

    public function listLanding()
    {
        try {
            //code...
            $data = CmsKandungan::where('unique_key','landing')->first();
            $data2= ModelBreakingNews::orderBy('created_at', 'desc')->first();
            
            if($data->is_video) {                
                $media = $data->getMedia('slide_video');
            }else {                
                $media = $data->getMedia('slide_images');
            }
            $data['media_details'] = $media;            
            return response()->json([
                'code' => '200',
                'status' => 'Success',
                'data' => $data,
                'data2'=>$data2,
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

    public function storeLanding(Request $request)
    {
        // dd($request->toArray());
        try {
            //code...                  
        if($request->landing_radio == 0){
            $tajuk  = $request->landing_tajuk_image;
        }else {
            $tajuk  = $request->landing_tajuk_video;
        }

        $result = CmsKandungan::updateOrCreate(
            ['unique_key' => 'landing'],
            [
                'tajuk' => $tajuk,
                'keterangan' => '',
                'is_video' => $request->landing_radio,
                'dibuat_oleh' => $request->user_id,
                'dikemaskini_oleh' => $request->user_id
            ]
        );
        $order = ModelBreakingNews::first();
        if(is_null($order)) {
            $result2 = ModelBreakingNews::Create(
                [
                'news'=>$request->BreakingNews,
                'dibuat_oleh' => $request->user_id,
                'dikemaskini_oleh' => $request->user_id,
                ]
            );
        }else {
            $result2 = ModelBreakingNews::where('id','=',1)->Update(
                [
                'news'=>$request->BreakingNews,
                'dibuat_oleh' => $request->user_id,
                'dikemaskini_oleh' => $request->user_id,
                'dikemaskini_pada'=>Carbon::now()->format('Y-m-d H:i:s')
                ]
            );
        }
        
        if($result->is_video) {

            if($request->file('landing_video')) {
                $media = $result->getMedia('slide_video');  
                if($media->count() > 0){
                    foreach ($media as $mediaItem) {
                        $mediaItem->delete();
                    }
                }
                $result
                    ->addMedia($request->file('landing_video'))
                    ->toMediaCollection('slide_video');
            }
            

        }else {
            $existing_images = explode(',' ,$request->existing_images);
            $media = $result->getMedia('slide_images');   
            
            if($media->count() > 0){
                foreach ($media as $mediaItem) {
                    if(!in_array($mediaItem->uuid, $existing_images))
                    {
                        $mediaItem->delete();   
                    }
                }
            }
            
            if($request->landing_images) {
                foreach($request->landing_images as $landing_image){
                    $result
                        ->addMedia($landing_image)
                        ->toMediaCollection('slide_images');
                }
            }
        }
        
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

    public function listPenganalan()
    {
        try {
            //code...
            $data = CmsKandungan::where('unique_key','pengenalan')->with('media')->first();
            if($data->is_video) {                
                $media = $data->getMedia('video');
            }else {                
                $media = $data->getMedia('image');
            }
            $data['media_details'] = $media; 

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

    public function storePenganalan(Request $request)
    {   
        // dd($request->toArray());
        try {
            //code...

            if($request->pengenalan_radio == 0){
                $tajuk  = $request->pengenalan_tajuk_image;
                $keterangan = $request->pengenalan_keterangan_image;
            }else {
                $tajuk  = $request->pengenalan_tajuk_video;
                $keterangan = $request->pengenalan_keterangan_video;
            }
    
            $result = CmsKandungan::updateOrCreate(
                ['unique_key' => 'pengenalan'],
                [
                    'tajuk' => $tajuk,
                    'keterangan' => $keterangan,
                    'is_video' => $request->pengenalan_radio,
                    'dibuat_oleh' => $request->user_id,
                    'dikemaskini_oleh' => $request->user_id,
                    'dikemaskini_pada' => Carbon::now()->format('Y-m-d H:i:s')
                ]
            );

            if($request->pengenalan_radio){
                $video = $result->getMedia('video')->first();
                if($request->pengenalan_video){
                    if($video){
                        $video->delete();                    
                    }
                    $result
                    ->addMedia($request->file('pengenalan_video'))
                    ->toMediaCollection('video');
                }

            }else {
                $image = $result->getMedia('image')->first();
                if($image){
                    $image->delete();
                }

                $result
                ->addMedia($request->file('pengenalan_image'))
                ->toMediaCollection('image');
            }

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

    public function listContact()
    {
        try {
            //code...
            $data = CmsKandungan::where('unique_key','contact')->first();
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

    public function storeContact(Request $request)
    {
        try {
            //code...
            // dd($request->all());
            $json_values = [];
            $json_values['phone_no'] = $request->phone_no;
            $json_values['email'] = $request->email;
            $json_values['address'] = $request->address;
            $json_values['mapCode'] = $request->mapCode;
            $result = CmsKandungan::updateOrCreate(
                ['unique_key' => 'contact'],
                [
                    'tajuk' => 'contact',
                    'keterangan' => '',
                    'is_video' => 0,
                    'json_values' => json_encode($json_values),
                    'dibuat_oleh' => $request->user_id,
                    'dikemaskini_oleh' => $request->user_id,
                    'dikemaskini_pada' => Carbon::now()->format('Y-m-d H:i:s')
                ]
            );
            
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

    public function listPemgumuman(Request $request)
    {
        try {
            //code...
            // if($request->has('id')){
            $data = CmsPengumuman::where('id',$request->id)->with('updatedBy')->first();
            // }
            // else {
            //     $data = \App\Models\CmsPengumuman::with('updatedBy')->get();
            // }
            $data = CmsPengumuman::with(['updatedBy','media'])->get();
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

    public function getPengumuman($id)
    {
        try {
            //code...                
            $data = CmsPengumuman::whereId($id)->with(['updatedBy','media'])->first();
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

    public function storePengumuman(Request $request)
    {
        try {            
            $validator = Validator::make($request->all(),[
                'tajuk' => ['required', 'string', 'max:255'],
                'sub_tajuk' => ['required', 'string', 'max:255'],
                'keterangan' => ['required', 'string', 'max:4000'],
                'tarikh' => ['required', 'string', 'max:255']
            ]);
            
            if(!$validator->fails()) {                   
                if($request->id) {
                    // $data = CmsPengumuman::where('id', $request->id)->update([
                    //     'tajuk' => $request->tajuk,
                    //     'sub_tajuk' => $request->sub_tajuk,
                    //     'keterangan' => $request->keterangan,
                    //     'tarikh' => $request->tarikh,
                    //     'dikemaskini_oleh' => $request->user_id,
                    //     'dikemaskini_pada' => Carbon::now()->format('Y-m-d H:i:s'),
                    // ]);
                    CmsPengumuman::where('id',$request->id)->delete();
                }

                    $data = CmsPengumuman::create([                    
                        'tajuk' => $request->tajuk,
                        'sub_tajuk' => $request->sub_tajuk,
                        'keterangan' => $request->keterangan,
                        'tarikh' => $request->tarikh,                   
                        'created_at' =>  Carbon::now()->format('Y-m-d H:i:s'),            
                        'dibuat_pada' => Carbon::now()->format('Y-m-d H:i:s'),
                        'dibuat_oleh' => $request->user_id,
                        'dikemaskini_oleh' => $request->user_id,
                        'dikemaskini_pada' => Carbon::now()->format('Y-m-d H:i:s'),
                    ]);  
                
                if($request->file('pengumuman_image')) {
                    // $data=$request->file('pengumuman_image');
                    // $image = $data->getMedia('image')->first();
                    // if($image) {
                    //     $image->delete();
                    // }
                    
                    $data->addMedia($request->file('pengumuman_image'))->toMediaCollection('image');
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

    public function getFooter()
    {
        try {
            //code...            
            $data = CmsKandungan::where('unique_key','footer')->with('media')->first();
            $image = $data->getMedia('footer_image')->first();
                if($image) {
                    $data['imeg'] = $image->getUrl();
                 }
            
            $logo = $data->getMedia('footer_logo')->first();
                if($image) {
                    $data['logo'] = $logo->getUrl();
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

    public function storeFooter(Request $request)
    {
        // dd($request->toArray());
        try {
            //code...
            //dd($request->all());            
            $json_values = [];
            $json_values['copyright'] = $request->copyright;
            $json_values['total_visit'] = $request->total_visit;
            $json_values['total_visit_today'] = $request->total_visit_today;
            $json_values['total_visit_month'] = $request->total_visit_month;
            $json_values['total_visit_year'] = $request->total_visit_year;
            $json_values['logo_url'] = $request->logo_url;
            $result = CmsKandungan::updateOrCreate(
                ['unique_key' => 'footer'],
                [
                    'tajuk' => 'footer',
                    'keterangan' => '',
                    'is_video' => 0,
                    'json_values' => json_encode($json_values),
                    'dibuat_oleh' => $request->user_id,
                    'dikemaskini_oleh' => $request->user_id,
                    'dikemaskini_pada' => Carbon::now()->format('Y-m-d H:i:s')
                ]
            );

            if($request->file('Imeg_footer')) {
                $image = $result->getMedia('footer_image')->first();
                if($image) {
                    $image->delete();
                }
                
                $result
                ->addMedia($request->file('Imeg_footer'))
                ->toMediaCollection('footer_image');
            }

            if($request->file('Logo_footer')) {
                $logo = $result->getMedia('footer_logo')->first();
                if($logo) {
                    $logo->delete();
                }
                
                $result
                ->addMedia($request->file('Logo_footer'))
                ->toMediaCollection('footer_logo');
            }
            
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

    public function getPortal()
    {
        try {
            //code...


            $header = CmsKandungan::where('unique_key','header')->with('media')->first();
            $result = [];
            $logo1 = $header->getMedia('logo_1')->first();
            $logo2 = $header->getMedia('logo_2')->first();
            $logo3 = $header->getMedia('logo_3')->first();
            $result['logo1'] = '';
            $result['logo2'] = '';
            $result['logo3'] = '';
            // dd($logo1->getUrl());
            if($logo1){
                $result['logo1'] = $logo1->getUrl();
            }

            if($logo2){
                $result['logo2'] = $logo2->getUrl();
            }

            if($logo3){
                $result['logo3'] = $logo3->getUrl();
            }

            $data = CmsKandungan::where('unique_key','landing')->first();
                
            if($data->is_video) {                
                $media = $data->getMedia('slide_video');
            }else {                
                $media = $data->getMedia('slide_images');
            }
            
            $result['landing'] = $data;
            $result['landing']['media_details'] = $media;
            $result['landing_is_video'] = $data->is_video;

            $data = CmsKandungan::where('unique_key','pengenalan')->first();
            if($data->is_video) {                
                $media = $data->getMedia('video')->first();
            }else {                
                $media = $data->getMedia('image')->first();
            }
            $result['pengenalan'] = $data; 
            $result['pengenalan']['media_details'] = '';
            if($media) {
                $result['pengenalan']['media_details'] = $media->getUrl();
            }
            
            $result['pengenalan_is_video'] = $data->is_video;

            $data = CmsPengumuman::where('row_status',1)->with('media')->orderBy('id', 'desc')->take(3)->get();
            $result['pengumuman'] = $data;

            $data = CmsKandungan::where('unique_key','contact')->first();
            $result['contact'] = $data;

            $data = CmsKandungan::where('unique_key','footer')->first();
            $image = $data->getMedia('footer_image')->first();
            $data['imeg'] ='';
                if($image) {
                    $data['imeg'] = $image->getUrl();
                    }
            $data['logo'] ='';
            $logo = $data->getMedia('footer_logo')->first();
                if($image) {
                    $data['logo'] = $logo->getUrl();
                }

            $result['footer'] = $data;

            $data=CmsPengumuman::where('row_status',1)->orderBy('created_at', 'desc')->take(3)->get();
            
            $result['BreakingNews'] = $data;
            // $data['totalvisited'] = DB::connection(env('DB_CONNECTION_AUDIT'))->table('user_logging_audit')
            //                         ->select(DB::raw('count(DISTINCT user_id) AS totalvisited'))
            //                         ->get();

            // $data['visitedtoday'] = DB::connection(env('DB_CONNECTION_AUDIT'))->table('user_logging_audit')
            //                         ->select(DB::raw('count(DISTINCT user_id) AS visitedtoday'))
            //                         ->whereRaw(DB::raw("FORMAT(GETDATE(), 'dd-MMM-yyyy') = FORMAT(created_at, 'dd-MMM-yyyy')"))
            //                         ->get();
            
            // $data['visitedmonth'] = DB::connection(env('DB_CONNECTION_AUDIT'))->table('user_logging_audit')
            //                         ->select(DB::raw('count(DISTINCT user_id) AS visitedmonth'))
            //                         ->whereRaw(DB::raw('MONTH(GETDATE()) = MONTH(created_at) and YEAR(GETDATE()) = YEAR(created_at)'))
            //                         ->get();
            
            // $data['visitedyear'] = DB::connection(env('DB_CONNECTION_AUDIT'))->table('user_logging_audit')
            //                         ->select(DB::raw('count(DISTINCT user_id) AS visitedyear'))
            //                         ->whereRaw(DB::raw('YEAR(GETDATE()) = YEAR(created_at)'))
            //                         ->get();

            $data=[];

            $data['totalvisited'] = DB::connection(env('DB_CONNECTION_AUDIT'))->table('user_logging_audit')
                                    ->select(DB::raw('count(DISTINCT user_id) AS totalvisited'))
                                    ->get();

            $data['visitedtoday'] = DB::connection(env('DB_CONNECTION_AUDIT'))->table('user_logging_audit')
                                    ->select(DB::raw('count(DISTINCT user_id) AS visitedtoday'))
                                    ->whereRaw(DB::raw("FORMAT(GETDATE(), 'dd-MMM-yyyy') = FORMAT(created_at, 'dd-MMM-yyyy')"))
                                    ->get();
            
            $data['visitedmonth'] = DB::connection(env('DB_CONNECTION_AUDIT'))->table('user_logging_audit')
                                    ->select(DB::raw('count(DISTINCT user_id) AS visitedmonth'))
                                    ->whereRaw(DB::raw('MONTH(GETDATE()) = MONTH(created_at) and YEAR(GETDATE()) = YEAR(created_at)'))
                                    ->get();
            
            $data['visitedyear'] = DB::connection(env('DB_CONNECTION_AUDIT'))->table('user_logging_audit')
                                    ->select(DB::raw('count(DISTINCT user_id) AS visitedyear'))
                                    ->whereRaw(DB::raw('YEAR(GETDATE()) = YEAR(created_at)'))
                                    ->get();

            
            return response()->json([
                'code' => '200',
                'status' => 'Success',
                'data' => $result,
                'data2' => $data,
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

    public function removefooterlogo(Request $request){
        try{
                $id=$request->toArray();
                $media_id=intval($id[0]);
                $media_id2=intval($id[1]);
                // var_dump($media_id);
                CmsKandungan::whereHas('media', function ($query) use($media_id,$media_id2){
                    $query->whereId($media_id);
            })->first()->deleteMedia($media_id);

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


    public function ActivatePengumuman(Request $request){
        // dd($request->toArray());
        try{
                $data = CmsPengumuman::where('id', $request->id)->update([
                    'row_status' => $request->value,
                    'dikemaskini_oleh' => $request->loged_user_id,
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

    public function deActivatePengumuman(Request $request){
        // dd($request->toArray());
        try{
                $data = CmsPengumuman::where('id', $request->id)->update([
                    'row_status' => $request->value,
                    'dikemaskini_oleh' => $request->loged_user_id,
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
