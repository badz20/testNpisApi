<?php

namespace App\Http\Controllers\Api\Permohonan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Permohonan\KewanganBelanjaMengurus;
use App\Models\Permohonan\KewanganBelanjaMengurusDetails;
use App\Models\Permohonan\ProjectKewanganBelanjaMengurusTuntutan;
use App\Models\Project;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;


class KewanganBelanjaMengurusController extends Controller
{
    //

    public function index(Request $request) {
        try {

            $belanjaMengurus = KewanganBelanjaMengurus::where('project_id',$request->project_id)
                                ->with(['project','belanjaDetails','belanjaTuntutan'])
                                ->get();
            return response()->json([
                'code' => '200',
                'status' => 'Success',
                'data' => $belanjaMengurus,
                
            ]);
        }
        catch (\Throwable $th) {

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
            $this->pengurusPelaksanaan($request);
            $this->pengurusDocumentasi($request);
            $this->pengurusTuntutan($request);
            
            $belanjaMengurus = KewanganBelanjaMengurus::where('project_id',$request->permohonan_projek_id)
                            ->get();
            return response()->json([
                'code' => '200',
                'status' => 'Success',
                'data' => $belanjaMengurus,
                
            ]);
        }
        catch (\Throwable $th) {

            logger()->error($th->getMessage());

            return response()->json([
                'code' => '500',
                'status' => 'Failed',
                'error' => $th,
            ]);
        }    
    }

    private function pengurusPelaksanaan($request)
    {
        $belanjaMengurus = KewanganBelanjaMengurus::where('project_id',$request->permohonan_projek_id)
                            ->where('type','pelaksanaan')
                            ->first();
        $pelaksanaan_json = json_decode($request->pelaksanaanTableData, TRUE);

        if($belanjaMengurus) {
            $data = [
                'peratus' => $pelaksanaan_json['pertaus1'],
                'peratus_RMK' => $pelaksanaan_json['pertaus2'],
                'jumlah' => $pelaksanaan_json['Jumlah']['jumlah1'],
                'jumlah_RMK' => $pelaksanaan_json['Jumlah']['jumlahRMK'],
                'dikemaskini_oleh' => $request->user_id,
                'dikemaskini_pada' => Carbon::now()->format('Y-m-d H:i:s'),
            ];

            $silingData = $pelaksanaan_json['Jumlah']['silling']; 

            // Loop through the "siling" array and add keys dynamically
            for ($i = 0; $i < count($silingData); $i++) {
                $data["yr_" . ($i + 1)] = $silingData[$i];
            }
            
            KewanganBelanjaMengurus::where('project_id',$request->permohonan_projek_id)
                            ->where('type','pelaksanaan')
                            ->update($data);
            $belanjaMengurus = KewanganBelanjaMengurus::where('project_id',$request->permohonan_projek_id)
                            ->where('type','pelaksanaan')->first();
        }else {

            $data = [
                'project_id' =>$request->permohonan_projek_id,
                'type' =>  'pelaksanaan',
                'peratus' => $pelaksanaan_json['pertaus1'],
                'peratus_RMK' => $pelaksanaan_json['pertaus2'],
                'jumlah' => $pelaksanaan_json['Jumlah']['jumlah1'],
                'jumlah_RMK' => $pelaksanaan_json['Jumlah']['jumlahRMK'],
                'dibuat_pada' => Carbon::now()->format('Y-m-d H:i:s'),
                'dibuat_oleh' => $request->user_id,
                'dikemaskini_oleh' => $request->user_id,
                'dikemaskini_pada' => Carbon::now()->format('Y-m-d H:i:s'),
            ];

            $silingData = $pelaksanaan_json['Jumlah']['silling']; 

            // Loop through the "siling" array and add keys dynamically
            for ($i = 0; $i < count($silingData); $i++) {
                $data["yr_" . ($i + 1)] = $silingData[$i];
            }

            
            $belanjaMengurus = KewanganBelanjaMengurus::create($data);
        }

        $this->pengurusDetailsData($request,$belanjaMengurus,$pelaksanaan_json['VA'],'VA');
        $this->pengurusDetailsData($request,$belanjaMengurus,$pelaksanaan_json['VE'],'VE');
        $this->pengurusDetailsData($request,$belanjaMengurus,$pelaksanaan_json['VR'],'VR');
        $this->pengurusDetailsData($request,$belanjaMengurus,$pelaksanaan_json['PD'],'PD');
        $this->pengurusDetailsData($request,$belanjaMengurus,$pelaksanaan_json['PE'],'PE');
        $this->pengurusDetailsData($request,$belanjaMengurus,$pelaksanaan_json['PF'],'PF');
        $this->pengurusDetailsData($request,$belanjaMengurus,$pelaksanaan_json['PG'],'PG');
        $this->pengurusDetailsData($request,$belanjaMengurus,$pelaksanaan_json['PH'],'PH');
    }


