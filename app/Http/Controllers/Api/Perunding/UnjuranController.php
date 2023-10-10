<?php

namespace App\Http\Controllers\Api\Perunding;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Carbon;
use App\Models\Perunding\PerundingKewanganUnjuran;
use App\Models\Perunding\PerundingRekodBayaranModel;
use App\Models\Perunding\PemantauanPerolehan;
use App\Models\PemantauanProject;
use Illuminate\Support\Facades\Log;

class UnjuranController extends Controller
{
    //

    public function list(Request $request)
    {

        try {
            Log::info($request);
            
            $result['unjuran'] = PerundingKewanganUnjuran::where('pemantauan_id',$request->pemantauan_id)
                        ->where('perolehan_id',$request->perolehan_id)
                        ->get();

            $result['rekord_bayaran'] = PerundingRekodBayaranModel::where('pemantauan_id',$request->pemantauan_id)
                        ->where('perolehan',$request->perolehan_id)
                        ->get();

            $result['pemantuan'] = PemantauanProject::whereId($request->pemantauan_id)->first();
            $result['perolehan'] = PemantauanPerolehan::whereId($request->perolehan_id)->first();
                        
            return response()->json([
                'code' => '200',
                'status' => 'Success',
                'data' => $result,
                
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

                foreach ($request->unjuranDetails as $unjuraniDetails) {
                    $unjuran_json = json_decode($unjuraniDetails, TRUE);
                    if ($unjuran_json['id'] == '' ) {
                        $unjuran = PerundingKewanganUnjuran::create([
                            'pemantauan_id' => $request->pemantauan_id,
                            'perolehan_id' => $request->perolehan_id,
                            'tahun' => $unjuran_json['tahun'],
                            'bulan' => $unjuran_json['bulan'],
                            'unjuran' => $unjuran_json['unjuran'],
                            'jumlah_unjuran' => $unjuran_json['jumlahUnjuran'],
                            'prestasi_jadual' => $unjuran_json['prestasiJadual'],
                            'dibuat_pada' => Carbon::now()->format('Y-m-d H:i:s'),
                            'dibuat_oleh' => $request->user_id,
                            'dikemaskini_oleh' => $request->user_id,
                            'dikemaskini_pada' => Carbon::now()->format('Y-m-d H:i:s'),
                        ]);
                    }else {
                        PerundingKewanganUnjuran::where('pemantauan_id', $request->pemantauan_id)
                        ->where('perolehan_id', $request->perolehan_id)
                        ->where('id', $unjuran_json['id'])
                        ->update([
                            'tahun' => $unjuran_json['tahun'],
                            'bulan' => $unjuran_json['bulan'],
                            'unjuran' => $unjuran_json['unjuran'],
                            'jumlah_unjuran' => $unjuran_json['jumlahUnjuran'],
                            'prestasi_jadual' => $unjuran_json['prestasiJadual'],
                            'dikemaskini_oleh' => $request->user_id,
                            'dikemaskini_pada' => Carbon::now()->format('Y-m-d H:i:s'),
                        ]);

                        $unjuran = PerundingKewanganUnjuran::where('pemantauan_id', $request->pemantauan_id)
                                ->where('perolehan_id', $request->perolehan_id)
                                ->where('id', $unjuran_json['id'])->first();
                    }
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
                'data' => '',
                
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