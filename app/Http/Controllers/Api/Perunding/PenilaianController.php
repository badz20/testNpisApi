<?php

namespace App\Http\Controllers\Api\Perunding;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Perunding\PerundingPenilaian;
use App\Models\Perunding\PerundingPenilaianHistory;
use App\Models\Perunding\PerundingPrestasi;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Carbon;

class PenilaianController extends Controller
{
    //
    public function store(Request $request)
    {

        try {
            $validator = Validator::make($request->all(), [
                'perolehan_id' => ['required', 'integer'],
                'pemantauan_id' => ['required', 'integer'],
                'deliverable' => ['required', 'string'],
            ]);

            $highestVersion = PerundingPrestasi::select('version_no')
                ->where('pemantauan_id', $request->pemantauan_id)
                ->where('perolehan_id', $request->perolehan_id)
                ->max('version_no');

            $prestasiPerunding = PerundingPrestasi::where('pemantauan_id', $request->pemantauan_id)
                ->where('perolehan_id', $request->perolehan_id)
                ->where('deliverable', $request->deliverable)
                ->where('version_no', $highestVersion)
                ->whereNot('keputusan', 'Gagal')
                ->where('is_readonly', 0)
                ->first();

            if (!$prestasiPerunding) {
                return response()->json([
                    'code' => '400',
                    'status' => 'No Deliverable in Prestasi',
                    'message' => 'Tiada deliverable dalam prestasi kemajuan, Sila tambah deliverable dahulu sebelum lakukan penilaian',
                ]);
            }

            if (!$validator->fails()) {

                $penilaianPerunding = PerundingPenilaian::where('pemantauan_id', $request->pemantauan_id)
                    ->where('perolehan_id', $request->perolehan_id)
                    ->where('deliverable', $request->deliverable)
                    ->first();

                if ($penilaianPerunding) {
                    PerundingPenilaian::where('id', $penilaianPerunding->id)->update([
                        'deliverable' => $request->deliverable,
                        'jadual_pelaksanaan' => $request->JadualPelaksanaan,
                        'skop_perkhidmatan' => $request->SkopPerkhidmatan,
                        'pengurusan_sumber' => $request->PengurusanSumber,
                        'keupayaan_teknikal' => $request->KeupayaanTeknikal,
                        'kualiti_kerja' => $request->KualitiKerja,
                        'kerjasama' => $request->Kerjasama,
                        'peruntukan_diluluskan' => $request->PeruntukanDiluluskan,
                        'pengawasan' => $request->Pengawasan,
                        'lemah_jumlah' => $request->lemahJumlah,
                        'sederhana_jumlah' => $request->sederhanaJumlah,
                        'baik_jumlah' => $request->baikJumlah,
                        'sangat_baik_jumlah' => $request->sangatBaikJumlah,
                        'total_jumlah' => $request->totalJumlah,
                        'penilaian_keseluruhan' => $request->penilaianKeseluruhan,
                        'is_disyorkan' => $request->disyorkan,
                        'is_pengawasan' => $request->tidak_checkbox,
                        'catatan' => $request->catatan,
                        'tarikh_penilaian' => Carbon::now()->format('Y-m-d H:i:s'),

                        'dikemaskini_oleh' => $request->user_id,
                        'dikemaskini_pada' => Carbon::now()->format('Y-m-d H:i:s'),
                    ]);
                } else {
                    $penilaianPerunding = PerundingPenilaian::create([
                        'pemantauan_id' => $request->pemantauan_id,
                        'perolehan_id' => $request->perolehan_id,
                        'deliverable' => $request->deliverable,
                        'jadual_pelaksanaan' => $request->JadualPelaksanaan,
                        'skop_perkhidmatan' => $request->SkopPerkhidmatan,
                        'pengurusan_sumber' => $request->PengurusanSumber,
                        'keupayaan_teknikal' => $request->KeupayaanTeknikal,
                        'kualiti_kerja' => $request->KualitiKerja,
                        'kerjasama' => $request->Kerjasama,
                        'peruntukan_diluluskan' => $request->PeruntukanDiluluskan,
                        'pengawasan' => $request->Pengawasan,
                        'lemah_jumlah' => $request->lemahJumlah,
                        'sederhana_jumlah' => $request->sederhanaJumlah,
                        'baik_jumlah' => $request->baikJumlah,
                        'sangat_baik_jumlah' => $request->sangatBaikJumlah,
                        'total_jumlah' => $request->totalJumlah,
                        'penilaian_keseluruhan' => $request->penilaianKeseluruhan,
                        'is_disyorkan' => $request->disyorkan,
                        'is_pengawasan' => $request->tidak_checkbox,
                        'catatan' => $request->catatan,
                        'tarikh_penilaian' => Carbon::now()->format('Y-m-d H:i:s'),
                        'created_at' =>  Carbon::now()->format('Y-m-d H:i:s'),
                        'dibuat_pada' => Carbon::now()->format('Y-m-d H:i:s'),
                        'dibuat_oleh' => $request->user_id,
                        'dikemaskini_oleh' => $request->user_id,
                        'dikemaskini_pada' => Carbon::now()->format('Y-m-d H:i:s'),
                    ]);
                }


                if ($prestasiPerunding) {
                    $prestasiPerunding->penilaian = $request->penilaianKeseluruhan;
                    $prestasiPerunding->save();
                }
                PerundingPenilaianHistory::create([
                    'pemantauan_id' => $request->pemantauan_id,
                    'perolehan_id' => $request->perolehan_id,
                    'dikemaskini_oleh' => $request->user_id,
                    'bahagian' => 'Penilaian'
                ]);
            } else {
                return response()->json([
                    'code' => '422',
                    'status' => 'Unprocessable Entity',
                    'data' => $validator->errors(),
                ]);
            }

            return response()->json([
                'code' => '200',
                'status' => 'Success',
                'data' => $penilaianPerunding,

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

    public function edit(Request $request)
    {
        try {

            if ($request->deliverable != '') {
                $penilaianPerunding = PerundingPenilaian::where('pemantauan_id', $request->pemantauan_id)
                    ->where('perolehan_id', $request->perolehan_id)
                    ->where('deliverable', $request->deliverable)
                    ->first();
            } else {
                $penilaianPerunding = PerundingPenilaian::where('pemantauan_id', $request->pemantauan_id)
                    ->where('perolehan_id', $request->perolehan_id)
                    ->first();
            }


            return response()->json([
                'code' => '200',
                'status' => 'Success',
                'data' => $penilaianPerunding,

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

    public function list(Request $request)
    {
        try {

            $penilaianPerunding = PerundingPenilaian::where('pemantauan_id', $request->pemantauan_id)
                ->where('perolehan_id', $request->perolehan_id)
                ->with('deliverables')
                ->get();

            return response()->json([
                'code' => '200',
                'status' => 'Success',
                'data' => $penilaianPerunding,

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

    public function getPenilaianSejarah(Request $request)
    {
        try {
            $prestasiSejarahPerunding = PerundingPenilaianHistory::where('pemantauan_id', $request->pemantauan_id)
                ->where('perolehan_id', $request->perolehan_id)
                ->with('updatedBy', 'updatedBy.bahagian')
                ->get();

            return response()->json([
                'code' => '200',
                'status' => 'Success',
                'data' => $prestasiSejarahPerunding,

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

    public function getLatestPenilaianSejarah(Request $request)
    {
        try {
            $prestasiSejarahPerunding = PerundingPenilaianHistory::latest()
                ->where('pemantauan_id', $request->pemantauan_id)
                ->where('perolehan_id', $request->perolehan_id)
                ->with('updatedBy', 'updatedBy.bahagian')
                ->first();

            return response()->json([
                'code' => '200',
                'status' => 'Success',
                'data' => $prestasiSejarahPerunding,

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
