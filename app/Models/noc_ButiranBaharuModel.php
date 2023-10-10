<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class noc_ButiranBaharuModel extends Model
{
    use HasFactory;
    protected $table = 'noc_butiran_baharu';
    protected $fillable = [        
        'noc_id',
        'nama_projek',
        'kod_projek',
        'justifikasi',
        'kos_projek',
        'keperluan',
        'dibuat_oleh',
        'dibuat_pada',
        'dikemaskini_oleh',
        'dikemaskini_pada',
        'row_status',
        'pp_id',
    ];
}
