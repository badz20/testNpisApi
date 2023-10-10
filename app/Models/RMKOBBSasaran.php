<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RMKOBBSasaran extends Model
{
    use HasFactory;

    protected $table = 'RMK_OBB_SDG_Sasaran';

    protected $fillable = [        
        'id',
        'Sasaran_id',
        'permohonan_projek_id',
        'dibuat_oleh',
        'dibuat_pada',
        'dikemskini_oleh',
        'dikemskini_pada',
        'row_status'       
    ];
}
