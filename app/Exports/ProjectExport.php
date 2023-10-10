<?php

namespace App\Exports;

use App\Models\Project;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;
use \DOMDocument;

class ProjectExport implements FromArray , WithStrictNullComparison
{

    protected $request;

    public function __construct($request)
    {
        $this->request = $request;
    }

    

    private function headings():array{
        return[
            'BIL',                  //1 
            'NO_RUJUKAN',           //2
            'Bahagian',             //3
            'RMK',                  //4
            'RP',                   //5
            'TAHUN_MOHON',          //6
            'STATUS_PERMOHONAN',    //7
            'KEMENETERIAN',         //8

            'NAMA_AGENSI_PEMILIK',  //9
            'KEUTAMAAN',            //10
            'KOMPONEN_DE',          //11
            'LABEL_PROJEK',         //12
            'WISHLIST_NEGERI',      //13
            'RUJ_WISHLIST_NEGERI',  //14


            'JENIS_PERMOHONAN',     //15

            'PROJEK_BAHARU_2022',    //16

            'KOD_PROJEK',           //17
            'NAMA_PROJEK',          //18
            'SKOP_PROJEK',          //19
            'NEGERI',               //20
            'KUMPULAN_SASARAN',     //21
            'OUTPUT_PROJEK',        //22
            'OUTCOME_PROJEK',       //23

            'KOS_DE_ASAL',   //25
            'KOS_DE_DIPINDA',   //26
            'KOS_DE_MOHON',   //27
            'KOS_DE_SEMAKAN',   //28
            'KOS_DE_PERAKU1',   //29
            'KOS_DE_PERAKU2',   //30
            'SILING_2023(MOHON)',   //31
            'SILING_2023(SEMAK)',   //32
            'SILING_2023(PERAKU1)',   //33
            'SILING_2023(PERAKU2)',   //34
            'BAHAGIAN',   //35
            'STATUS_PELAKSANAAN_PROJEK',   //36
            'AKTIVITI_PROJEK',   //37
            'KEMAJUAN_PROJEK',   //38
            '% JADUAL',   //39
            '% SEBENAR',   //40
            'BELANJA_RMK_LEPAS',   //41
            'BELANJA_RMK12_2021',   //42
            'PERUNTUKAN_MOF_2022',   //43
            'BAKI_KOS',   //44
            'KOD_MP',   //46
            'MP',   //47
            'KOD_BUTIRAN',   //48
            'BUTIRAN',   //49
            'RMK12:KOD_TEMA/PEMANGKIN',   //50
            'RMK12:TEMA/PEMANGKIN',   //51
            'RMK12:KOD_BAB',   //52
            'RMK12:BAB',   //53
            'RMK12:KOD_BK',   //54
            'RMK12:BIDANG_KEUTAMAAN',   //55
            'RMK12:KOD_OUTCOME',   //56
            'RMK12:OUTCOME',   //57
            'RMK12:KOD_STRATEGI',   //58
            'RMK12:STRATEGI',   //59
            'RMK12:KPI',   //60
            'KOD_OBB_PROGRAM',   //61
            'OBB_PROGRAM',   //62
            'KOD_OBB_AKTIVITI',   //63
            'OBB_AKTIVITI',   //64
            'KOD_OBB_OUTPUT_AKTIVITI',   //65
            'OBB_OUTPUT_AKTIVITI',   //66
            'OBJEKTIF',   //67
            'KETERANGAN_PROJEK',   //68
            'KOMPONEN',   //69
            'PERBINCANGAN_KERAJAAN_NEGERI',   //70
            'INDIKATOR',   //71
            'KORIDOR',   //72
            'KAWASAN',   //73
            'KOD_SEKTOR_UTAMA',   //74
            'SEKTOR_UTAMA',   //75
            'KOD_SEKTOR',   //76
            'SEKTOR',   //77
            'KOD_SUBSEKTOR',   //78
            'SUBSEKTOR',   //79
            'JENIS_KATEGORI',   //80
            'JENIS_SUBKATEGORI',   //81
            'PERUBAHAN_STRUKTUR_ASAL',   //82
            'PSC',   //83
            'PENGECUALIAN_VAE',   //84
            'KATEGORI_ACAT',   //85
            'GN0',   //86
            'CARA_PEMBIAYAAN',   //87
            'CARA_PENYALURAN',   //88
            'NAMA_AGENSI_PEMOHON',   //89
            'AGENSI_PELAKSANA_JKR',   //90
            'AGENSI_PELAKSANA_JPS',   //91
            'AGENSI_PELAKSANA_LAIN',   //92
            'DAERAH',   //93
            'PARLIMEN',   //94
            'DUN',   //95
            'SPESIFIKASI_TEKNIKAL',   //96
            'PEMILIKAN_TAPAK',   //97
            'RUJUKAN_MYeTAPP/JKPTG',   //98
            'KELUASAN_TAPAK',   //99
            'NO_LOT',   //100
            'REKABENTUK',   //101
            'SELARAS PBT',   //102
            'M/B_PBT',   //103
            'PERBINCANGAN KPTG',   //104
            'M/B_KPTG',   //105
            'CADANGAN_UTILITI',   //106
            'M/B_PENYEDIA_UTILITI',   //107
            'TEMPOH_PELAKSANAAN_(BULAN)',   //108
            'TAHUN_JANGKA_MULA',   //109
            'TAHUN_JANGKA_SIAP',   //110
            'BELANJA_RMK9',   //111
            'BELANJA_RMK10',   //112
            'BELANJA_RMK11',   //113
            'BELANJA_2021',   //114
            'PERUNTUKAN_DISEMAK_IGFMAS_2022',   //115
            'SILING_2022(PEMANTAUAN)',   //116
            'SILING_2024(MOHON)',   //117
            'SILING_2025(MOHON)',   //118
            'SILING_2024(SEMAK)',   //119
            'SILING_2025(SEMAK)',   //120
            'SILING_2024(PERAKU1)',   //121
            'SILING_2025(PERAKU1)',   //122
            'SILING_2024(PERAKU2)',   //123
            'SILING_2025(PERAKU2)',   //124
            'KOS_NEGERI',   //125
            'SILING_NEGERI_2022',   //126
            'KOS_OE',   //127
            'KOS_BUKAN_DE',   //128
            'CREATIVITY_INDEX',   //129
            'JUMLAH_SDG',   //130
            'SDG',   //131
            'MAKLUMAT_TAMBAHAN',   //132
            'KOS_PROJECK', //133
            

        ];
    } 