    private function pengurusDocumentasi($request)
    {
        $belanjaMengurusDocumentasi = KewanganBelanjaMengurus::where('project_id',$request->permohonan_projek_id)
                            ->where('type','documentasi')
                            ->first();
        $documentasi_json = json_decode($request->documentasiTableData, TRUE);

        if($belanjaMengurusDocumentasi) {
            $data = [
                'peratus' => $documentasi_json['pertaus1'],
                'peratus_RMK' => $documentasi_json['pertaus2'],
                'jumlah' => $documentasi_json['Jumlah']['jumlah1'],
                'jumlah_RMK' => $documentasi_json['Jumlah']['jumlahRMK'],
                'dikemaskini_oleh' => $request->user_id,
                'dikemaskini_pada' => Carbon::now()->format('Y-m-d H:i:s'),
            ];

            $silingData = $documentasi_json['Jumlah']['silling']; 

            // Loop through the "siling" array and add keys dynamically
            for ($i = 0; $i < count($silingData); $i++) {
                $data["yr_" . ($i + 1)] = $silingData[$i];
            }
            
            KewanganBelanjaMengurus::where('project_id',$request->permohonan_projek_id)
                            ->where('type','documentasi')
                            ->update($data);

            $belanjaMengurusDocumentasi = KewanganBelanjaMengurus::where('project_id',$request->permohonan_projek_id)
                            ->where('type','documentasi')->first();
        }else {

            $data = [
                'project_id' =>$request->permohonan_projek_id,
                'type' =>  'documentasi',
                'peratus' => $documentasi_json['pertaus1'],
                'peratus_RMK' => $documentasi_json['pertaus2'],
                'jumlah' => $documentasi_json['Jumlah']['jumlah1'],
                'jumlah_RMK' => $documentasi_json['Jumlah']['jumlahRMK'],
                'dibuat_pada' => Carbon::now()->format('Y-m-d H:i:s'),
                'dibuat_oleh' => $request->user_id,
                'dikemaskini_oleh' => $request->user_id,
                'dikemaskini_pada' => Carbon::now()->format('Y-m-d H:i:s'),
            ];

            $silingData = $documentasi_json['Jumlah']['silling']; 

            // Loop through the "siling" array and add keys dynamically
            for ($i = 0; $i < count($silingData); $i++) {
                $data["yr_" . ($i + 1)] = $silingData[$i];
            }

            
            $belanjaMengurusDocumentasi = KewanganBelanjaMengurus::create($data);
        }

        $this->pengurusDetailsData($request,$belanjaMengurusDocumentasi,$documentasi_json['DA'],'DA');
        $this->pengurusDetailsData($request,$belanjaMengurusDocumentasi,$documentasi_json['DB'],'DB');
        $this->pengurusDetailsData($request,$belanjaMengurusDocumentasi,$documentasi_json['DC'],'DC');
        $this->pengurusDetailsData($request,$belanjaMengurusDocumentasi,$documentasi_json['DE'],'DE');
        $this->pengurusDetailsData($request,$belanjaMengurusDocumentasi,$documentasi_json['DF'],'DF');
        $this->pengurusDetailsData($request,$belanjaMengurusDocumentasi,$documentasi_json['DG'],'DG');
        $this->pengurusDetailsData($request,$belanjaMengurusDocumentasi,$documentasi_json['DH'],'DH');
        $this->pengurusDetailsData($request,$belanjaMengurusDocumentasi,$documentasi_json['DI'],'DI');
        $this->pengurusDetailsData($request,$belanjaMengurusDocumentasi,$documentasi_json['DJ'],'DJ');
        $this->pengurusDetailsData($request,$belanjaMengurusDocumentasi,$documentasi_json['DK'],'DK');
    }

