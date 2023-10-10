<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KewanganSkopSilling extends Model
{
      use HasFactory;
    
        protected $table = 'projek_kewangan_siling_kos';
    
        protected $fillable = [        
            'id',
            'permohonan_projek_id',
            'skop_id',
            'siling_yr1',
            'siling_yr2',
            'siling_yr3',
            'siling_yr4',
            'siling_yr5',
            'siling_yr6',
            'siling_yr7',
            'siling_yr8',
            'siling_yr9',
            'siling_yr10',
            'jumlah_kos',
            'dibuat_oleh',
            'dibuat_pada',
            'dikemskini_oleh',
            'dikemskini_pada',
            'row_status'
        ];

    public function skop()
    {        
        return $this->belongsTo(\App\Models\SkopProject::class, 'skop_id', 'id')->withDefault();
    }

}