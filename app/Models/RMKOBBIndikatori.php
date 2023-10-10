<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RMKOBBIndikatori extends Model
{
    use HasFactory;

    protected $table = 'RMK_OBB_SDG_Indikatori';

    protected $fillable = [        
        'id',
        'Indikatori_id',
        'permohonan_projek_id',
        'dibuat_oleh',
        'dibuat_pada',
        'dikemskini_oleh',
        'dikemskini_pada',
        'row_status'       
    ];
}
