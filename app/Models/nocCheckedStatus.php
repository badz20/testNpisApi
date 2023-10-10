<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class nocCheckedStatus extends Model
{
    use HasFactory;
    protected $table = 'noc_checked_status';
    protected $fillable = [        
        'noc_id',
        'pp_id',
        'skop_status',
        'kos_status',
        'butiran_status',
        'semula_status',
        'nama_status',
        'lokasi_status',
        'kpi_status',
        'outcome_status',
        'kod_status',
        'objectif_status',
        'output_status',
        'dibuat_oleh',
        'dibuat_pada',
        'dikemaskini_oleh',
        'dikemaskini_pada',
        'row_status',
    ];
}
