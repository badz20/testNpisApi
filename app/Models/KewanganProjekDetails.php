<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KewanganProjekDetails extends Model
{
    use HasFactory;

    protected $table = 'Projek_Kewangan_details';

    protected $fillable = [        
        'id',
        'impak_keseluruhan',
        'ci',
        'permohonan_projek_id',
        'Komponen_id',
        'totalkos',
        'Siling_Dimohon',
        'kos_keseluruhan_oe',
        'kos_keseluruhan',
        'imbuhan_balik',
        'sst_tax',
        'temp_sst_tax',
        'jumlahkos',
        'temp_jumlahkos',
        
        'anggaran_mainworks',
        'P_max',
        'P_min',
        'P_avg',
        'design_fee',

        'imbuhanbalik_piawai',
        'cukai_sst',
        'anggarankos_piawai',

        'yuran_perunding_kos',
        'yuran_professional',
        'yuran_subprofessional',
        'yuran_imbuhanbalik',
        'yuran_ssttax',
        'yuran_anggaran',

        'yuran_perunding_kos_tapak',
        'yuran_professional_tapak',
        'yuran_subprofessional_tapak',
        'yuran_imbuhanbalik_tapak',
        'yuran_ssttax_tapak',
        'yuran_anggaran_tapak',
        

        'Siling_Bayangan',
        'updated_at',
        'created_at',
        'dibuat_oleh',
        'dibuat_pada',
        'dikemskini_oleh',
        'dikemskini_pada',
        'row_status'
    ];


    public function komponen()
    {        
        return $this->belongsTo(\App\Models\refKomponen::class,'Komponen_id','id');
    }
}
