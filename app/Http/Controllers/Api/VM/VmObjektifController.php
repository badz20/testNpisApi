<?php

namespace App\Http\Controllers\Api\VM;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use App\Models\VM\VmObjektif;
use App\Models\VM\VmSkop;
use App\Models\VM\VmOutput;
use App\Models\VM\VmOutcome;
use App\Models\VM\vr_dockumen;
use App\Models\VM\VmMakmalKajianNilai;
use App\Models\VM\VmUlasan;
use App\Models\KalendarModel;
use \App\Models\VEKalendarModel;
use \App\Models\VRKalendarModel;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;
use App\Models\PemantauanProject;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Jenssegers\Agent\Facades\Agent;


class VmObjektifController extends Controller
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
                    $data['objektif'] = VmObjektif::where('type','=','VE')->where('pp_id',$request->pp_id)->with('va')->get();
                    $data['skop'] = VmSkop::where('type','=','VE')->where('pp_id',$request->pp_id)->with('va')->get();
                    $data['outcome'] = VmOutcome::where('type','=','VE')->where('pp_id',$request->pp_id)->with('va')->get();
                    $data['output'] = VmOutput::where('type','=','VE')->where('pp_id',$request->pp_id)->with('va')->get();
                    
                    $va = VmMakmalKajianNilai::where('type','=','VE')->where('pp_id',$request->pp_id)->first();
                    $data['project'] = PemantauanProject::whereId($request->pp_id)->first();
                    $data['va'] = $va;
                    $va_media = [];
                    if($va && $va->media) {
                        foreach($va->media as $media) {
                            $calSize = $media->size / (1024 * 1024);
                            array_push($va_media, [$media->file_name, number_format((float)$calSize, 2, '.', '') . 'mb', $media->id]);
                        }
                    }
                    
                    
                    $data['medias'] = $va_media;
                    $data['kalender_cadangan_pra_makmal'] = VEKalendarModel::where('pp_id',$request->pp_id)->where('kategori',1)->latest()->first();
                    $data['kalender_cadangan_makmal'] = VEKalendarModel::where('pp_id',$request->pp_id)->where('kategori',2)->latest()->first();
                    $data['kalender_cadangan_law_makmal'] = VEKalendarModel::where('pp_id',$request->pp_id)->where('kategori',3)->latest()->first();
                    $data['kalender_cadangan_mesyurat_makmal'] = VEKalendarModel::where('pp_id',$request->pp_id)->where('kategori',4)->latest()->first();
                }else {
                    $data['objektif'] = VmObjektif::where('type','=','VE')->get();
                    $data['skop'] = VmSkop::where('type','=','VE')->get();
                    $data['outcome'] = VmOutcome::where('type','=','VE')->get();
                    $data['output'] = VmOutput::where('type','=','VE')->get();
                    $data['va'] = VmMakmalKajianNilai::where('type','=','VE')->get();
                }
            }
            else if($request->type=='VR')
            {
                if($request->has('pp_id')){
                    $data['objektif'] = VmObjektif::where('type','=','VE')->where('pp_id',$request->pp_id)->with('va')->get();
                    $data['skop'] = VmSkop::where('type','=','VE')->where('pp_id',$request->pp_id)->with('va')->get();
                    $data['outcome'] = VmOutcome::where('type','=','VE')->where('pp_id',$request->pp_id)->with('va')->get();
                    $data['output'] = VmOutput::where('type','=','VE')->where('pp_id',$request->pp_id)->with('va')->get();
                    
                    $va = VmMakmalKajianNilai::where('type','=','VE')->where('pp_id',$request->pp_id)->first();
                    $data['project'] = PemantauanProject::whereId($request->pp_id)->first();
                    $data['va'] = $va;
                    $va_media = [];
                    if($va && $va->media) {
                        foreach($va->media as $media) {
                            $calSize = $media->size / (1024 * 1024);
                            array_push($va_media, [$media->file_name, number_format((float)$calSize, 2, '.', '') . 'mb', $media->id]);
                        }
                    }
                    
                    
                    $data['medias'] = $va_media;
                    $data['kalender_cadangan_pra_makmal'] = VRKalendarModel::where('pp_id',$request->pp_id)->where('kategori',1)->latest()->first();
                    $data['kalender_cadangan_makmal'] = VRKalendarModel::where('pp_id',$request->pp_id)->where('kategori',2)->latest()->first();
                    $data['kalender_cadangan_law_makmal'] = VRKalendarModel::where('pp_id',$request->pp_id)->where('kategori',3)->latest()->first();
                    $data['kalender_cadangan_mesyurat_makmal'] = VRKalendarModel::where('pp_id',$request->pp_id)->where('kategori',4)->latest()->first();
                }else {
                    $data['objektif'] = VmObjektif::where('type','=','VE')->get();
                    $data['skop'] = VmSkop::where('type','=','VE')->get();
                    $data['outcome'] = VmOutcome::where('type','=','VE')->get();
                    $data['output'] = VmOutput::where('type','=','VE')->get();
                    $data['va'] = VmMakmalKajianNilai::where('type','=','VE')->get();
                }
            }
            else
            {
                if($request->has('pp_id')){
                    $data['objektif'] = VmObjektif::where('type','VA')->where('pp_id',$request->pp_id)->with('va')->get();
                    $data['skop'] = VmSkop::where('type','VA')->where('pp_id',$request->pp_id)->with('va')->get();
                    $data['outcome'] = VmOutcome::where('type','VA')->where('pp_id',$request->pp_id)->with('va')->get();
                    $data['output'] = VmOutput::where('type','VA')->where('pp_id',$request->pp_id)->with('va')->get();
                    
                    $va = VmMakmalKajianNilai::where('type','VA')->where('pp_id',$request->pp_id)->first();
                    $data['project'] = PemantauanProject::whereId($request->pp_id)->first();
                    $data['va'] = $va;
                    $va_media = [];
                    if($va && $va->media) {
                        foreach($va->media as $media) {
                            $calSize = $media->size / (1024 * 1024);
                            array_push($va_media, [$media->file_name, number_format((float)$calSize, 2, '.', '') . 'mb', $media->id]);
                        }
                    }
                    
                    
                    $data['medias'] = $va_media;
                    $data['kalender_cadangan_pra_makmal'] = KalendarModel::where('pp_id',$request->pp_id)->where('kategori',1)->latest()->first();
                    $data['kalender_cadangan_makmal'] = KalendarModel::where('pp_id',$request->pp_id)->where('kategori',2)->latest()->first();
                    $data['kalender_cadangan_law_makmal'] = KalendarModel::where('pp_id',$request->pp_id)->where('kategori',3)->latest()->first();
                    $data['kalender_cadangan_mesyurat_makmal'] = KalendarModel::where('pp_id',$request->pp_id)->where('kategori',4)->latest()->first();

                }else {
                    $data['objektif'] = VmObjektif::where('type','VA')->get();
                    $data['skop'] = VmSkop::where('type','VA')->get();
                    $data['outcome'] = VmOutcome::where('type','VA')->get();
                    $data['output'] = VmOutput::where('type','VA')->get();
                    $data['va'] = VmMakmalKajianNilai::where('type','VA')->get();
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
                'pp_id' => ['required', 'string', 'max:255'],
                'user_id' => ['required', 'string', 'max:255'],
                'kos_selepas_makmal' => ['required', 'string', 'max:255'],
                'laporanFileInput' => Rule::requiredIf($request->existing_file == 'false'),
                // 'objektif' => ['required', 'array', 'max:255'],
                // 'skop' => ['required', 'array', 'max:255'],
                // 'outcome' => ['required', 'array', 'max:255'],
                // 'output' => ['required', 'array', 'max:255'],
            ],[
                'required' => 'Sila isi :attribute .',
            ]);

            if(!$validator->fails()) {
                $filename = '';
                if($request->file('laporanFileInput')){
                    $filename = $request->file('laporanFileInput')->getClientOriginalName();
                }else {
                    $filename = $request->existing_filename;
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
                    $type_data='VE';

                    $va_data= PemantauanProject::where('id',$request->pp_id)->first();
                    $va_data->ve_status = $status_new;
                    $va_data->current_status = $status_new;
                    $va_data->update();
                }
                else
                {
                    $type_data='VA';

                    $va_data= PemantauanProject::where('id',$request->pp_id)->first();
                    $va_data->va_status = $status_new;
                    $va_data->current_status = $status_new;
                    $va_data->update();
                }
                if($request->va_id) {
                    $data = VmMakmalKajianNilai::where('id', $request->va_id)->update([
                        'pp_id' => $request->pp_id,
                        'kos_selepas_makmal' => $request->kos_selepas_makmal,
                        'pengecualian' => $request->pengecualian,
                        'status' => $request->status,
                        'laporan_file_name' => $filename,
                        'dikemaskini_oleh' => $request->user_id,
                        'dikemaskini_pada' => Carbon::now()->format('Y-m-d H:i:s'),
                        'type'=> $type_data,
                    ]);
                }else {
                    $data = VmMakmalKajianNilai::create([
                        'pp_id' => $request->pp_id,
                        'kos_selepas_makmal' => $request->kos_selepas_makmal,
                        'pengecualian' => $request->pengecualian,
                        'status' => $request->status,
                        'laporan_file_name' => $filename,
                        'row_status' => 1,
                        'created_at' =>  Carbon::now()->format('Y-m-d H:i:s'),            
                        'dibuat_pada' => Carbon::now()->format('Y-m-d H:i:s'),
                        'dibuat_oleh' => $request->user_id,
                        'dikemaskini_oleh' => $request->user_id,
                        'dikemaskini_pada' => Carbon::now()->format('Y-m-d H:i:s'),
                        'type' => $type_data,
                    ]);
                }

                if($request->status != '29') {
                    PemantauanProject::where('id', $request->pp_id)->update([
                        'status_perlaksanaan' => $request->status,
                    ]);
                }

                // Update project status
                $pemantauan_data = PemantauanProject::where('id', $request->pp_id)->first();
                $status_perlaksanaan = $pemantauan_data->status_perlaksanaan;
                if($status_perlaksanaan  == 27 || 
                $status_perlaksanaan  == 34 || 
                $status_perlaksanaan  == 35 ||
                $status_perlaksanaan  == 31) {
                    if($request->type=='VE')
                    {
                        $pemantauan_data->ve_status = 32;
                        $pemantauan_data->current_status = 32;
                        $pemantauan_data->save();
                    }
                    else
                    {
                        $pemantauan_data->va_status = 32;
                        $pemantauan_data->current_status = 32;
                        $pemantauan_data->save();
                    }
                }
                
                if($request->has('objektif')) {
                    $this->createObjektif($request);
                }

                if($request->has('skop')) {
                    $this->createSkop($request);
                }

                if($request->has('outcome')) {
                    $this->createOutcome($request);
                }

                if($request->has('output')) {
                    $this->createOutput($request);
                }

                if($request->va_id) {
                    $data = VmMakmalKajianNilai::where('id', $request->va_id)->where('type',$type_data)->first();
                }

                if($request->file('laporanFileInput')) {
                    $data->clearMediaCollection('laporanFileInput');
                    $data->addMedia($request->file('laporanFileInput'))
                              ->toMediaCollection('laporanFileInput','vm');
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


    private function createObjektif($request)
    {
        try{
                if($request->type=='VE')
                {
                    $existing_objektif_id = VmObjektif::where('type','VE')->where('pp_id',$request->pp_id)->pluck('id')->toArray();
                    $type_data='VE';
                }
                else
                {
                    $existing_objektif_id = VmObjektif::where('type','VA')->where('pp_id',$request->pp_id)->pluck('id')->toArray();
                    $type_data='VA';
                }
                $current_objektif_id = [];


                foreach($request->objektif as $objektif){
                    $objektif_json = json_decode($objektif,TRUE); //print "hlelo"; print_r($objektif_json); print "hleloeeeeeeeee";

                    if ($objektif_json === null && json_last_error() !== JSON_ERROR_NONE) {
                        // There was an error decoding the JSON
                        echo 'JSON decoding error: ' . json_last_error_msg();
                    }
                    if($objektif_json['id'] != '') {
                        array_push($current_objektif_id,$objektif_json['id']) ;
                    }
                }

                $delete_objektif_id = [];
                
                foreach($existing_objektif_id as $objektif_id){
                    if (!in_array($objektif_id, $current_objektif_id)) {
                        array_push($delete_objektif_id,$objektif_id);
                    }

                }

                VmObjektif::whereIn('id',$delete_objektif_id)->delete();


                foreach($request->objektif as $objektif){
                    $objektif_json = json_decode($objektif,TRUE);
                    $objektif_id = $objektif_json['id'];
                    if($objektif_json['id'] != '') {
                        //array_push($current_objektif_id,$objektif_json['id']) ;
                        $skop_project = VmObjektif::whereId($objektif_json['id'])->update([
                            // 'objecktif_sebelum' => $objektif_json['old_objektif'],
                            'objecktif_selepas' => $objektif_json['new_objektif'],
                            'dikemaskini_oleh' => $request->user_id,
                            'dikemaskini_pada' => Carbon::now()->format('Y-m-d H:i:s'),
                            'type' => $type_data,
                        ]);
                    }else {
                        $data = VmObjektif::create([       
                            'pp_id' => $request->pp_id,
                            // 'objecktif_sebelum' => $objektif_json['old_objektif'],
                            'objecktif_selepas' => $objektif_json['new_objektif'],
                            'row_status' => 1,
                            'is_hidden' => 0,                    
                            'created_at' =>  Carbon::now()->format('Y-m-d H:i:s'),            
                            'dibuat_pada' => Carbon::now()->format('Y-m-d H:i:s'),
                            'dibuat_oleh' => $request->user_id,
                            'dikemaskini_oleh' => $request->user_id,
                            'dikemaskini_pada' => Carbon::now()->format('Y-m-d H:i:s'),
                            'type' => $type_data
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


    private function createSkop($request)
    {

        try{
                if($request->type=='VE')
                {
                    $existing_skop_id = VmSkop::where('type','VE')->where('pp_id',$request->pp_id)->pluck('id')->toArray();
                    $type_data='VE';
                }
                else
                {
                    $existing_skop_id = VmSkop::where('type','VA')->where('pp_id',$request->pp_id)->pluck('id')->toArray();
                    $type_data='VA';

                }
                $current_skop_id = [];


                foreach($request->skop as $skop){
                    $skop_json = json_decode($skop,TRUE);

                    if ($skop_json === null && json_last_error() !== JSON_ERROR_NONE) {
                        echo 'JSON decoding error: ' . json_last_error_msg();
                    }
                    if($skop_json['id'] != '') {
                        array_push($current_skop_id,$skop_json['id']) ;
                    }
                }

                $delete_skop_id = [];
                
                foreach($existing_skop_id as $skop_id){
                    if (!in_array($skop_id, $current_skop_id)) {
                        array_push($delete_skop_id,$skop_id);
                    }

                }
                
                VmSkop::whereIn('id',$delete_skop_id)->delete();

                foreach($request->skop as $skop){
                    $skop_json = json_decode($skop,TRUE);
                    $skop_id = $skop_json['id'];
                    if($skop_json['id'] == 'undefined') {
                        continue;
                    }
                    if($skop_json['id'] != '') {
                        //array_push($current_skop_id,$skop_json['id']) ;
                        $skop_project = VmSkop::whereId($skop_json['id'])->update([
                            // 'skop_sebelum' => $skop_json['old_skop'],
                            // 'kos_sebelum' => $skop_json['old_kos'],
                            'skop_selepas' => $skop_json['new_skop'],
                            'kos_selepas' => $skop_json['new_kos'],
                            'dikemaskini_oleh' => $request->user_id,
                            'dikemaskini_pada' => Carbon::now()->format('Y-m-d H:i:s'),
                            'type' => $type_data,
                        ]);
                    }else {
                        $data = VmSkop::create([       
                            'pp_id' => $request->pp_id,
                            // 'skop_sebelum' => $skop_json['old_skop'],
                            // 'kos_sebelum' => $skop_json['old_kos'],
                            'skop_selepas' => $skop_json['new_skop'],
                            'kos_selepas' => $skop_json['new_kos'],
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

    private function createOutcome($request)
    {
        try{

            if($request->type=='VE')
            {
                $existing_outcome_id = VmOutcome::where('type','VE')->where('pp_id',$request->pp_id)->pluck('id')->toArray();
                $type_data='VE';
            }
            else
            {
                $existing_outcome_id = VmOutcome::where('type',NULL)->where('pp_id',$request->pp_id)->pluck('id')->toArray();
                $type_data='VA';
            }

            $current_outcome_id = [];


            foreach($request->outcome as $outcome){
                $outcome_json = json_decode($outcome,TRUE);

                if ($outcome_json === null && json_last_error() !== JSON_ERROR_NONE) {
                    echo 'JSON outcome decoding error: ' . json_last_error_msg();
                }

                if($outcome_json['id'] != '') {
                    array_push($current_outcome_id,$outcome_json['id']) ;
                }
            }

            $delete_outcome_id = [];
            
            foreach($existing_outcome_id as $outcome_id){
                if (!in_array($outcome_id, $current_outcome_id)) {
                    array_push($delete_outcome_id,$outcome_id);
                }

            }
            
            VmOutcome::whereIn('id',$delete_outcome_id)->delete();

            foreach($request->outcome as $outcome){
                $outcome_json = json_decode($outcome,TRUE);
                $outcome_id = $outcome_json['id'];
                if($outcome_json['id'] == 'undefined') {
                    continue;
                }
                if($outcome_json['id'] != '') {
                    //array_push($current_outcome_id,$outcome_json['id']) ;
                    $outcome_project = VmOutcome::whereId($outcome_json['id'])->update([
                        // 'outcome_sebelum' => $outcome_json['old_outcome'],
                        // 'quantity_sebelum' => $outcome_json['old_quantity'],
                        // 'unit_sebelum' => $outcome_json['old_unit'],
                        'outcome_selepas' => $outcome_json['new_outcome'],
                        'quantity_selepas' => $outcome_json['new_quantity'],
                        'unit_selepas' => $outcome_json['new_unit'],
                        'dikemaskini_oleh' => $request->user_id,
                        'dikemaskini_pada' => Carbon::now()->format('Y-m-d H:i:s'),
                        'type' => $type_data,
                    ]);
                }else {
                    $data = VmOutcome::create([       
                        'pp_id' => $request->pp_id,
                        // 'outcome_sebelum' => $outcome_json['old_outcome'],
                        // 'quantity_sebelum' => $outcome_json['old_quantity'],
                        // 'unit_sebelum' => $outcome_json['old_unit'],
                        'outcome_selepas' => $outcome_json['new_outcome'],
                        'quantity_selepas' => $outcome_json['new_quantity'],
                        'unit_selepas' => $outcome_json['new_unit'],
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

    private function createOutput($request)
    {
        try{
            if($request->type=='VE')
            {
                $existing_output_id = VmOutput::where('type','VE')->where('pp_id',$request->pp_id)->pluck('id')->toArray();
                $type_data='VE';
            }
            else
            {
                $existing_output_id = VmOutput::where('type',NULL)->where('pp_id',$request->pp_id)->pluck('id')->toArray();
                $type_data='VA';
            }
            $current_output_id = [];


            foreach($request->output as $output){
                $output_json = json_decode($output,TRUE);
                if ($output_json === null && json_last_error() !== JSON_ERROR_NONE) {
                    echo 'JSON output decoding error: ' . json_last_error_msg();
                }
                if($output_json['id'] != '') {
                    array_push($current_output_id,$output_json['id']) ;
                }
            }

            $delete_output_id = [];
            
            foreach($existing_output_id as $output_id){
                if (!in_array($output_id, $current_output_id)) {
                    array_push($delete_output_id,$output_id);
                }

            }
            
            VmOutput::whereIn('id',$delete_output_id)->delete();

            foreach($request->output as $output){
                $output_json = json_decode($output,TRUE);
                $output_id = $output_json['id'];
                if($output_json['id'] == 'undefined') {
                    continue;
                }
                if($output_json['id'] != '') {
                    //array_push($current_output_id,$output_json['id']) ;
                    $output_project = VmOutput::whereId($output_json['id'])->update([
                        // 'output_sebelum' => $output_json['old_output'],
                        // 'quantity_sebelum' => $output_json['old_quantity'],
                        // 'unit_sebelum' => $output_json['old_unit'],
                        'output_selepas' => $output_json['new_output'],
                        'quantity_selepas' => $output_json['new_quantity'],
                        'unit_selepas' => $output_json['new_unit'],
                        'dikemaskini_oleh' => $request->user_id,
                        'dikemaskini_pada' => Carbon::now()->format('Y-m-d H:i:s'),
                        'type' => $type_data,
                    ]);
                }else {
                    $data = VmOutput::create([       
                        'pp_id' => $request->pp_id,
                        // 'output_sebelum' => $output_json['old_output'],
                        // 'quantity_sebelum' => $output_json['old_quantity'],
                        // 'unit_sebelum' => $output_json['old_unit'],
                        'output_selepas' => $output_json['new_output'],
                        'quantity_selepas' => $output_json['new_quantity'],
                        'unit_selepas' => $output_json['new_unit'],
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

    public function downloadDoc(Request $request)
    {
        try{
            $id = $request->id;
            $doc = VmMakmalKajianNilai::where('pp_id',$id)->first();
            $mediaItem = $doc->getFirstMedia('laporanFileInput');
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


    public function vr_objectiveData(Request $request){

        $myArray = explode(',', $request->objVal);

        //  dd($myArray);
        // // $until = substr($request->objVal, 0, strrpos($request->objVal.",", ","));
        // $until = preg_replace('/,[^,]*$/', '', $request->objVal);
        // dd($until);
        try {
            VmObjektif::where('pp_id',$request->pp_id)->where('type','VR')->delete();
            vr_dockumen::where('pp_id',$request->pp_id)->where('type','VR')->delete();          
            $data = VmObjektif::create([       
                'pp_id' => $request->pp_id,
                // 'objecktif_sebelum' => $objektif_json['old_objektif'],
                'objecktif_selepas' => $request->objVal,
                'row_status' => 1,
                'is_hidden' => 0,                    
                'created_at' =>  Carbon::now()->format('Y-m-d H:i:s'),            
                'dibuat_pada' => Carbon::now()->format('Y-m-d H:i:s'),
                'dibuat_oleh' => $request->user_id,
                'dikemaskini_oleh' => $request->user_id,
                'dikemaskini_pada' => Carbon::now()->format('Y-m-d H:i:s'),
                'type'=>$request->type,
            ]);
            $data2 = vr_dockumen::create([       
                'pp_id' => $request->pp_id,
                'objektif_file' => $request->file('objektif_file')->getClientOriginalName(),
                'type'=>$request->type,
                'row_status' => 1,
                'is_hidden' => 0,                    
                'created_at' =>  Carbon::now()->format('Y-m-d H:i:s'),            
                'dibuat_pada' => Carbon::now()->format('Y-m-d H:i:s'),
                'dibuat_oleh' => $request->user_id,
                'dikemaskini_oleh' => $request->user_id,
                'dikemaskini_pada' => Carbon::now()->format('Y-m-d H:i:s'),
            ]);
            if($request->file('objektif_file')) {
                $data2->clearMediaCollection('objektif_file');
                $data2->addMedia($request->file('objektif_file'))
                          ->toMediaCollection('objektif_file','vm');
            }
            
                
                return response()->json([
                    'code' => '200',
                    'status' => 'Sucess',
                    'data' => $data,
                    'data2' => $data2,

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

    public function vrData($kod,$type){
        try{
            $data= VmObjektif::where('pp_id',$kod)->where('type',$type)->get(); 
            $data2= vr_dockumen::where('pp_id',$kod)->where('type',$type)->with('media')->first(); //print_r($data2);exit();
            return response()->json([
                'code' => '200',
                'status' => 'Sucess',
                'data' => $data,
                'data2' => $data2,
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


