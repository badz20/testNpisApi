<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use \App\Models\ProjectNegeriLokas;
use \App\Models\noc_negeri;
use \App\Models\ProjectNegeriDokumen;
use Illuminate\Support\Carbon;
use \App\Models\projectLog;
use \App\Models\Project;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Jenssegers\Agent\Facades\Agent;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class ProjectNegeriLokasController extends Controller
{
    public function negeriDetails($id)
    {
        try {
            $negeri_lokas = \App\Models\ProjectNegeriLokas::where('permohonan_Projek_id', $id)
                ->where('row_status', 1)
                ->orderBy('negeri_id')
                ->get();

            $data['negeri'] = $negeri_lokas;
            $data['documents'] = \App\Models\ProjectNegeriDokumen::with('media')->select('id', 'projek_negeri_dokumen_name', 'keterangan')
                                ->where('permohonan_Projek_id', $id)
                                ->where('row_status', 1)
                                ->orderBy('id', 'DESC')->get();

            $data['negeriselection'] = \App\Models\Project::select('negeri_selection_type', 'koordinat_latitude', 'koordinat_longitude')
                ->where('id', $id)
                ->where('row_status', 1)
                ->get();

            // print_r($data);exit;


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

    public function updateNegeri(Request $request)
    {
        // dd($request->toArray());
        try {
            // $validator = Validator::make($request->all(),[
            //     // 'mukim_id' => ['required', 'integer'],
            //     'dun_id' => ['required', 'integer'],
            //     'parlimen_id' => ['required', 'integer'],
            //     'negeri_id' => ['required', 'integer'],
            //     'daerah_id' => ['required', 'integer']
            // ]);
            if (true) {

                $negeri_data = \App\Models\ProjectNegeriLokas::where('permohonan_Projek_id', $request->id)->first();
                $negeri_lokas = null;
                if ($negeri_data) {
                    $negeri_lokas = $this->updateNegeriData($request->all(), $request->id);
                }
                //$negeri_lokas = $this->createNegeriData($request->all());


                $data = $request->toArray();
                $project_id = $request->id;


                \App\Models\Project::where('id', $project_id)->update([
                    'koordinat_latitude' => $request->koordinat_latitude,
                    'koordinat_longitude' => $request->koordinat_longitude,
                    'negeri_name' => $request->NegeriName,
                ]);

                if ($request->negeritext) {

                    foreach ($request->negeritext as $negeritextitem) {
                        $data = json_decode($negeritextitem, TRUE);
                        $negerilokascomponen = \App\Models\ProjectNegeriLokas::create([
                            'negeri_id' => $data['negeri_id'],
                            'daerah_id' => $data['daerah_id'],
                            'mukim_id' => $data['mukim_id'],
                            'parlimen_id' => $data['parlimen_id'],
                            'dun_id' => $data['dun_id'],
                            'permohonan_Projek_id' => $request->id,
                            // 'koordinat_latitude' => $data['koordinat_latitude'],
                            // 'koordinat_longitude' => $data['koordinat_longitude'],
                            'dibuat_pada' => Carbon::now()->format('Y-m-d H:i:s'),
                            'dikemaskini_pada' => Carbon::now()->format('Y-m-d H:i:s'),
                            'row_status' => 1
                        ]);
                    }
                    try {
                        $negeri_selection = $this->updateNegeriSelection($request->all(), $request->id);
                    } catch (\Throwable $th) {
                        logger()->error($th->getMessage());

                        return response()->json([
                            'code' => '500',
                            'status' => 'Failed',
                            'error' => $th,
                        ]);
                    }
                }



                $section_name = 'Negeri lokasi';
                $user_data = DB::table('users')
                    ->join('ref_jawatan', 'ref_jawatan.id', '=', 'users.jawatan_id')
                    ->select('users.*', 'ref_jawatan.nama_jawatan')->where('users.id', $request->userid)->first();
                $no_rojukan_data = DB::table('projects')->select('no_rujukan')->where('id', $request->id)->first();
                $logData = [
                    'user_id' => $request->userid,
                    'section_name' => $section_name,
                    'projek_id' => $request->id,
                    'modul' => 'Permohonan Projek',
                    'user_ic_no' => $user_data->no_ic,
                    'user_jawatan' => $user_data->nama_jawatan,
                    'user_name' => $user_data->name,
                    'no_rujukan' => $no_rojukan_data->no_rujukan,
                ];

                DB::connection(env('DB_CONNECTION_AUDIT'))->table('projek_log')->insert($logData);

                return response()->json([
                    'code' => '200',
                    'status' => 'Success',
                    'data' => $negeri_lokas,
                ]);
            } else {
                return response()->json([
                    'code' => '422',
                    'status' => 'Unprocessable Entity',
                    'data' => $validator->errors(),
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

    protected function updateNegeriData(array $data, $id)
    {
        // print_r($data);exit;

        return ProjectNegeriLokas::where('permohonan_Projek_id', $id)->update([
            'dikemaskini_pada' => Carbon::now()->format('Y-m-d H:i:s'),
            'row_status' => 0
        ]);
    }

    protected function updateNegeriDataNOC(array $data, $id)
    {
        // print_r($data);exit;

        return noc_negeri::where('pp_id', $id)->update([
            'dikemaskini_pada' => Carbon::now()->format('Y-m-d H:i:s'),
            'row_status' => 0
        ]);
    }

    protected function updateNegeriSelection(array $data, $id)
    {
        // print_r($data);exit;


        return  \App\Models\Project::where('id', $id)->update([

            'dikemaskini_pada' => Carbon::now()->format('Y-m-d H:i:s'),
            'negeri_selection_type' => $data['negeri_selection_type']
        ]);
    }
    protected function createNegeriData(array $data)
    {

        return ProjectNegeriLokas::create([
            'negeri_id' => $data['negeri_id'],
            'daerah_id' => $data['daerah_id'],
            'mukim_id' => $data['mukim_id'],
            'parlimen_id' => $data['parlimen_id'],
            'dun_id' => $data['dun_id'],
            'permohonan_Projek_id' => $data['id'],
            'koordinat_latitude' => $data['koordinat_latitude'],
            'koordinat_longitude' => $data['koordinat_longitude'],
            'dibuat_pada' => Carbon::now()->format('Y-m-d H:i:s'),
            'dikemaskini_pada' => Carbon::now()->format('Y-m-d H:i:s'),
            'row_status' => 1
        ]);
    }

    public function addDocument(Request $request)
    {
        // print_r($request->all());exit;
        // $negeri_data_s = \App\Models\ProjectNegeriLokas::where('permohonan_Projek_id', $request->id)->first();     
        // if($negeri_data_s)
        // { 
        //     $negeri_lokas = $this->updateNegeriData($request->all(),$request->id);
        //     if($request->file('gambar_image')) {
        //         $negeri_data_s
        //         ->addMedia($request->file('gambar_image'))
        //         ->toMediaCollection('negeri_document');
        //     }
        // }
        // else
        // { 
        //     $negeri_lokas = $this->createNegeriData($request->all());
        //     if($request->file('gambar_image')) {
        //         $negeri_lokas
        //         ->addMedia($request->file('gambar_image'))
        //         ->toMediaCollection('negeri_document');
        //     }

        // }

        try {

            $file_name = $request->gambar_image;
            $file_size = $request->image_size;

            $original_file_name = $file_name->getClientOriginalName();
            $extension       = $file_name->getClientOriginalExtension();
            $fileWithoutExt  = str_replace(".", "", basename($original_file_name, $extension));
            $updated_fileName = $fileWithoutExt . "_" . rand(0, 99) . "." . $extension . "/" . $file_size;

            $negeri = ProjectNegeriDokumen::create([
                'permohonan_Projek_id' => $request->id,
                'projek_negeri_dokumen_name' => $updated_fileName,
                'keterangan' => $request->keterangan,
                'dikemaskini_oleh' => $request->user_id,
                'dikemaskini_pada' => Carbon::now()->format('Y-m-d H:i:s'),
                'dibuat_oleh' => $request->user_id,
                'dibuat_pada' => Carbon::now()->format('Y-m-d H:i:s'),
                'row_status' => 1
            ]);

            if ($request->file('gambar_image')) {
                $negeri->clearMediaCollection('negeri_document');
                $negeri->addMedia($request->file('gambar_image'))
                        ->toMediaCollection('negeri_document');
            }

            return $negeri;
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

    public function deleteDocument(Request $request)
    {
        try {
            return ProjectNegeriDokumen::where('id', $request->id)->update([
                'dikemaskini_oleh' => $request->user_id,
                'dikemaskini_pada' => Carbon::now()->format('Y-m-d H:i:s'),
                'dibuat_oleh' => $request->user_id,
                'dibuat_pada' => Carbon::now()->format('Y-m-d H:i:s'),
                'row_status' => 0
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

    public function updateNegeriNOC(Request $request)
    {
        // dd($request->toArray());
        try {
            // $validator = Validator::make($request->all(),[
            //     // 'mukim_id' => ['required', 'integer'],
            //     'dun_id' => ['required', 'integer'],
            //     'parlimen_id' => ['required', 'integer'],
            //     'negeri_id' => ['required', 'integer'],
            //     'daerah_id' => ['required', 'integer']
            // ]);
            if (true) {

                $negeri_data = \App\Models\noc_negeri::where('pp_id', $request->id)->first();
                if ($negeri_data) {
                    $negeri_lokas = $this->updateNegeriDataNOC($request->all(), $request->id);
                }
                //$negeri_lokas = $this->createNegeriData($request->all());


                $data = $request->toArray();
                $project_id = $request->id;


                // \App\Models\Project::where('id', $project_id)->update([ 
                //    'koordinat_latitude' => $request->koordinat_latitude,
                //     'koordinat_longitude' => $request->koordinat_longitude,
                //     'negeri_name'=> $request->NegeriName,
                // ]);

                if ($request->negeritext) {

                    foreach ($request->negeritext as $negeritextitem) {
                        $data = json_decode($negeritextitem, TRUE);
                        $negerilokascomponen = \App\Models\noc_negeri::create([
                            'negeri_id' => $data['negeri_id'],
                            'daerah_id' => $data['daerah_id'],
                            'mukim_id' => $data['mukim_id'],
                            'parlimen_id' => $data['parlimen_id'],
                            'dun_id' => $data['dun_id'],
                            'pp_id' => $request->id,
                            'dibuat_pada' => Carbon::now()->format('Y-m-d H:i:s'),
                            'dikemaskini_pada' => Carbon::now()->format('Y-m-d H:i:s'),
                            'row_status' => 1,
                            'status_id' => 1
                        ]);
                    }
                    try {
                        //$negeri_selection = $this->updateNegeriSelection($request->all(),$request->id);

                    } catch (\Throwable $th) {
                        logger()->error($th->getMessage());

                        return response()->json([
                            'code' => '500',
                            'status' => 'Failed',
                            'error' => $th,
                        ]);
                    }
                }



                // $section_name='Negeri lokasi';
                // $user_data = DB::table('users')
                //            ->join('ref_jawatan','ref_jawatan.id', '=','users.jawatan_id')
                //            ->select('users.*','ref_jawatan.nama_jawatan')->where('users.id',$request->userid)->first();
                // $no_rojukan_data = DB::table('projects')->select('no_rujukan')->where('id',$request->id)->first();
                // $logData=[
                //             'user_id' =>$request->userid, 
                //             'section_name'=>$section_name,   
                //             'projek_id'=>$request->id,
                //             'modul' => 'Permohonan Projek',
                //             'user_ic_no' => $user_data->no_ic,
                //             'user_jawatan' => $user_data->nama_jawatan,
                //             'user_name' => $user_data->name,
                //             'no_rujukan' => $no_rojukan_data-> no_rujukan,
                //       ];

                // DB::connection(env('DB_CONNECTION_AUDIT'))->table('projek_log')->insert($logData);

                // return response()->json([
                //     'code' => '200',
                //     'status' => 'Success',
                //     'data' => $negeri_lokas,
                // ]);
            } else {
                return response()->json([
                    'code' => '422',
                    'status' => 'Unprocessable Entity',
                    // 'data' => $validator->errors(),
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


    public function negeriDetailsforringkasan($id)
    {
        try {
            $negeri_lokas = \App\Models\ProjectNegeriLokas::with(['negeri', 'daerah', 'parlimen', 'dun'])
                ->where('permohonan_Projek_id', $id)
                ->where('row_status', 1)
                ->orderBy('id', 'DESC')
                ->get();

            $data['negeri'] = $negeri_lokas;
            $data['documents'] = \App\Models\ProjectNegeriDokumen::select('keterangan')
                ->where('permohonan_Projek_id', $id)
                ->where('row_status', 1)
                ->orderBy('id', 'DESC')->get();


            $result = \App\Models\ProjectNegeriDokumen::where('permohonan_Projek_id', $id)
                ->where('row_status', 1)
                ->orderBy('id', 'DESC')->get();

            $data['docs1'] = '';
            $data['docs2'] = '';
            $data['docs3'] = '';

            if (count($result) == 1) {
                $docs1 = $result[0]->getMedia('negeri_document')->first();
                $docs2 = '';
                $docs3 = '';
            } elseif (count($result) == 2) {
                $docs1 = $result[0]->getMedia('negeri_document')->first();
                $docs2 = $result[1]->getMedia('negeri_document')->first();
                $docs3 = '';
            } elseif (count($result) >= 3) {
                $docs1 = $result[0]->getMedia('negeri_document')->first();
                $docs2 = $result[1]->getMedia('negeri_document')->first();
                $docs3 = $result[2]->getMedia('negeri_document')->first();
            } else {
                $docs1 = '';
                $docs2 = '';
                $docs3 = '';
            }


            if ($docs1) {
                $data['docs1'] = $docs1->getUrl();
            }
            if ($docs2) {
                $data['docs2'] = $docs2->getUrl();
            }
            if ($docs3) {
                $data['docs3'] = $docs3->getUrl();
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

    function downloadDokumen(Request $request ,Media $mediaItem){
        
        try {
                $id = $request->id;
                $doc = ProjectNegeriDokumen::where('id','=',$id)->first();
                $mediaItem = $doc->getFirstMedia('negeri_document');
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
