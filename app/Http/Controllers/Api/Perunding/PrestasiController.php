<?php

namespace App\Http\Controllers\Api\Perunding;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Perunding\PerundingPrestasi;
use App\Models\Perunding\PerundingPrestasiRekordLapiran;
use App\Models\Perunding\PemantauanPerolehan;
use App\Models\Perunding\PerundingPrestasiChangeHistory;
use App\Models\Perunding\PerundingPrestasiHistory;
use App\Models\Perunding\PerundingPrestasiMasalah;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Carbon;

class PrestasiController extends Controller
{
    //

    public function store(Request $request)
    {

        try {
            $validator = Validator::make($request->all(), [
                'perolehan_id' => ['required', 'integer'],
                'pemantauan_id' => ['required', 'integer'],
            ]);

            $version_no = $this->getHighestVersionNo($request->pemantauan_id, $request->perolehan_id);

            if ($version_no) {
                $latest_version = $version_no;
            } else {
                $latest_version = 1;
            }


            if (!$validator->fails()) {

                // $this->closeCompletedDeliverable($request, $latest_version);

                $checking_result = $this->deliverableCheckings($request,$latest_version);

                if($checking_result['code'] == 400) {
                    return response()->json($checking_result);
                }

                // if ($this->isSameDeliverables($request)) {
                //     return response()->json([
                //         'code' => '400',
                //         'status' => 'Mutilple Deliverables',
                //         'message' => 'Tidak dibenarkan memilih deliverable yang sama',
                //     ]);
                // }

                // if (!$this->isDeliverablesCompleted($request, $latest_version)) {
                //     return response()->json([
                //         'code' => '400',
                //         'status' => 'Deliverables not completed',
                //         'message' => 'Cannot add new deliverables before completing previous ones',
                //     ]);
                // }

                $newPrestasiIds = [];
                if ($request->has('prestasiDetails')) {

                    $is_make_history = $this->checkHistoryChange($request);

                    $spFileCounter = 0;
                    $eotFileCounter = 0;

                    if ($request->has('spFile')) {
                        $spFiles = $request->file('spFile');
                    }

                    if ($request->has('eotFile')) {
                        $eotFiles = $request->file('eotFile');
                    }

                    // $this->isDifferentDeliverables($request, $latest_version);
                    $order_no = 1;
                    foreach ($request->prestasiDetails as $prestasiDetails) {
                        $prestasi_json = json_decode($prestasiDetails, TRUE);

                        if ($prestasi_json['version'] == '' && $prestasi_json['id'] == '') {

                            $prestasi = PerundingPrestasi::create([
                                'pemantauan_id' => $request->pemantauan_id,
                                'perolehan_id' => $request->perolehan_id,
                                'tahun' => $prestasi_json['tahun'],
                                'bulan' => $prestasi_json['bulan'],
                                'deliverable' => $prestasi_json['deliverable'],
                                'emel' => $prestasi_json['emel'],
                                'tarikh_mula_jadual' => $prestasi_json['tarikhMulaJadual'],
                                'tarikh_mula_sebenar' => $prestasi_json['tarikhMulaSebenar'],
                                'tarikh_siap_jadual' => $prestasi_json['tarikhSiapJadual'],
                                'tarikh_siap_sebenar' => $prestasi_json['tarikhSiapSebenar'],
                                'hari_lewat_awal' => $prestasi_json['hariLewat'],
                                'peratus_jadual' => $prestasi_json['peratusJadual'],
                                'peratus_sebenar' => $prestasi_json['peratusSebenar'],
                                'status_pelaksanaan' => $prestasi_json['statusPelaksanaan'],
                                'tarikh_mesyuarat' => $prestasi_json['tarikhMesyuarat'],
                                'keputusan' => $prestasi_json['keputusan'],
                                'penilaian' => $prestasi_json['penilaian'],
                                'EOT' => $prestasi_json['EOT'],
                                'tarikh_lad_mula' => $prestasi_json['tarikhLadMula'],
                                'tarikh_lad_tamat' => $prestasi_json['tarikhLadTamat'],
                                'bilangan_hari_lad' => $prestasi_json['bilanganHariLad'],
                                'jumlah_lad_terkumpul' => $prestasi_json['jumlahLad'],
                                'tarikh_kemaskini' => Carbon::now()->format('Y-m-d'),
                                'version_no' => $latest_version,
                                'order_no' => $order_no,
                                'created_at' =>  Carbon::now()->format('Y-m-d H:i:s'),
                                'dibuat_pada' => Carbon::now()->format('Y-m-d H:i:s'),
                                'dibuat_oleh' => $request->user_id,
                                'dikemaskini_oleh' => $request->user_id,
                                'dikemaskini_pada' => Carbon::now()->format('Y-m-d H:i:s'),
                            ]);

                            $order_no = $order_no + 1;
                            if ($prestasi_json['keputusan'] == 'Gagal' && $prestasi_json['is_readonly'] == '0') {
                                $new_prestasi = $this->copyGagalRow($prestasi, $prestasi_json, $request, $latest_version,$order_no);
                                $order_no = $order_no + 1;
                                array_push($newPrestasiIds, $new_prestasi->id);
                            }
                            array_push($newPrestasiIds, $prestasi->id);
                        } else {
                            PerundingPrestasi::where('pemantauan_id', $request->pemantauan_id)
                                ->where('perolehan_id', $request->perolehan_id)
                                ->where('id', $prestasi_json['id'])
                                ->update([
                                    'tahun' => $prestasi_json['tahun'],
                                    'bulan' => $prestasi_json['bulan'],
                                    'deliverable' => $prestasi_json['deliverable'],
                                    'emel' => $prestasi_json['emel'],
                                    'tarikh_mula_jadual' => $prestasi_json['tarikhMulaJadual'],
                                    'tarikh_mula_sebenar' => $prestasi_json['tarikhMulaSebenar'],
                                    'tarikh_siap_jadual' => $prestasi_json['tarikhSiapJadual'],
                                    'tarikh_siap_sebenar' => $prestasi_json['tarikhSiapSebenar'],
                                    'hari_lewat_awal' => $prestasi_json['hariLewat'],
                                    'peratus_jadual' => $prestasi_json['peratusJadual'],
                                    'peratus_sebenar' => $prestasi_json['peratusSebenar'],
                                    'status_pelaksanaan' => $prestasi_json['statusPelaksanaan'],
                                    'tarikh_mesyuarat' => $prestasi_json['tarikhMesyuarat'],
                                    'keputusan' => $prestasi_json['keputusan'],
                                    'penilaian' => $prestasi_json['penilaian'],
                                    'EOT' => $prestasi_json['EOT'],
                                    'tarikh_lad_mula' => $prestasi_json['tarikhLadMula'],
                                    'tarikh_lad_tamat' => $prestasi_json['tarikhLadTamat'],
                                    'bilangan_hari_lad' => $prestasi_json['bilanganHariLad'],
                                    'jumlah_lad_terkumpul' => $prestasi_json['jumlahLad'],
                                    'order_no' => $order_no,
                                    'tarikh_kemaskini' => Carbon::now()->format('Y-m-d'),
                                    'dikemaskini_oleh' => $request->user_id,
                                    'dikemaskini_pada' => Carbon::now()->format('Y-m-d H:i:s'),
                                ]);

                            $order_no = $order_no + 1;
                            $prestasi = PerundingPrestasi::where('pemantauan_id', $request->pemantauan_id)
                                ->where('perolehan_id', $request->perolehan_id)
                                ->where('id', $prestasi_json['id'])->first();

                            if ($prestasi_json['keputusan'] == 'Gagal' && $prestasi_json['is_readonly'] == '0') {
                                $new_prestasi = $this->copyGagalRow($prestasi, $prestasi_json, $request, $latest_version,$order_no);
                                $order_no = $order_no + 1;
                                array_push($newPrestasiIds, $new_prestasi->id);
                            }
                            array_push($newPrestasiIds, $prestasi_json['id']);
                        }

                        if ($prestasi_json['spFile'] && $request->has('spFile')) {

                            if ($spFiles[$spFileCounter]) {
                                $prestasi->clearMediaCollection('sp');
                                $prestasi->addMedia($spFiles[$spFileCounter])
                                    ->toMediaCollection('sp', 'perunding');
                            }

                            $spFileCounter = $spFileCounter + 1;
                        }

                        if ($prestasi_json['eotFile'] && $request->has('eotFile')) {

                            if ($eotFiles[$eotFileCounter]) {
                                $prestasi->clearMediaCollection('eot');
                                $prestasi->addMedia($eotFiles[$eotFileCounter])
                                    ->toMediaCollection('eot', 'perunding');
                            }

                            $eotFileCounter = $eotFileCounter + 1;
                        }
                    }

                    $existingIds = PerundingPrestasi::where('version_no', $latest_version)
                                                        ->where('pemantauan_id', $request->pemantauan_id)
                                                        ->where('perolehan_id', $request->perolehan_id)
                                                        ->get()
                                                        ->pluck('id')
                                                        ->toArray();
                    $deleteIds = array_diff($existingIds, $newPrestasiIds);
                    deleteMedia($deleteIds, 'App\Models\Perunding\PerundingPrestasi');
                    PerundingPrestasiRekordLapiran::whereIn('prestasi_id', $deleteIds)->delete();
                    PerundingPrestasi::whereIn('id', $deleteIds)->delete();

                    if ($is_make_history) {
                        $this->makeHistory($request);
                    }

                    PerundingPrestasiChangeHistory::create([
                        'pemantauan_id' => $request->pemantauan_id,
                        'perolehan_id' => $request->perolehan_id,
                        'dikemaskini_oleh' => $request->user_id,
                        'bahagian' => 'Prestasi'
                    ]);
                } else {
                    $existingIds = PerundingPrestasi::where('version_no', $latest_version)->get()->pluck('id')->toArray();
                    $deleteIds = array_diff($existingIds, []);
                    deleteMedia($deleteIds, 'App\Models\Perunding\PerundingPrestasi');
                    PerundingPrestasiRekordLapiran::whereIn('prestasi_id', $deleteIds)->delete();
                    PerundingPrestasi::whereIn('id', $deleteIds)->delete();
                }
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

    public function edit(Request $request)
    {
        try {
            $version_no = $this->getHighestVersionNo($request->pemantauan_id, $request->perolehan_id);

            $prestasiPerunding = PerundingPrestasi::where('pemantauan_id', $request->pemantauan_id)
                ->where('perolehan_id', $request->perolehan_id)
                ->where('version_no', $version_no)
                ->with(['media', 'masalah'])
                ->orderBy('order_no')
                //                     ->with(['eocp','sa','perlindugan','perolehanProject','pemantauanProject','pemantauanProject.negeri','pemantauanProject.bahagianPemilik'])
                ->get();

            $prestasiPerundingActive = PerundingPrestasi::where('pemantauan_id', $request->pemantauan_id)
                ->where('perolehan_id', $request->perolehan_id)
                ->where('version_no', $version_no)
                ->where('is_readonly', 0)
                ->with(['media', 'masalah'])
                ->first();

            $prestasiChangeHistory =  PerundingPrestasiChangeHistory::where('pemantauan_id', $request->pemantauan_id)
                                    ->where('perolehan_id', $request->perolehan_id)
                                    ->latest()
                                    ->first();

            $all_version = PerundingPrestasi::select('version_no')->distinct()->get();
            $all_version_data = [];
            foreach ($all_version as $version) {
                if ($version['version_no'] != $version_no) {
                    $versionData = PerundingPrestasi::where('pemantauan_id', $request->pemantauan_id)
                        ->where('perolehan_id', $request->perolehan_id)
                        ->where('version_no', $version['version_no'])
                        ->with(['media', 'masalah'])
                        ->orderBy('order_no')
                        ->get();

                    if (count($versionData) > 0) {
                        array_push($all_version_data, $versionData);
                    }
                }
            }

            $perolehan = PemantauanPerolehan::where('pemantauan_id', $request->pemantauan_id)
                ->with('pemantauanProject')
                ->whereId($request->perolehan_id)
                ->first();

            $data['prestasiPerunding']    = $prestasiPerunding;
            $data['all_version'] =  $all_version_data;
            $data['perolehan']    = $perolehan;
            $data['prestasiPerundingActive']    = $prestasiPerundingActive;
            $data['prestasiChangeHistory'] = $prestasiChangeHistory;

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

    public function getMasalahById(Request $request)
    {
        try {
            $prestasiMasalahPerunding = PerundingPrestasiMasalah::where('pemantauan_id', $request->pemantauan_id)
                ->where('perolehan_id', $request->perolehan_id)
                ->where('prestasi_id', $request->prestasi_id)
                //                     ->with(['eocp','sa','perlindugan','perolehanProject','pemantauanProject','pemantauanProject.negeri','pemantauanProject.bahagianPemilik'])
                ->first();

            return response()->json([
                'code' => '200',
                'status' => 'Success',
                'data' => $prestasiMasalahPerunding,

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

    public function getMasalah(Request $request)
    {
        try {
            $prestasiMasalahPerunding = PerundingPrestasiMasalah::where('pemantauan_id', $request->pemantauan_id)
                ->where('perolehan_id', $request->perolehan_id)
                ->get();

            return response()->json([
                'code' => '200',
                'status' => 'Success',
                'data' => $prestasiMasalahPerunding,

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

    public function getPrestasiSejarah(Request $request)
    {
        try {
            $prestasiSejarahPerunding = PerundingPrestasiChangeHistory::where('pemantauan_id', $request->pemantauan_id)
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

    public function getLatestPrestasiSejarah(Request $request)
    {
        try {
            $prestasiSejarahPerunding = PerundingPrestasiChangeHistory::latest()
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

    public function storeMasalah(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'perolehan_id' => ['required', 'integer'],
                'pemantauan_id' => ['required', 'integer'],
            ]);

            if (!$validator->fails()) {
                $prestasiMasalah = PerundingPrestasiMasalah::where('pemantauan_id', $request->pemantauan_id)
                    ->where('perolehan_id', $request->perolehan_id)
                    ->where('prestasi_id', $request->masalah_id)
                    ->first();
                if (!$prestasiMasalah) {
                    PerundingPrestasiMasalah::create([
                        'pemantauan_id' => $request->pemantauan_id,
                        'perolehan_id' => $request->perolehan_id,
                        'prestasi_id' => $request->masalah_id,
                        'masalah' => $request->masalah_catatan,
                        'tarikh_masalah' => Carbon::now()->format('Y-m-d'),
                        'created_at' =>  Carbon::now()->format('Y-m-d H:i:s'),
                        'dibuat_pada' => Carbon::now()->format('Y-m-d H:i:s'),
                        'dibuat_oleh' => $request->user_id,
                        'dikemaskini_oleh' => $request->user_id,
                        'dikemaskini_pada' => Carbon::now()->format('Y-m-d H:i:s'),
                    ]);
                } else {
                    PerundingPrestasiMasalah::where('pemantauan_id', $request->pemantauan_id)
                        ->where('perolehan_id', $request->perolehan_id)
                        ->where('prestasi_id', $request->masalah_id)
                        ->update([
                            'masalah' => $request->masalah_catatan,
                        ]);
                }
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

    private function getHighestVersionNo($pemantauan_id, $perolehan_id)
    {

        $highestVersion = PerundingPrestasi::select('version_no')
            ->where('pemantauan_id', $pemantauan_id)
            ->where('perolehan_id', $perolehan_id)
            ->max('version_no');

        return $highestVersion;
    }

    private function checkHistoryChange($request)
    {
        foreach ($request->prestasiDetails as $prestasiDetails) {
            $prestasi_json = json_decode($prestasiDetails, TRUE);

            if ($prestasi_json['version'] == '' && $prestasi_json['id'] == '') {

                if ($prestasi_json['tarikhLadMula'] != null || $prestasi_json['EOT'] != null) {
                    //makehistory
                    return TRUE;
                }
            } else {
                $prestasi = PerundingPrestasi::where('pemantauan_id', $request->pemantauan_id)
                    ->where('perolehan_id', $request->perolehan_id)
                    ->where('id', $prestasi_json['id'])->first();

                if ($prestasi->tarikh_lad_mula != $prestasi_json['tarikhLadMula'] || $prestasi->EOT != $prestasi_json['EOT']) {
                    //makehistory
                    return TRUE;
                }
            }
        }

        return FALSE;
    }

    private function makeHistory($request)
    {
        $latest_version = $this->getHighestVersionNo($request->pemantauan_id, $request->perolehan_id);

        $latestPrestasiVersionData = PerundingPrestasi::where('pemantauan_id', $request->pemantauan_id)
            ->where('perolehan_id', $request->perolehan_id)
            ->where('version_no', $latest_version)->get();
        $new_version = $latest_version + 1;

        foreach ($latestPrestasiVersionData as $prestasi) {

            $is_readonly = $prestasi->is_readonly;;
            if ($prestasi->keputusan == 'Gagal') {
                $is_readonly = TRUE;
            }

            $new_prestasi = PerundingPrestasi::create([
                'pemantauan_id' => $prestasi->pemantauan_id,
                'perolehan_id' => $prestasi->perolehan_id,
                'tahun' => $prestasi->tahun,
                'bulan' => $prestasi->bulan,
                'deliverable' => $prestasi->deliverable,
                'emel' => $prestasi->emel,
                'tarikh_mula_jadual' => $prestasi->tarikh_mula_jadual,
                'tarikh_mula_sebenar' => $prestasi->tarikh_mula_sebenar,
                'tarikh_siap_jadual' => $prestasi->tarikh_siap_jadual,
                'tarikh_siap_sebenar' => $prestasi->tarikh_siap_sebenar,
                'hari_lewat_awal' => $prestasi->hari_lewat_awal,
                'peratus_jadual' => $prestasi->peratus_jadual,
                'peratus_sebenar' => $prestasi->peratus_sebenar,
                'status_pelaksanaan' => $prestasi->status_pelaksanaan,
                'tarikh_mesyuarat' => $prestasi->tarikh_mesyuarat,
                'keputusan' => $prestasi->keputusan,
                'penilaian' => $prestasi->penilaian,
                'EOT' => $prestasi->EOT,
                'tarikh_lad_mula' => $prestasi->tarikh_lad_mula,
                'tarikh_lad_tamat' => $prestasi->tarikh_lad_tamat,
                'bilangan_hari_lad' => $prestasi->bilangan_hari_lad,
                'jumlah_lad_terkumpul' => $prestasi->jumlah_lad_terkumpul,
                'tarikh_kemaskini' => $prestasi->tarikh_kemaskini,
                'version_no' => $new_version,
                'is_readonly' => $is_readonly,
                'order_no' => $prestasi->order_no,
                'is_gagal_row' => $prestasi->is_gagal_row,
                'created_at' =>  Carbon::now()->format('Y-m-d H:i:s'),
                'dibuat_pada' => Carbon::now()->format('Y-m-d H:i:s'),
                'dibuat_oleh' => $request->user_id,
                'dikemaskini_oleh' => $request->user_id,
                'dikemaskini_pada' => Carbon::now()->format('Y-m-d H:i:s'),
            ]);


            $medias = $prestasi->getMedia('sp');

            foreach ($medias as $medium) {
                $copiedMedia = $medium->copy($new_prestasi, $medium->collection_name, 'perunding');
            }

            $medias = $prestasi->getMedia('eot');

            foreach ($medias as $medium) {
                $copiedMedia = $medium->copy($new_prestasi, $medium->collection_name, 'perunding');
            }

            $new_prestasi->save();
        }

        PerundingPrestasi::where('pemantauan_id', $request->pemantauan_id)
            ->where('perolehan_id', $request->perolehan_id)
            ->where('version_no', $latest_version)
            ->update([
                'is_readonly' => true,
                'dikemaskini_oleh' => $request->user_id,
                'dikemaskini_pada' => Carbon::now()->format('Y-m-d H:i:s')
            ]);
    }

    private function copyGagalRow($prestasi, $prestasi_json, $request, $latest_version,$order_no)
    {
        $prestasi->is_readonly = TRUE;
        $prestasi->save();
        $new_prestasi = PerundingPrestasi::create([
            'pemantauan_id' => $request->pemantauan_id,
            'perolehan_id' => $request->perolehan_id,
            'tahun' => $prestasi_json['tahun'],
            'bulan' => $prestasi_json['bulan'],
            'deliverable' => $prestasi_json['deliverable'],
            'emel' => $prestasi_json['emel'],
            'tarikh_mula_jadual' => $prestasi_json['tarikhMulaJadual'],
            'tarikh_mula_sebenar' => $prestasi_json['tarikhMulaSebenar'],
            'tarikh_siap_jadual' => $prestasi_json['tarikhSiapJadual'],
            // 'tarikh_siap_sebenar' => $prestasi_json['tarikhSiapSebenar'],
            // 'hari_lewat_awal' => $prestasi_json['hariLewat'],
            'hari_lewat_awal' => 0,
            'peratus_jadual' => $prestasi_json['peratusJadual'],
            'peratus_sebenar' => $prestasi_json['peratusSebenar'],
            'status_pelaksanaan' => $prestasi_json['statusPelaksanaan'],
            'tarikh_mesyuarat' => $prestasi_json['tarikhMesyuarat'],
            'keputusan' => 'Lulus',
            'penilaian' => $prestasi_json['penilaian'],
            'EOT' => $prestasi_json['EOT'],
            'tarikh_lad_mula' => $prestasi_json['tarikhLadMula'],
            'tarikh_lad_tamat' => $prestasi_json['tarikhLadTamat'],
            'bilangan_hari_lad' => $prestasi_json['bilanganHariLad'],
            'jumlah_lad_terkumpul' => $prestasi_json['jumlahLad'],
            'tarikh_kemaskini' => Carbon::now()->format('Y-m-d'),
            'version_no' => $latest_version,
            'order_no' => $order_no,
            'is_gagal_row' => true,
            'created_at' =>  Carbon::now()->format('Y-m-d H:i:s'),
            'dibuat_pada' => Carbon::now()->format('Y-m-d H:i:s'),
            'dibuat_oleh' => $request->user_id,
            'dikemaskini_oleh' => $request->user_id,
            'dikemaskini_pada' => Carbon::now()->format('Y-m-d H:i:s'),
        ]);

        $medias = $prestasi->getMedia('sp');

        foreach ($medias as $medium) {
            $copiedMedia = $medium->copy($new_prestasi, $medium->collection_name, 'perunding');
        }

        $medias = $prestasi->getMedia('eot');

        foreach ($medias as $medium) {
            $copiedMedia = $medium->copy($new_prestasi, $medium->collection_name, 'perunding');
        }

        $new_prestasi->save();

        return $new_prestasi;
    }

    private function deliverableCheckings($request,$latest_version) {
        $new_deliverables = [];
        $existing_completed_deliverables = [];
        $existing_not_completed_deliverables = [];
        $new_completed_deliverables = [];
        $same_deliverables = [];
        $existing_deliverables =[];

        

        $result = [
            'code' => '200',
            'status' => '',
            'message' => '',
        ];

        $existing_db_not_completed_deliverables = PerundingPrestasi::where('pemantauan_id', $request->pemantauan_id)
                ->where('perolehan_id', $request->perolehan_id)
                ->where('version_no', $latest_version)
                ->whereNull('tarikh_siap_sebenar')
                ->pluck('id')
                ->toArray();

        $existing_db_deliverables = PerundingPrestasi::where('pemantauan_id', $request->pemantauan_id)
                ->where('perolehan_id', $request->perolehan_id)
                ->where('version_no', $latest_version)
                ->pluck('deliverable')
                ->toArray();
        if ($request->has('prestasiDetails')) {
            foreach ($request->prestasiDetails as $prestasiDetails) {
                $prestasi_json = json_decode($prestasiDetails, TRUE);
                if ($prestasi_json['version'] == '' && $prestasi_json['id'] == '') {
                    array_push($new_deliverables,$prestasi_json);
                }

                if ($prestasi_json['keputusan'] != 'Gagal' && in_array($prestasi_json['deliverable'],$existing_deliverables)) {
                    array_push($same_deliverables,$prestasi_json);
                }else {
                    if($prestasi_json['keputusan'] == 'Lulus') {
                        array_push($existing_deliverables,$prestasi_json['deliverable']);
                    }
                    
                }

                if ($prestasi_json['id'] != '' && ($prestasi_json['tarikhSiapSebenar'] == '' || $prestasi_json['tarikhSiapSebenar'] == null) && $prestasi_json['is_readonly'] == 0) {
                    array_push($existing_not_completed_deliverables,$prestasi_json);
                }

                if ($prestasi_json['id'] != '' && $prestasi_json['tarikhSiapSebenar'] && $prestasi_json['is_readonly'] == 0 &&  in_array($prestasi_json['id'],$existing_db_not_completed_deliverables)) {
                    array_push($new_completed_deliverables,$prestasi_json);
                }

                if ($prestasi_json['is_readonly'] == 1) {
                    array_push($existing_completed_deliverables,$prestasi_json);
                }
                
            }

            //Check if new deliverable has more then 1
            // if(count($new_deliverables) > 1) {
            //     $result['code'] = 400;
            //     $result['status'] = 'Cannot have more than one deliverable';
            //     $result['message'] = 'Cannot have more than one deliverable';

            //     return $result;
            // }

            //Check if new deliverable has already existing deliverable
            if(count($same_deliverables) > 0) {
                $result['code'] = 400;
                $result['status'] = 'Tidak boleh memilih deliverable yang sama';
                $result['message'] = 'Tidak boleh memilih deliverable yang sama';
                return $result;
            }

            //check if old deliverable not completed and have new deliverable
            // if(count($existing_not_completed_deliverables) > 0 && count($new_deliverables) > 0) {
            //     $result['code'] = 400;
            //     $result['status'] = 'Need to complete old deliverables first';
            //     $result['message'] = 'Need to complete old deliverables first';
            //     return $result;
            // }

            if(count($new_completed_deliverables) > 0) {
                foreach ($new_completed_deliverables as $deliverable) {
                    // $deliverable = $new_completed_deliverables[0];
                    PerundingPrestasi::where('pemantauan_id', $request->pemantauan_id)
                        ->where('perolehan_id', $request->perolehan_id)
                        ->where('id', $deliverable['id'])
                        ->update([
                            'tahun' => $deliverable['tahun'],
                            'bulan' => $deliverable['bulan'],
                            'deliverable' => $deliverable['deliverable'],
                            'emel' => $deliverable['emel'],
                            'tarikh_mula_jadual' => $deliverable['tarikhMulaJadual'],
                            'tarikh_mula_sebenar' => $deliverable['tarikhMulaSebenar'],
                            'tarikh_siap_jadual' => $deliverable['tarikhSiapJadual'],
                            'tarikh_siap_sebenar' => $deliverable['tarikhSiapSebenar'],
                            'hari_lewat_awal' => $deliverable['hariLewat'],
                            'peratus_jadual' => $deliverable['peratusJadual'],
                            'peratus_sebenar' => $deliverable['peratusSebenar'],
                            'status_pelaksanaan' => $deliverable['statusPelaksanaan'],
                            'tarikh_mesyuarat' => $deliverable['tarikhMesyuarat'],
                            'keputusan' => $deliverable['keputusan'],
                            'penilaian' => $deliverable['penilaian'],
                            'EOT' => $deliverable['EOT'],
                            'is_readonly' => 1,
                            'tarikh_lad_mula' => $deliverable['tarikhLadMula'],
                            'tarikh_lad_tamat' => $deliverable['tarikhLadTamat'],
                            'bilangan_hari_lad' => $deliverable['bilanganHariLad'],
                            'jumlah_lad_terkumpul' => $deliverable['jumlahLad'],
                            'tarikh_kemaskini' => $deliverable['tarikhKemaskini'],
                            'dikemaskini_oleh' => $request->user_id,
                            'dikemaskini_pada' => Carbon::now()->format('Y-m-d H:i:s'),
                        ]);
                }
                // $deliverable = $new_completed_deliverables[0];
                // PerundingPrestasi::where('pemantauan_id', $request->pemantauan_id)
                //     ->where('perolehan_id', $request->perolehan_id)
                //     ->where('id', $deliverable['id'])
                //     ->update([
                //         'tahun' => $deliverable['tahun'],
                //         'bulan' => $deliverable['bulan'],
                //         'deliverable' => $deliverable['deliverable'],
                //         'emel' => $deliverable['emel'],
                //         'tarikh_mula_jadual' => $deliverable['tarikhMulaJadual'],
                //         'tarikh_mula_sebenar' => $deliverable['tarikhMulaSebenar'],
                //         'tarikh_siap_jadual' => $deliverable['tarikhSiapJadual'],
                //         'tarikh_siap_sebenar' => $deliverable['tarikhSiapSebenar'],
                //         'hari_lewat_awal' => $deliverable['hariLewat'],
                //         'peratus_jadual' => $deliverable['peratusJadual'],
                //         'peratus_sebenar' => $deliverable['peratusSebenar'],
                //         'status_pelaksanaan' => $deliverable['statusPelaksanaan'],
                //         'tarikh_mesyuarat' => $deliverable['tarikhMesyuarat'],
                //         'keputusan' => $deliverable['keputusan'],
                //         'penilaian' => $deliverable['penilaian'],
                //         'EOT' => $deliverable['EOT'],
                //         'is_readonly' => 1,
                //         'tarikh_lad_mula' => $deliverable['tarikhLadMula'],
                //         'tarikh_lad_tamat' => $deliverable['tarikhLadTamat'],
                //         'bilangan_hari_lad' => $deliverable['bilanganHariLad'],
                //         'jumlah_lad_terkumpul' => $deliverable['jumlahLad'],
                //         'tarikh_kemaskini' => $deliverable['tarikhKemaskini'],
                //         'dikemaskini_oleh' => $request->user_id,
                //         'dikemaskini_pada' => Carbon::now()->format('Y-m-d H:i:s'),
                //     ]);
            }
        }

        return $result;
    }

    public function getRekordLampiran(Request $request) {
        try {

            $rekord_lampiran = PerundingPrestasiRekordLapiran::where('pemantauan_id', $request->pemantauan_id)
                            ->where('perolehan_id', $request->perolehan_id)
                            ->where('prestasi_id', $request->prestasi_id)
                            ->with('media')
                            ->get();

            return response()->json([
                'code' => '200',
                'status' => 'Success',
                'data' => $rekord_lampiran,
                
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

    public function deleteRekordLampiran(Request $request) {
        try {
        
            $rekord_lampiran = PerundingPrestasiRekordLapiran::whereId($request->id)->first();
            // $rekord_lampiran->deleteMedia('sp'); 
            deleteMedia([$request->id],'App\Models\Perunding\PerundingPrestasiRekordLapiran');
            $rekord_lampiran->delete();

            return response()->json([
                'code' => '200',
                'status' => 'Success',
                'data' => 'deleted',
                
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

    public function storeRekordLampiran(Request $request) {
        try {
            $validator = Validator::make($request->all(),[
                'perolehan_id' => ['required', 'integer'],  
                'pemantauan_id' => ['required', 'integer'],
                'type' => ['required', 'string'],
                'tarikh' => ['required', 'string'],
                'sp_file_name' => ['required', 'file'],
            ]);

            $version_no = $this->getHighestVersionNo($request->pemantauan_id, $request->perolehan_id);

            if ($version_no) {
                $latest_version = $version_no;
            } else {
                $latest_version = 1;
            }
            

            if(!$validator->fails()) {

                $prestasi_json = json_decode($request->prestasi_details, TRUE);
                
                if ($prestasi_json['version'] == '' && $prestasi_json['id'] == '') {

                    $prestasi = PerundingPrestasi::create([
                        'pemantauan_id' => $request->pemantauan_id,
                        'perolehan_id' => $request->perolehan_id,
                        'tahun' => $prestasi_json['tahun'],
                        'bulan' => $prestasi_json['bulan'],
                        'deliverable' => $prestasi_json['deliverable'],
                        'emel' => $prestasi_json['emel'],
                        'tarikh_mula_jadual' => $prestasi_json['tarikhMulaJadual'],
                        'tarikh_mula_sebenar' => $prestasi_json['tarikhMulaSebenar'],
                        'tarikh_siap_jadual' => $prestasi_json['tarikhSiapJadual'],
                        'tarikh_siap_sebenar' => $prestasi_json['tarikhSiapSebenar'],
                        'hari_lewat_awal' => $prestasi_json['hariLewat'],
                        'peratus_jadual' => $prestasi_json['peratusJadual'],
                        'peratus_sebenar' => $prestasi_json['peratusSebenar'],
                        'status_pelaksanaan' => $prestasi_json['statusPelaksanaan'],
                        'tarikh_mesyuarat' => $prestasi_json['tarikhMesyuarat'],
                        'keputusan' => $prestasi_json['keputusan'],
                        'penilaian' => $prestasi_json['penilaian'],
                        'EOT' => $prestasi_json['EOT'],
                        'tarikh_lad_mula' => $prestasi_json['tarikhLadMula'],
                        'tarikh_lad_tamat' => $prestasi_json['tarikhLadTamat'],
                        'bilangan_hari_lad' => $prestasi_json['bilanganHariLad'],
                        'jumlah_lad_terkumpul' => $prestasi_json['jumlahLad'],
                        'tarikh_kemaskini' => $prestasi_json['tarikhKemaskini'],
                        'version_no' => $latest_version,
                        'created_at' =>  Carbon::now()->format('Y-m-d H:i:s'),
                        'dibuat_pada' => Carbon::now()->format('Y-m-d H:i:s'),
                        'dibuat_oleh' => $request->user_id,
                        'dikemaskini_oleh' => $request->user_id,
                        'dikemaskini_pada' => Carbon::now()->format('Y-m-d H:i:s'),
                    ]);

                } else {
                    PerundingPrestasi::where('pemantauan_id', $request->pemantauan_id)
                        ->where('perolehan_id', $request->perolehan_id)
                        ->where('id', $prestasi_json['id'])
                        ->update([
                            'tahun' => $prestasi_json['tahun'],
                            'bulan' => $prestasi_json['bulan'],
                            'deliverable' => $prestasi_json['deliverable'],
                            'emel' => $prestasi_json['emel'],
                            'tarikh_mula_jadual' => $prestasi_json['tarikhMulaJadual'],
                            'tarikh_mula_sebenar' => $prestasi_json['tarikhMulaSebenar'],
                            'tarikh_siap_jadual' => $prestasi_json['tarikhSiapJadual'],
                            'tarikh_siap_sebenar' => $prestasi_json['tarikhSiapSebenar'],
                            'hari_lewat_awal' => $prestasi_json['hariLewat'],
                            'peratus_jadual' => $prestasi_json['peratusJadual'],
                            'peratus_sebenar' => $prestasi_json['peratusSebenar'],
                            'status_pelaksanaan' => $prestasi_json['statusPelaksanaan'],
                            'tarikh_mesyuarat' => $prestasi_json['tarikhMesyuarat'],
                            'keputusan' => $prestasi_json['keputusan'],
                            'penilaian' => $prestasi_json['penilaian'],
                            'EOT' => $prestasi_json['EOT'],
                            'tarikh_lad_mula' => $prestasi_json['tarikhLadMula'],
                            'tarikh_lad_tamat' => $prestasi_json['tarikhLadTamat'],
                            'bilangan_hari_lad' => $prestasi_json['bilanganHariLad'],
                            'jumlah_lad_terkumpul' => $prestasi_json['jumlahLad'],
                            'tarikh_kemaskini' => $prestasi_json['tarikhKemaskini'],
                            'dikemaskini_oleh' => $request->user_id,
                            'dikemaskini_pada' => Carbon::now()->format('Y-m-d H:i:s'),
                        ]);


                    $prestasi = PerundingPrestasi::where('pemantauan_id', $request->pemantauan_id)
                        ->where('perolehan_id', $request->perolehan_id)
                        ->where('id', $prestasi_json['id'])->first();
                }

                $prestastiRekordLampiran = PerundingPrestasiRekordLapiran::create([
                    'pemantauan_id' => $request->pemantauan_id,
                    'perolehan_id' => $request->perolehan_id,
                    'prestasi_id' => $prestasi->id,
                    'type' => $request->type,
                    'tarikh' => $request->tarikh,
                    'created_at' =>  Carbon::now()->format('Y-m-d H:i:s'),
                    'dibuat_pada' => Carbon::now()->format('Y-m-d H:i:s'),
                    'dibuat_oleh' => $request->user_id,
                    'dikemaskini_oleh' => $request->user_id,
                    'dikemaskini_pada' => Carbon::now()->format('Y-m-d H:i:s'),
                ]);

                
                $prestastiRekordLampiran->addMedia($request->sp_file_name)
                    ->toMediaCollection('sp', 'perunding');
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
                'data' => $prestasi,
                
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

    private function closeCompletedDeliverable($request, $latest_version) {
        $total_readonly = 0;
        $updateble_prestasi = '';
        
        if ($request->has('prestasiDetails')) {
            foreach ($request->prestasiDetails as $prestasiDetails) {
                $prestasi_json = json_decode($prestasiDetails, TRUE);
                if ($prestasi_json['is_readonly'] == 0) {
                    $total_readonly = $total_readonly + 1;
                    if ($prestasi_json['tarikhSiapSebenar'] != '' || $prestasi_json['tarikhSiapSebenar'] != null) {
                        $updateble_prestasi = $prestasi_json;
                    }
                }
            }

            if($total_readonly == 2 && $updateble_prestasi != '') {
                $updatable_deliverables = PerundingPrestasi::whereId($updateble_prestasi['id'])->first();
                $updatable_deliverables->is_readonly = 1;
                $updatable_deliverables->tarikh_siap_sebenar = $updateble_prestasi['tarikhSiapSebenar'];
                $updatable_deliverables->save();
            }
        }
    }

    private function isSameDeliverables($request)
    {
        $existing_deliverables = [];
        if ($request->has('prestasiDetails')) {
            foreach ($request->prestasiDetails as $prestasiDetails) {
                $prestasi_json = json_decode($prestasiDetails, TRUE);
                if ($prestasi_json['is_readonly'] != 1 && in_array($prestasi_json['deliverable'], $existing_deliverables, TRUE)) {
                        return TRUE;
                } else {
                    if ($prestasi_json['is_readonly'] == 0) {
                        array_push($existing_deliverables, $prestasi_json['deliverable']);
                        // $existing_deliverables = $prestasi_json['deliverable'];
                    }
                }


                if (count($existing_deliverables) > 1) {
                    return TRUE;
                }
            }
        }

        return FALSE;
    }

    private function isDeliverablesCompleted($request, $latest_version)
    {
        $isNewDeliverable = False;
        foreach ($request->prestasiDetails as $prestasiDetails) {
            $prestasi_json = json_decode($prestasiDetails, TRUE);
            if ($prestasi_json['version'] == '' && $prestasi_json['id'] == '') {
                $isNewDeliverable = True;
                break;
            }
        }

        if ($isNewDeliverable) {
            $existing_deliverables = PerundingPrestasi::where('pemantauan_id', $request->pemantauan_id)
                ->where('perolehan_id', $request->perolehan_id)
                ->where('version_no', $latest_version)
                ->whereNull('tarikh_siap_sebenar')
                // ->where('is_readonly', 0)
                ->pluck('deliverable')
                ->toArray();

            if (count($existing_deliverables) > 0) {
                return FALSE;
            }
        }
        return TRUE;
    }

    private function isDifferentDeliverables($request, $latest_version)
    {
        $new_deliverables = [];
        foreach ($request->prestasiDetails as $prestasiDetails) {
            $prestasi_json = json_decode($prestasiDetails, TRUE);
            if ($prestasi_json['is_readonly'] == 0 || $prestasi_json['id'] == '') {
                array_push($new_deliverables, $prestasi_json['deliverable']);
            }
        }

        $existing_deliverables = PerundingPrestasi::where('pemantauan_id', $request->pemantauan_id)
            ->where('perolehan_id', $request->perolehan_id)
            ->where('version_no', $latest_version)
            ->where('is_readonly', 0)
            ->pluck('deliverable')
            ->toArray();

        $deliverable_difference = array_diff($new_deliverables, $existing_deliverables);
        if (count($deliverable_difference) > 0) {
            $existing_prestasi = PerundingPrestasi::where('pemantauan_id', $request->pemantauan_id)
                ->where('perolehan_id', $request->perolehan_id)
                ->where('version_no', $latest_version)
                ->where('is_readonly', 0)
                ->get();

            foreach ($existing_prestasi as $prestasi) {
                $prestasi->is_readonly = 1;
                $prestasi->save();
            }
        }
    }
}