    private function get_projects()
    {
        if($this->request->usertype==1)
            {
                $result = \App\Models\Project::with(['skopProjects.skopOptions',
                        'bahagianPemilik',
                        'bahagianPemilik.kementerian',
                        'bahagianTerlibat.bahagian',
                        'jenisKategori',
                        'lokasi.negeri',
                        'rollingPlan',
                        'outcomeProjects',
                        'outputProjects',
                        'RmkObbSdg.strategi',
                        'RmkObbSdg.obbOutputAktiviti',
                        'sektor',
                        'sektorUtama',
                        'subSektor',
                        'kementerian'])
                                  ->where('daerah_id',$this->request->daerah)
                                  ->get();
            }
            else if($this->request->usertype==2)
            { 
                $result = \App\Models\Project::with(['skopProjects.skopOptions',
                            'bahagianPemilik',
                            'bahagianPemilik.kementerian',
                            'bahagianTerlibat.bahagian',
                            'jenisKategori',
                            'lokasi.negeri',
                            'rollingPlan',
                            'outcomeProjects',
                            'outputProjects',
                            'RmkObbSdg.strategi',
                            'RmkObbSdg.obbOutputAktiviti',
                            'sektor',
                            'sektorUtama',
                            'subSektor',
                            'kementerian'])
                            ->select('projects.*')
                            ->where('projects.negeri_id',$this->request->negeri)
                            ->where('projects.daerah_id','=',NULL)
                            ->get();
                $start=2; $end=4;
                $result_daerah = \App\Models\Project::with([  'skopProjects.skopOptions',
                'bahagianPemilik',
                'bahagianPemilik.kementerian',
                'bahagianTerlibat.bahagian',
                'jenisKategori',
                'lokasi.negeri',
                'rollingPlan',
                'outcomeProjects',
                'outputProjects',
                'RmkObbSdg.strategi',
                'RmkObbSdg.obbOutputAktiviti',
                'sektor',
                'sektorUtama',
                'subSektor',
                'kementerian'])
                                             ->select('projects.*')
                                             ->where('projects.negeri_id',$this->request->negeri)
                                             ->where('projects.daerah_id','!=',NULL)
                                             ->where(function($query) use ($start,$end){
                                                $query->where('projects.workflow_status',$start)
                                                ->orwhere('projects.workflow_status',$end);
                                             })
                                             ->get();
                
                $result = $result->concat($result_daerah);


            }
            else if($this->request->usertype==3)
            {
                
                        $result = \App\Models\Project::with([  'skopProjects.skopOptions',
                        'bahagianPemilik',
                        'bahagianPemilik.kementerian',
                        'bahagianTerlibat.bahagian',
                        'jenisKategori',
                        'lokasi.negeri',
                        'rollingPlan',
                        'outcomeProjects',
                        'outputProjects',
                        'RmkObbSdg.strategi',
                        'RmkObbSdg.obbOutputAktiviti',
                        'sektor',
                        'sektorUtama',
                        'subSektor',
                        'kementerian'])
                            ->select('projects.*')
                            ->where('projects.bahagian_pemilik', $this->request->bahagian)
                            ->where('projects.negeri_id','=',NULL)
                            ->get();
                            
                        if($this->request->pengesah==1)
                        {
                            $result_pengesah = \App\Models\Project::with([  'skopProjects.skopOptions',
                            'bahagianPemilik',
                            'bahagianPemilik.kementerian',
                            'bahagianTerlibat.bahagian',
                            'jenisKategori',
                            'lokasi.negeri',
                            'rollingPlan',
                            'outcomeProjects',
                            'outputProjects',
                            'RmkObbSdg.strategi',
                            'RmkObbSdg.obbOutputAktiviti',
                            'sektor',
                            'sektorUtama',
                            'subSektor',
                            'kementerian'])
                                            ->select('projects.*')
                                            ->where('projects.bahagian_pemilik', $this->request->bahagian)
                                            ->where('projects.negeri_id','!=',NULL)
                                            ->Where('projects.workflow_status',11)
                                            ->get();

                             $result = $result->concat($result_pengesah);
                        }

                        $result_negeri = \App\Models\Project::with([  'skopProjects.skopOptions',
                        'bahagianPemilik',
                        'bahagianPemilik.kementerian',
                        'bahagianTerlibat.bahagian',
                        'jenisKategori',
                        'lokasi.negeri',
                        'rollingPlan',
                        'outcomeProjects',
                        'outputProjects',
                        'RmkObbSdg.strategi',
                        'RmkObbSdg.obbOutputAktiviti',
                        'sektor',
                        'sektorUtama',
                        'subSektor',
                        'kementerian'])
                                            ->select('projects.*')
                                            ->where('projects.bahagian_pemilik', $this->request->bahagian)
                                            ->where('projects.negeri_id','!=',NULL)
                                            ->Where('projects.workflow_status',7)
                                            ->get();
                   

                    $result = $result->concat($result_negeri);

            }
            else if($this->request->usertype==4 && $this->request->userRole==4)
            {
                $result = \App\Models\Project::with([  'skopProjects.skopOptions',
                'bahagianPemilik',
                'bahagianPemilik.kementerian',
                'bahagianTerlibat.bahagian',
                'jenisKategori',
                'lokasi.negeri',
                'rollingPlan',
                'outcomeProjects',
                'outputProjects',
                'RmkObbSdg.strategi',
                'RmkObbSdg.obbOutputAktiviti',
                'sektor',
                'sektorUtama',
                'subSektor',
                'kementerian'])
                                             ->where('workflow_status','>=',14)->get();

                $result_bkor = \App\Models\Project::with([  'skopProjects.skopOptions',
                'bahagianPemilik',
                'bahagianPemilik.kementerian',
                'bahagianTerlibat.bahagian',
                'jenisKategori',
                'lokasi.negeri',
                'rollingPlan',
                'outcomeProjects',
                'outputProjects',
                'RmkObbSdg.strategi',
                'RmkObbSdg.obbOutputAktiviti',
                'sektor',
                'sektorUtama',
                'subSektor',
                'kementerian'])
                                             ->where('projects.bahagian_pemilik', $this->request->bahagian)
                                             ->where('projects.negeri_id','=',NULL)
                                             ->where('workflow_status','<',14)
                                             ->orwhere('dibuat_oleh',$this->request->id)->get();
                
                $result = $result->concat($result_bkor);

            }
            else
            {
                $result = \App\Models\Project::with([  'skopProjects.skopOptions',
                'bahagianPemilik',
                'bahagianPemilik.kementerian',
                'bahagianTerlibat.bahagian',
                'jenisKategori',
                'lokasi.negeri',
                'rollingPlan',
                'outcomeProjects',
                'outputProjects',
                'RmkObbSdg.strategi',
                'RmkObbSdg.obbOutputAktiviti',
                'sektor',
                'sektorUtama',
                'subSektor',
                'kementerian'])
                                             ->where('dibuat_oleh',$this->request->id)->get();
            }

            return $result;
    }

