<?php

namespace App\Http\Controllers\Api\Perunding;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Perunding\PerundingMaklumat;
use App\Models\Perunding\PerundingMaklumatEocp;
use App\Models\Perunding\PerundingMaklumatPerlindungan;
use App\Models\Perunding\PerundingMaklumatSa;
use App\Models\Perunding\PemantauanPerolehan;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Carbon;

class MaklumatController extends Controller
{
    //
    public function edit(Request $request)
    {
        try {
            Log::info($request);
            $maklumatPerunding = PerundingMaklumat::where('pemantauan_id',$request->pemantauan_id)
                                ->where('perolehan_id',$request->perolehan_id)
                                ->with(['eocp','eocp.media','sa','perlindugan','perolehanProject','pemantauanProject','pemantauanProject.negeri','pemantauanProject.bahagianPemilik','media'])
                                ->first();

            $perolehan = PemantauanPerolehan::where('pemantauan_id',$request->pemantauan_id)
                            ->where('id',$request->perolehan_id)
                            ->with(['pemantauanProject','pemantauanProject.negeri','pemantauanProject.bahagianPemilik'])
                            ->first();
            $data['maklumatPerunding'] = $maklumatPerunding;
            $data['perolehan'] = $perolehan;
            if($maklumatPerunding) {
                $lsst = $maklumatPerunding->getFirstMedia('lsst');
                $Tr = $maklumatPerunding->getFirstMedia('tr');
                $sb = $maklumatPerunding->getFirstMedia('sb');
                $ba = $maklumatPerunding->getFirstMedia('ba');

                $data['lsst'] = $lsst;
                $data['tr'] = $Tr;
                $data['sb'] = $sb;
                $data['ba'] = $ba;
            }else {
                $data['lsst'] = null;
                $data['tr'] = null;
                $data['sb'] = null;
                $data['ba'] = null;
            }

            return response()->json([
                'code' => '200',
                'status' => 'Success',
                'data' => $data,
                
            ]);
        } catch (\Throwable $th) {

            logger()->error($th->getMessage());

            return response()->json([
                'code' => '500',
                'status' => 'Failed',
                'error' => $th,
            ]);
        }  

    }