    private function pengurusTuntutan($request)
    {
        $belanjaMengurusTuntutan = KewanganBelanjaMengurus::where('project_id',$request->permohonan_projek_id)
                            ->where('type','tuntutan')
                            ->first();
        $tuntutan_json = json_decode($request->tuntutanTableData, TRUE);

        if($belanjaMengurusTuntutan) {
            $data = [
                'peratus' => $tuntutan_json['pertaus1'],
                'peratus_RMK' => $tuntutan_json['pertaus2'],
                'jumlah' => $tuntutan_json['Jumlah']['jumlah1'],
                'jumlah_RMK' => $tuntutan_json['Jumlah']['jumlahRMK'],
                'dikemaskini_oleh' => $request->user_id,
                'dikemaskini_pada' => Carbon::now()->format('Y-m-d H:i:s'),
            ];

            $silingData = $tuntutan_json['Jumlah']['silling']; 

            // Loop through the "siling" array and add keys dynamically
            for ($i = 0; $i < count($silingData); $i++) {
                $data["yr_" . ($i + 1)] = $silingData[$i];
            }
            
            KewanganBelanjaMengurus::where('project_id',$request->permohonan_projek_id)
                            ->where('type','tuntutan')
                            ->update($data);

            $belanjaMengurusTuntutan = KewanganBelanjaMengurus::where('project_id',$request->permohonan_projek_id)
                            ->where('type','tuntutan')->first();

        }else {

            $data = [
                'project_id' =>$request->permohonan_projek_id,
                'type' =>  'tuntutan',
                'peratus' => $tuntutan_json['pertaus1'],
                'peratus_RMK' => $tuntutan_json['pertaus2'],
                'jumlah' => $tuntutan_json['Jumlah']['jumlah1'],
                'jumlah_RMK' => $tuntutan_json['Jumlah']['jumlahRMK'],
                'dibuat_pada' => Carbon::now()->format('Y-m-d H:i:s'),
                'dibuat_oleh' => $request->user_id,
                'dikemaskini_oleh' => $request->user_id,
                'dikemaskini_pada' => Carbon::now()->format('Y-m-d H:i:s'),
            ];

            $silingData = $tuntutan_json['Jumlah']['silling']; 

            // Loop through the "siling" array and add keys dynamically
            for ($i = 0; $i < count($silingData); $i++) {
                $data["yr_" . ($i + 1)] = $silingData[$i];
            }

            $belanjaMengurusTuntutan = KewanganBelanjaMengurus::create($data);

            
        }

        
        $belanjan_tuntutan = ProjectKewanganBelanjaMengurusTuntutan::where('project_id',$request->permohonan_projek_id)
                                    ->where('belanja_mengurus_id',$belanjaMengurusTuntutan->id)->first();

        if($belanjan_tuntutan) {
            ProjectKewanganBelanjaMengurusTuntutan::where('project_id',$request->permohonan_projek_id)
            ->where('belanja_mengurus_id',$belanjaMengurusTuntutan->id)
            ->update([
                    'project_id' =>$request->permohonan_projek_id,
                    'belanja_mengurus_id' =>$belanjaMengurusTuntutan->id,
                    'anggaran_perjalanan' => $tuntutan_json['tuntutan']['text1'],
                    'mesyuarat_tapak' => $tuntutan_json['tuntutan']['text2'],
                    'mesyuarat_teknikal' => $tuntutan_json['tuntutan']['text3'],
                    'mesyuarat_pemantauan' => $tuntutan_json['tuntutan']['text4'],
                    'mesyuarat_kemajuan_perunding' => $tuntutan_json['tuntutan']['text5'],
                    'dikemaskini_oleh' => $request->user_id,
                    'dikemaskini_pada' => Carbon::now()->format('Y-m-d H:i:s'),
                ]);
        }else {
            ProjectKewanganBelanjaMengurusTuntutan::create([
                'project_id' =>$request->permohonan_projek_id,
                'belanja_mengurus_id' =>$belanjaMengurusTuntutan->id,
                'anggaran_perjalanan' => $tuntutan_json['tuntutan']['text1'],
                'mesyuarat_tapak' => $tuntutan_json['tuntutan']['text2'],
                'mesyuarat_teknikal' => $tuntutan_json['tuntutan']['text3'],
                'mesyuarat_pemantauan' => $tuntutan_json['tuntutan']['text4'],
                'mesyuarat_kemajuan_perunding' => $tuntutan_json['tuntutan']['text5'],
                'dibuat_pada' => Carbon::now()->format('Y-m-d H:i:s'),
                'dibuat_oleh' => $request->user_id,
                'dikemaskini_oleh' => $request->user_id,
                'dikemaskini_pada' => Carbon::now()->format('Y-m-d H:i:s'),
            ]);
        }
        foreach ($tuntutan_json as $key => $value) {
            if (is_array($value) && $key != 'Jumlah' && $key != 'tuntutan') {
                $this->pengurusDetailsData($request,$belanjaMengurusTuntutan,$tuntutan_json[$key],$key,TRUE);
            } 
        }
    }

