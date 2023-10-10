<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class nocSelectedProjeck extends Model
{
    use HasFactory;

    protected $table = 'noc_selected_projeck';
    protected $fillable = [
                            'id',
                            'noc_id',
                            'pp_id',
                            'no_rujukan',
                            'kod_projeck',
                            'butiran_code',
                            'nama_projek',
                            'pembiyan',
                            'kos_projeck',
                            'keseruluhan_kos',
                            'baki_kos',
                            'peruntukan_kos',
                            'peruntukan_asal',
                            'tambah', 
                            'kurang',
                            'dipinda',
                            'justifikasi',
                            'bahagian_pemilik',
                            'dibuat_oleh',
                            'dibuat_pada',
                            'dikemaskini_oleh',
                            'dikemaskini_pada',
                            'status_id',
                            'row_status',
    ];

    public function bahagianPemilik()
    {
        return $this->belongsTo(\App\Models\refBahagian::class, 'bahagian_pemilik', 'id')->withDefault();
    }

    public function peruntukan()
    {
        return $this->belongsTo(\App\Models\PemantaunKewanganMaklumatPeruntukan::class, 'pp_id', 'pp_id')->withDefault();
    }
  
}