    public function array(): array
    {
        $projects = Project::
        with([  'skopProjects.skopOptions',
        'bahagianPemilik',
        'bahagianPemilik.kementerian',
        'bahagianTerlibat.bahagian',
        'jenisKategori',
        'lokasi.negeri',
        'rollingPlan',
        'outcomeProjects',
        'outputProjects',
        'kementerian'])
                ->get();

                
        $array_data = $this->format_data($this->get_projects());

        return $array_data;
    }

    private function format_data($projects)
    {
        $final_data = [];

        array_push($final_data,$this->headings());
        $counter = 1;
        foreach($projects as $project) {
            $data = [];
            
            $applicaton_status=$this->getStatus($project->workflow_status);

            array_push($data,$counter); //1
            array_push($data,$project->no_rujukan); //2
            array_push($data,$project->bahagianPemilik ? $project->bahagianPemilik->nama_bahagian: ''); //3
            array_push($data,'RMKe-' .$project->rmk); //4
            array_push($data,$project->rollingPlan->name); //5

            array_push($data,$project->tahun_jangka_mula); //6


            array_push($data,$applicaton_status); //7
            array_push($data,$project->bahagianPemilik->kementerian->nama_kementerian); //8

            array_push($data,$this->get_project_bhagian_terlibat($project)); //9
            array_push($data,$project->RmkObbSdg ? $project->RmkObbSdg->Bidang_Keutamaan: ''); //10
            array_push($data,$project->kewangan ? $project->kewangan->komponen ? $project->kewangan->komponen->nama_komponen:'' : ''); //11
            array_push($data,$project->jenisKategori->name); //12
            array_push($data,''); //13
            array_push($data,''); //14

            array_push($data,$project->jenisKategori->name); //15

            array_push($data,''); //16

            array_push($data,$project->kod_projeck); //17
            array_push($data,$project->nama_projek); //18
            array_push($data,$this->get_skop_project($project)); //19
            array_push($data,$project->lokasi->negeri->nama_negeri); //20
            array_push($data,$this->get_kumpulan_sasaran($project)); //21
            array_push($data,$this->get_project_output($project)); //22
            array_push($data,$this->get_project_outcome($project)); //23

            array_push($data,''); //25
            array_push($data,''); //26
            array_push($data,''); //27
            array_push($data,''); //28
            array_push($data,''); //29
            array_push($data,''); //30
            array_push($data,$project->kewangan ? $project->kewangan->Siling_Dimohon: ''); //31
            array_push($data,$project->kewangan ? $project->kewangan->Siling_Dimohon: ''); //32
            array_push($data,$project->kewangan ? $project->kewangan->Siling_Dimohon: ''); //33
            array_push($data,$project->kewangan ? $project->kewangan->Siling_Dimohon: ''); //34
            array_push($data,$this->get_project_bhagian_terlibat($project)); //35
            array_push($data,''); //36
            array_push($data,''); //37
            array_push($data,''); //38
            array_push($data,''); //39
            array_push($data,''); //40
            array_push($data,''); //41
            array_push($data,''); //42
            array_push($data,''); //43
            array_push($data,''); //44
            array_push($data,''); //46
            array_push($data,''); //47
            array_push($data,$project->butiran_code); //48
            array_push($data,$project->butiran->value); //49

            $rmkDetails = $this->get_project_rmk_details($project);

            array_push($data,$rmkDetails['pemangkin'][0]); //50
            array_push($data,$rmkDetails['pemangkin'][1]); //51
            array_push($data,$rmkDetails['bab'][0]); //52
            array_push($data,$rmkDetails['bab'][1]); //53
            array_push($data,$rmkDetails['bk'][0]); //54
            array_push($data,$rmkDetails['bk'][1]); //55
            array_push($data,$rmkDetails['outcomeNational'][0]); //56
            array_push($data,$rmkDetails['outcomeNational'][1]); //57
            array_push($data,$rmkDetails['bk'][0]); //58
            array_push($data,$rmkDetails['bk'][1]); //59
            array_push($data,''); //60
            array_push($data,$rmkDetails['obbProgram'][0]); //61
            array_push($data,$rmkDetails['obbProgram'][1]); //62
            array_push($data,$rmkDetails['obbAktivti'][0]); //63
            array_push($data,$rmkDetails['obbAktivti'][1]); //64
            array_push($data,$rmkDetails['obbOutputAktivti'][0]); //65
            array_push($data,$rmkDetails['obbOutputAktivti'][1]); //66
            array_push($data,$this->getListCell($project->objektif)); //67
            array_push($data,$this->getListCell($project->ringkasan_projek)); //68
            array_push($data,$this->get_skop_project($project)); //69
            array_push($data,$project->nota_tambahan); //70
            array_push($data,''); //71

            $koridor = lookupOptionSingle('koridor_pembangunan',$project->koridor_pembangunan);

            array_push($data,$koridor ? $koridor->value:''); //72
            array_push($data,$koridor ? $koridor->value:''); //73
            array_push($data,$project->sektorUtama->kod_sektor_utama); //74
            array_push($data,$project->sektorUtama->name); //75
            array_push($data,$project->sektor->kod_sektor); //76
            array_push($data,$project->sektor->name); //77
            array_push($data,$project->subSektor->kod_sub_sektor); //78
            array_push($data,$project->subSektor->name); //79
            array_push($data,$project->jenisKategori->name); //80
            array_push($data,$project->jenisSubKategori->name); //81
            array_push($data,''); //82
            array_push($data,''); //83
            array_push($data,''); //84
            array_push($data,$project->vae ? $project->vae->ACAT:''); //85
            array_push($data,$project->vae ? $project->vae->GNO_status:''); //86
            array_push($data,''); //87
            array_push($data,''); //88
            array_push($data,''); //89
            array_push($data,''); //90
            array_push($data,''); //91
            array_push($data,''); //92
            array_push($data,$project->ProjectNegeriLokas ? $project->ProjectNegeriLokas->negeri->nama_negeri:''); //93
            array_push($data,$project->ProjectNegeriLokas ? $project->ProjectNegeriLokas->daerah->nama_daerah:''); //94
            array_push($data,$project->ProjectNegeriLokas ? $project->ProjectNegeriLokas->dun->nama_dun:''); //95

            switch ($project->status_reka_bantuk) {
                case 0:
                    array_push($data,'Dalam Penyediaan Reka Bentuk'); //96
                    break;
                case 1:
                    array_push($data,'Reka Bentuk Siap'); //96
                    break;
                case 2:
                    array_push($data,'Tiada Reka Bentuk'); //96
                    break;
                default:
                    array_push($data,'Tiada Reka Bentuk'); //96
                    break;
            }
            
            array_push($data,''); //97
            array_push($data,''); //98
            array_push($data,''); //99
            array_push($data,''); //100
            array_push($data,''); //101
            array_push($data,''); //102
            array_push($data,''); //103
            array_push($data,''); //104
            array_push($data,''); //105
            array_push($data,''); //106
            array_push($data,''); //107
            array_push($data,$project->tempoh_pelaksanaan); //108
            array_push($data,$project->tahun_jangka_mula); //109
            array_push($data,$project->tahun_jangka_siap); //110
            array_push($data,''); //111
            array_push($data,''); //112
            array_push($data,''); //113
            array_push($data,''); //114
            array_push($data,''); //115
            array_push($data,''); //116
            array_push($data,''); //117
            array_push($data,''); //118
            array_push($data,''); //119
            array_push($data,''); //120
            array_push($data,''); //121
            array_push($data,''); //122
            array_push($data,''); //123
            array_push($data,''); //124
            array_push($data,''); //125
            array_push($data,''); //126
            array_push($data,$project->KewanganProjekDetails ? $project->KewanganProjekDetails->kos_keseluruhan_oe:''); //127
            array_push($data,''); //128
            array_push($data,$project->KewanganProjekDetails ? $project->KewanganProjekDetails->ci:''); //129
            array_push($data,''); //130
            array_push($data,''); //131
            array_push($data,''); //132
            array_push($data,$project->kos_projeck); //133


            array_push($final_data,$data);
            $counter = $counter + 1;
        }

        return $final_data;
    }

