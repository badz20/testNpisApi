<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RmkSDGIndikator extends Model
{
    use HasFactory;

    protected $table = 'RMK_SDG_Indikator';

    protected $fillable = [        
        'id',
        'permohonan_projek_id',
        'SDG_id',
        'Indikatori_id',
        'dibuat_oleh',
        'dibuat_pada',
        'dikemskini_oleh',
        'dikemskini_pada',
        'row_status',
        'created_at',
        'updated_at'      
    ];

    public function indikator()
    {
        return $this->belongsTo(\App\Models\RmkIndikatori::class, 'Indikatori_id', 'id')->withDefault();
    }
}