    private function pengurusDetailsData($request,$belanjaMengurus,$data,$key,$is_text=FALSE)
    {

        $belanjaMengurusDetails = KewanganBelanjaMengurusDetails::where('project_id',$request->permohonan_projek_id)
                                                        ->where('belanja_mengurus_id',$belanjaMengurus->id)
                                                        ->where('type',$key)
                                                        ->first();

        if($belanjaMengurusDetails) {
            $dbdata = [
                'type' =>  $key,
                'nilai1' => $data['nilai1'],
                'nilai2' => $data['nilai2'],
                'unit' => $data['unit'],
                'rm' => $data['rm'],
                'kadar_unit' => $data['kadarunit'],
                'kali' => $data['kali'],
                'jumlah' => $data['jumlah1'],
                'jumlahRMK' => $data['jumlah2'],
                'dikemaskini_oleh' => $request->user_id,
                'dikemaskini_pada' => Carbon::now()->format('Y-m-d H:i:s'),
            ];

            $silingData = $data['siling']; 

            // Loop through the "siling" array and add keys dynamically
            for ($i = 0; $i < count($silingData); $i++) {
                $dbdata["yr_" . ($i + 1)] = $silingData[$i];
            }

            if($is_text) {
                $dbdata['text'] =  $data['text'];
            }

            KewanganBelanjaMengurusDetails::where('project_id',$request->permohonan_projek_id)
                                ->where('belanja_mengurus_id',$belanjaMengurus->id)
                                ->where('type',$key)
                                ->update($dbdata);

            $belanjaDetails = KewanganBelanjaMengurusDetails::where('project_id',$request->permohonan_projek_id)
                                        ->where('belanja_mengurus_id',$belanjaMengurus->id)
                                        ->where('type',$key)
                                        ->first();
        }else {

            $dbdata = [
                'project_id' =>$request->permohonan_projek_id,
                'belanja_mengurus_id' => $belanjaMengurus->id,
                'type' =>  $key,
                'nilai1' => $data['nilai1'],
                'nilai2' => $data['nilai2'],
                'unit' => $data['unit'],
                'rm' => $data['rm'],
                'kadar_unit' => $data['kadarunit'],
                'kali' => $data['kali'],
                'jumlah' => $data['jumlah1'],
                'jumlahRMK' => $data['jumlah2'],
                'dibuat_pada' => Carbon::now()->format('Y-m-d H:i:s'),
                'dibuat_oleh' => $request->user_id,
                'dikemaskini_oleh' => $request->user_id,
                'dikemaskini_pada' => Carbon::now()->format('Y-m-d H:i:s'),
            ];

            $silingData = $data['siling']; 

            // Loop through the "siling" array and add keys dynamically
            for ($i = 0; $i < count($silingData); $i++) {
                $dbdata["yr_" . ($i + 1)] = $silingData[$i];
            }

            if($is_text) {
                $dbdata['text'] =  $data['text'];
            }
            
            $belanjaDetails = KewanganBelanjaMengurusDetails::create($dbdata);
        }
    }
}