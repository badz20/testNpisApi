<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PemantauanProject extends Model
{
    use HasFactory;

    protected $table = 'pemantauan_project';


    protected $casts = [
        'id',
        'kod_projeck',
        'melibat_pembinaan_fasa',
        'pernah_dibahasakan',
        'rujukan_pelan_induk',
        'sokongan_upen',        
        'bahagian_id',
        'bahagian_epu_id',        
        'butiran_code',
        'dibuat_oleh',
        'dikemaskini_oleh',
        'jenis_kajian',
        'jenis_kategori_code',
        'kajian',
        'kekerapan_banjir_code',
        'koridor_pembangunan',
        'kululusan_khas',
        'melibat_pembinaan_fasa_status',
        'rolling_plan_code',
        'sektor_id',
        'sektor_utama_id',
        'rujukan_code',
        'status_reka_bantuk',
        'sub_sektor_id',
        'status_reka_bantuk',
        'noc_status'
    ];

    public function skopProjects()
    {        
        return $this->hasMany(\App\Models\PemantauanSkopOption::class,'kod_projeck');
    }

    public function subskopProjects()
    {        
        return $this->hasMany(\App\Models\PemantauanSubSkopOption::class,'kod_projeck');
    }

    public function perolehan()
    {        
        return $this->hasMany(\App\Models\Perunding\PemantauanPerolehan::class,'pemantauan_id');
    }

    public function negeri()
    {
        return $this->belongsTo(\App\Models\refNegeri::class, 'negeri_id', 'id')->withDefault();
    }

    public function daerah()
    {
        return $this->belongsTo(\App\Models\refDaerah::class, 'daerah_id', 'id')->withDefault();
    }

    public function bahagianPemilik()
    {
        return $this->belongsTo(\App\Models\refBahagian::class, 'bahagian_pemilik', 'id')->withDefault();
    }

    public function rmk()
    {
        return $this->belongsTo(\App\Models\RollingPlan::class, 'rmk', 'rmk')->withDefault();
    }
}