    private function get_skop_project($project)
    {
        $skops = [];
        foreach($project->skopProjects as $skop)
        {
            array_push($skops,$skop->skopOptions->skop_name);
        }
        return implode(",",$skops);
    }

    private function get_kumpulan_sasaran($project)
    {

        return '';
    }

    private function getStatus($status)
    {
        if($status=="1"){
            $data = 'Dalam Penyediaan';
          }  
          else if($status=="2")
          {
            $data = 'Diserahkan oleh Penyemak';
          }
          else if($status=="3")
          {
            $data = 'Sedang Disemak oleh Penyemak';
          }  
          else if($status=="4")
          {
            $data = 'Telah Disemak oleh Penyemak';          
          }
          else if($status=="5")
          {
            $data = 'Permintaan untuk Dikemaskini oleh Penyemak';
          }
          else if($status=="6")
          {
            $data = 'Sedang Disemak oleh Penyemak 1';
          }
          else if($status=="7")
          {
            $data = 'Telah Disemak oleh Penyemak 1';
          }
          else if($status=="8")
          {
            $data = 'Permintaan untuk Dikemaskini oleh Penyemak 1';
          } 
          else if($status=="9")
          {
            $data = 'Ditolak oleh Penyemak 1';
          } 
          else if($status=="10")
          {
            $data = 'Sedang Disemak oleh Penyemak 2';
          }
          else if($status=="11")
          {
            $data = 'Telah Disemak oleh Penyemak 2';
          }
          else if($status=="12")
          {
            $data = 'Permintaan untuk Dikemaskini oleh Penyemak 2';
          } 
          else if($status=="13")
          {
            $data = 'Untuk Pengesahan Pengarah Bahagian';
          } 
          else if($status=="14")
          {
            $data = 'Disahkan oleh Pengesah';
          }
          else if($status=="15")
          {
            $data = 'Permintaan untuk dikemaskini oleh Pengesah';
          }
          else if($status=="16")
          {
            $data = 'Ditolak oleh Pengesah';
          }
          else if($status=="17")
          {
            $data = 'Diluluskan Peraku';
          } 
          else if($status=="18")
          {
            $data = 'Permintaan untuk Dikemaskini oleh Peraku';
          } 
          else if($status=="19")
          {
            $data = 'Ditolak oleh Peraku';
          }
          else if(row.workflow_status=="20")
          {
            $data =  'Dibatalkan';
          }
          else
          {
            $data =  '';
          }    
        
        return $data;
    }

