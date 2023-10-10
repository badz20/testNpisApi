<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
//use Illuminate\Database\Eloquent\Model;
use App\Models\Base as Model;

class Project extends Model
{

    protected $table = 'projects';

    protected $casts = [
        'melibat_pembinaan_fasa' => 'integer',
        'pernah_dibahasakan' => 'integer',
        'rujukan_pelan_induk' => 'integer',
        'sokongan_upen' => 'integer',        
        'bahagian_id' => 'integer',
        'bahagian_epu_id' => 'integer',        
        'butiran_code' => 'integer',
        'dibuat_oleh' => 'integer',
        'dikemaskini_oleh' => 'integer',
        'jenis_kajian' => 'integer',
        'jenis_kategori_code' => 'integer',
        'kajian' => 'integer',
        'kekerapan_banjir_code' => 'integer',
        'koridor_pembangunan' => 'integer',
        'kululusan_khas' => 'integer',
        'melibat_pembinaan_fasa_status' => 'integer',
        'rolling_plan_code' => 'integer',
        'sektor_id' => 'integer',
        'sektor_utama_id' => 'integer',
        'rujukan_code' => 'integer',
        'status_reka_bantuk' => 'integer',
        'sub_sektor_id' => 'integer',
        'status_reka_bantuk' => 'integer'
    ];

    public function skopProjects()
    {        
        return $this->hasMany(\App\Models\SkopProject::class,'project_id');
    }

    public function subskopProjects()
    {        
        return $this->hasMany(\App\Models\KewanganSkop::class,'permohonan_projek_id');
    }

    public function kajianProjects()
    {        
        return $this->hasMany(\App\Models\ProjectKajian::class,'project_id');
    }

    public function bahagianTerlibat()
    {        
        return $this->hasMany(\App\Models\BahagianTerlibat::class,'project_id');
    }

    public function outcomeProjects()
    {        
        return $this->hasMany(\App\Models\Outcome::class,'Permohonan_Projek_id');
    }

    public function outputProjects()
    {        
        return $this->hasMany(\App\Models\OutputPage::class,'Permohonan_Projek_id');
    }

    public function bahagianPemilik()
    {
        return $this->belongsTo(\App\Models\refBahagian::class, 'bahagian_pemilik', 'id')->withDefault();
    }

    public function jenisKategori()
    {
        return $this->belongsTo(\App\Models\JenisKategori::class, 'jenis_kategori_code', 'id')->withDefault();
    }

    public function jenisSubKategori()
    {
        return $this->belongsTo(\App\Models\JenisSubKategori::class, 'jenis_sub_kategori_code', 'id')->withDefault();
    }

    public function negeri()
    {
        return $this->belongsTo(\App\Models\refNegeri::class, 'negeri_id', 'id')->withDefault();
    }

    public function daerah()
    {
        return $this->belongsTo(\App\Models\refDaerah::class, 'daerah_id', 'id')->withDefault();
    }

    public function createdBy()
    {        
        return $this->belongsTo(\App\Models\User::class, 'dibuat_oleh', 'id')->withDefault();
    }

    public function updatedBy()
    {        
        return $this->belongsTo(\App\Models\User::class, 'dikemaskini_oleh', 'id')->withDefault();
    }

    public function penyemak()
    {        
        return $this->belongsTo(\App\Models\User::class, 'penyemak', 'id')->withDefault();
    }

    public function penyemak1()
    {        
        return $this->belongsTo(\App\Models\User::class, 'penyemak_1', 'id')->withDefault();
    }

    public function penyemak2()
    {        
        return $this->belongsTo(\App\Models\User::class, 'penyemak_2', 'id')->withDefault();
    }

    public function pengesah()
    {        
        return $this->belongsTo(\App\Models\User::class, 'pengesah', 'id')->withDefault();
    }

    public function peraku()
    {        
        return $this->belongsTo(\App\Models\User::class, 'peraku', 'id')->withDefault();
    }

    public function kewangan()
    {        
        return $this->belongsTo(\App\Models\KewanganProjekDetails::class, 'id', 'permohonan_projek_id')->withDefault();
    
    }

    public function lokasi()
    {        
        return $this->belongsTo(\App\Models\ProjectNegeriLokas::class,'id','permohonan_Projek_id')->withDefault();
    }

    public function rollingPlan()
    {        
        return $this->belongsTo(\App\Models\RollingPlan::class,'rolling_plan_code');
    }

    public function RmkObbSdg()
    {        
        return $this->belongsTo(\App\Models\RmkObbPage::class,'id','permohonan_projek_id');
    }

    public function kementerian(){
        return $this->belongsTo(\App\Models\refKementerian::class,'kod_baharu','kod_kementerian');
    }

    public function butiran()
    {

        return $this->belongsTo(\App\Models\LookupOption::class, 'butiran_code', 'code')->withDefault();
    }

    public function sektor()
    {
        
        return $this->belongsTo(\App\Models\Sektor::class, 'sektor_id', 'id')->withDefault();
    }

    public function sektorUtama()
    {
        
        return $this->belongsTo(\App\Models\SektorUtama::class, 'sektor_utama_id', 'id')->withDefault();
    }

    public function subSektor()
    {
        
        return $this->belongsTo(\App\Models\SubSektor::class, 'sub_sektor_id', 'id')->withDefault();
    }

    public function vae()
    {
        
        return $this->belongsTo(\App\Models\vae::class, 'id', 'Permohonan_Projek_id')->withDefault();
    }

    public function documenLampiran()
    {        
        return $this->belongsTo(\App\Models\DokumenLampiran::class, 'id', 'permohonan_projek_id')->withDefault();
    }

    public function Bahagian()
    {        
        return $this->belongsTo(\App\Models\refBahagian::class, 'bahagian_pemilik', 'id')->withDefault();
    }

    public function negerilist()
    {        
        return $this->hasMany(\App\Models\ProjectNegeriLokas::class,'permohonan_Projek_id');
    }

}


