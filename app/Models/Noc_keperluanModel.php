<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Noc_keperluanModel extends Model
{
    use HasFactory;

    protected $table = 'noc_keperluan_peruntukan';

    protected $fillable = [        
        'pemantauan_id',
        'tarikh_kemaskini',
        'kepeluruan_amaun',
        'justifikasi',
        'rekod_permohonan',
        'amaun',
        'taikh_waran',
        'waran_tambahan',
        'waran_pemulangan',
        'dibuat_oleh',
        'dibuat_pada',
        'dikemaskini_oleh',
        'dikemaskini_pada',
        'row_status',
    ];
}