    private function get_project_output($project)
    {

        $outputs = [];
        foreach($project->outputProjects as $output)
        {
            array_push($outputs,$output->output_proj);
        }

        return implode(",",$outputs);
    }

    private function get_project_outcome($project)
    {
        $outcomes = [];
        foreach($project->outcomeProjects as $outcome)
        {
            array_push($outcomes,$outcome->Projek_Outcome);
        }

        return implode(",",$outcomes);
    }

    private function get_project_bhagian_terlibat($project)
    {
        $terlibat = [];
        foreach($project->bahagianTerlibat as $bahagianTerlibat)
        {
            array_push($terlibat,$bahagianTerlibat->bahagian->nama_bahagian);
        }

        return implode(",",$terlibat);
    }

    private function get_project_rmk_details($project)
    {
        $rmk = [];

        if($project->RmkObbSdg) {

            $strategi = $project->RmkObbSdg->strategi ? explode(":" ,$project->RmkObbSdg->strategi->nama_strategi): ['',''];

            $pemangkin = $project->RmkObbSdg->strategi ? explode(":" ,$project->RmkObbSdg->strategi->Tema_Pemangkin_Dasar): ['',''];
            $bab = $project->RmkObbSdg->strategi ? explode(":" ,$project->RmkObbSdg->strategi->Bab): ['',''];
            $bk = $project->RmkObbSdg->strategi ? explode(":" ,$project->RmkObbSdg->strategi->Bidang_Keutamaan): ['',''];
            $outcomeNational = [];
            array_push($outcomeNational , '') ;
            array_push($outcomeNational , $project->RmkObbSdg->strategi ? $project->RmkObbSdg->strategi->Outcome_Nasional:'') ;

            
            $obbProgram = [];
            array_push($obbProgram , '') ;
            array_push($obbProgram , $project->RmkObbSdg->obbOutputAktiviti ? $project->RmkObbSdg->obbOutputAktiviti->obb_program:'') ;

            $obbAktivti = [];
            array_push($obbAktivti , '') ;
            array_push($obbAktivti , $project->RmkObbSdg->obbOutputAktiviti ? $project->RmkObbSdg->obbOutputAktiviti->nama_aktivity:'') ;

            $obbOutputAktivti = [];
            array_push($obbOutputAktivti , $project->RmkObbSdg->obbOutputAktiviti ? $project->RmkObbSdg->obbOutputAktiviti->kod_aktivity:'') ;
            array_push($obbOutputAktivti , $project->RmkObbSdg->obbOutputAktiviti ? $project->RmkObbSdg->obbOutputAktiviti->obb_aktiviti:'') ;

            
            $rmk['strategi'] = $strategi;
            $rmk['pemangkin'] = $pemangkin;
            $rmk['bab'] = $bab;
            $rmk['bk'] = $bk;
            $rmk['outcomeNational'] = $outcomeNational;

            $rmk['obbProgram'] = $obbProgram;
            $rmk['obbAktivti'] = $obbAktivti;
            $rmk['obbOutputAktivti'] = $obbOutputAktivti;

        }else {
            $rmk['strategi'] = ['',''];
            $rmk['pemangkin'] = ['',''];
            $rmk['bab'] = ['',''];
            $rmk['bk'] = ['',''];
            $rmk['outcomeNational'] = ['',''];
            $rmk['obbProgram'] = ['',''];
            $rmk['obbAktivti'] = ['',''];
            $rmk['obbOutputAktivti'] = ['',''];
        }

        return $rmk;
    }

    private function getListCell($value)
    {
        if($value) {
            $doc = new DOMDocument();
            @$doc->loadHTML($value);
            $doc->saveHTML();
            $items = $doc->getElementsByTagName('li');
            $array_doc = [];
            $counter = 1;
            if(count($items) > 0) //Only if tag1 items are found 
            {
                foreach ($items as $tag1)
                {
                    array_push($array_doc,$counter . '. '.  $tag1->nodeValue);
                    $counter = $counter + 1;
                }
            }
            return implode("\n\n", $array_doc);
        }
        
        
        // $oCell1->createTextRun($cols)->getFont();
        // $oCell1->getActiveParagraph()->getAlignment()->setMarginLeft(10);
    }

    // /**
    // * @return \Illuminate\Support\Collection
    // */
    // public function collection()
    // {
    //     //
    //     return Student::all();
    // }
}