    public function store(Request $request)
    {

        try {
            Log::info($request);
            $validator = Validator::make($request->all(),[
                'perolehan_id' => ['required', 'integer'],  
                'pemantauan_id' => ['required', 'integer'],
            ]);
            
            if(!$validator->fails()) {

                $maklumatPerunding = PerundingMaklumat::where('pemantauan_id',$request->pemantauan_id)->where('perolehan_id',$request->perolehan_id)->first();

                if($maklumatPerunding) {
                    PerundingMaklumat::where('id',$maklumatPerunding->id)->update([
                        'email_peringatan' => $request->emelPeringatan,
                        'kos_perolehan' => $request->kosPerolehan,
                        'nilai_bayaran_akhir_selesai' => $request->nilaiBayaranAkhirSelesai,
                        'penjimatan_selesai' => $request->penjimatanSelesai,
                        'nilai_bayaran_akhir_tamat' => $request->nilaiBayaranAkhirTamat,
                        'penjimatan_tamat' => $request->penjimatanTamat,
                        'no_polisi' => $request->noPolisi,
                        'nilai_polisi' => $request->nilaiPolisi,
                        'perlindungan_tarikh_mula' => $request->pelinduganTarikMula,
                        'perlindungan_tarikh_tamat' => $request->pelinduganTarikTamat,
                        'bayaran_perunding' => $request->bayaran_perunding_radio,
                        'dikemaskini_oleh' => $request->user_id,
                        'dikemaskini_pada' => Carbon::now()->format('Y-m-d H:i:s'),
                    ]);
                }else {
                    $maklumatPerunding = PerundingMaklumat::create([
                        'pemantauan_id' => $request->pemantauan_id,
                        'perolehan_id' => $request->perolehan_id,
                        'email_peringatan' => $request->emelPeringatan,
                        'kos_perolehan' => $request->kosPerolehan,
                        'nilai_bayaran_akhir_selesai' => $request->nilaiBayaranAkhirSelesai,
                        'penjimatan_selesai' => $request->penjimatanSelesai,
                        'nilai_bayaran_akhir_tamat' => $request->nilaiBayaranAkhirTamat,
                        'penjimatan_tamat' => $request->penjimatanTamat,
                        'no_polisi' => $request->noPolisi,
                        'nilai_polisi' => $request->nilaiPolisi,
                        'perlindungan_tarikh_mula' => $request->pelinduganTarikMula,
                        'perlindungan_tarikh_tamat' => $request->pelinduganTarikTamat,
                        'bayaran_perunding' => $request->bayaran_perunding_radio,
                        'created_at' =>  Carbon::now()->format('Y-m-d H:i:s'),            
                        'dibuat_pada' => Carbon::now()->format('Y-m-d H:i:s'),
                        'dibuat_oleh' => $request->user_id,
                        'dikemaskini_oleh' => $request->user_id,
                        'dikemaskini_pada' => Carbon::now()->format('Y-m-d H:i:s'),
                    ]);
                }

                // $maklumatPerunding->eocp()->delete();
                $maklumatPerunding->sa()->delete();
                $maklumatPerunding->perlindugan()->delete();
                
                if($request->has('eocpFile')) {
                    $eocpFiles = $request->file('eocpFile');
                }
                
                $fileCounter = 0;
                $newEocpIds = [];
                if($request->eocp) {
                    foreach($request->eocp as $eocp_details){
                        $eocp_json = json_decode($eocp_details,TRUE);
                        if($eocp_json['id'] != '0') {
                            array_push($newEocpIds, $eocp_json['id']);
                            PerundingMaklumatEocp::where('id',$eocp_json['id'])->update([
                            // $eocp = PerundingMaklumatEocp::create([
                                'pemantauan_id' => $request->pemantauan_id,
                                'perolehan_id' => $request->perolehan_id,
                                'maklumat_id' => $maklumatPerunding->id,
                                'tarikh' => $eocp_json['tarikh'],
                                'created_at' =>  Carbon::now()->format('Y-m-d H:i:s'),            
                                'dibuat_pada' => Carbon::now()->format('Y-m-d H:i:s'),
                                'dibuat_oleh' => $request->user_id,
                                'dikemaskini_oleh' => $request->user_id,
                                'dikemaskini_pada' => Carbon::now()->format('Y-m-d H:i:s'),
                            ]);
    
                            $eocp =  PerundingMaklumatEocp::where('id',$eocp_json['id'])->first();
                            
                            if($eocp_json['file'] && $request->has('eocpFile'))  {
                                
                                if($eocpFiles[$fileCounter]) {
                                    $eocp->clearMediaCollection('lampiran');
                                    $eocp->addMedia($eocpFiles[$fileCounter])
                                            ->toMediaCollection('lampiran','perunding');
                                }
    
                                $fileCounter = $fileCounter + 1;
                            }
                        } else {
                            $eocp = PerundingMaklumatEocp::create([
                                'pemantauan_id' => $request->pemantauan_id,
                                'perolehan_id' => $request->perolehan_id,
                                'maklumat_id' => $maklumatPerunding->id,
                                'tarikh' => $eocp_json['tarikh'],
                                'created_at' =>  Carbon::now()->format('Y-m-d H:i:s'),            
                                'dibuat_pada' => Carbon::now()->format('Y-m-d H:i:s'),
                                'dibuat_oleh' => $request->user_id,
                                'dikemaskini_oleh' => $request->user_id,
                                'dikemaskini_pada' => Carbon::now()->format('Y-m-d H:i:s'),
                            ]);
                            array_push($newEocpIds, $eocp->id);
                            if($eocp_json['file'] && $request->has('eocpFile'))  {
                                if($eocpFiles[$fileCounter]) {
                                    // $eocp->clearMediaCollection('lampiran');
                                    $eocp->addMedia($eocpFiles[$fileCounter])
                                            ->toMediaCollection('lampiran','perunding');
                                }
                                $fileCounter = $fileCounter + 1;
                            }
                        }
                    }

                    $existingIds = PerundingMaklumatEocp::get()->pluck('id')->toArray();
                    $deleteIds = array_diff($existingIds,$newEocpIds);
                    deleteMedia($deleteIds, 'App\Models\Perunding\PerundingMaklumatEocp');
                    PerundingMaklumatEocp::whereIn('id', $deleteIds)->delete();
                }
                

                
                if($request->has('sa')) {
                    foreach($request->sa as $sa_details){
                        $sa_json = json_decode($sa_details,TRUE);
                        $kos = 0;
                        if(is_numeric($sa_json['kos'])) {
                            $kos = $sa_json['kos'];
                        }
                        $sa = PerundingMaklumatSa::create([
                            'pemantauan_id' => $request->pemantauan_id,
                            'perolehan_id' => $request->perolehan_id,
                            'maklumat_id' => $maklumatPerunding->id,
                            'tarikh' => $sa_json['tarikh'],
                            'implikasi_kos' => $kos,
                            'created_at' =>  Carbon::now()->format('Y-m-d H:i:s'),            
                            'dibuat_pada' => Carbon::now()->format('Y-m-d H:i:s'),
                            'dibuat_oleh' => $request->user_id,
                            'dikemaskini_oleh' => $request->user_id,
                            'dikemaskini_pada' => Carbon::now()->format('Y-m-d H:i:s'),
                        ]);
                    }
                }
                
                if($request->has('perlinduganLanjutan')) {
                    foreach($request->perlinduganLanjutan as $perlindugan_lanjutan_details){
                        $perlindugan_lanjutan_json = json_decode($perlindugan_lanjutan_details,TRUE);
                        $sa = PerundingMaklumatPerlindungan::create([
                            'pemantauan_id' => $request->pemantauan_id,
                            'perolehan_id' => $request->perolehan_id,
                            'maklumat_id' => $maklumatPerunding->id,
                            'tarikh_mula' => $perlindugan_lanjutan_json['tarikhMula'],
                            'tarikh_tamat' => $perlindugan_lanjutan_json['tarikTamat'],
                            'created_at' =>  Carbon::now()->format('Y-m-d H:i:s'),            
                            'dibuat_pada' => Carbon::now()->format('Y-m-d H:i:s'),
                            'dibuat_oleh' => $request->user_id,
                            'dikemaskini_oleh' => $request->user_id,
                            'dikemaskini_pada' => Carbon::now()->format('Y-m-d H:i:s'),
                        ]);
                    }
                }

                if($request->has('lsst_file_name')) {
                    $maklumatPerunding->clearMediaCollection('lsst');
                    $maklumatPerunding->addMedia($request->file('lsst_file_name'))
                            ->toMediaCollection('lsst','perunding');
                }

                if($request->has('Tr_file_name')) {
                    $maklumatPerunding->clearMediaCollection('tr');
                    $maklumatPerunding->addMedia($request->file('Tr_file_name'))
                            ->toMediaCollection('tr','perunding');
                }

                if($request->has('sb_file_name')) {
                    $maklumatPerunding->clearMediaCollection('sb');
                    $maklumatPerunding->addMedia($request->file('sb_file_name'))
                            ->toMediaCollection('sb','perunding');
                }

                if($request->has('ba_file_name')) {
                    $maklumatPerunding->clearMediaCollection('ba');
                    $maklumatPerunding->addMedia($request->file('ba_file_name'))
                            ->toMediaCollection('ba','perunding');
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
                'data' => $maklumatPerunding,
                
            ]);

        } catch (\Throwable $th) {

            logger()->error($th->getMessage());

            return response()->json([
                'code' => '500',
                'status' => 'Failed',
                'error' => $th,
            ]);
        }    
        
    }
}
