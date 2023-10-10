<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RMKSDGSasaranIndikator extends Model
{
    use HasFactory;

    protected $table = 'RMK_SDG_Sasaran_Indikator';

    protected $fillable = [        
        'id',
        'permohonan_projek_id',
        'SDG_id',
        'Indikatori_id',
        'Sasaran_id',
        'dibuat_oleh',
        'dibuat_pada',
        'dikemskini_oleh',
        'dikemskini_pada',
        'row_status',
        'created_at',
        'updated_at'
    ];


    public function project()
    {
        return $this->belongsTo(\App\Models\Project::class, 'permohonan_projek_id', 'id')->withDefault();
    }

    public function sdg()
    {
        return $this->belongsTo(\App\Models\RmkSdg::class, 'SDG_id', 'id')->withDefault();
    }

    public function indikator()
    {
        return $this->belongsTo(\App\Models\RmkIndikatori::class, 'Indikatori_id', 'id')->withDefault();
    }

    public function sasaran()
    {
        return $this->belongsTo(\App\Models\RmkSasaran::class, 'Sasaran_id', 'id')->withDefault();
    }

}
