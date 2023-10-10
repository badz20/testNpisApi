<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NocKpiModule extends Model
{
    use HasFactory;
    protected $table = 'noc_kpi';
    protected $fillable = [        
        'pp_id',
        'noc_id',
        'no_rujukan',
        'project_id',
        'kuantiti',
        'unit',
        'penerangan',
        'yr_1',
        'yr_2',
        'yr_3',
        'yr_4',
        'yr_5',
        'yr_6',
        'yr_7',
        'yr_8',
        'yr_9',
        'yr_10',
        'dibuat_oleh',
        'dibuat_pada',
        'dikemaskini_oleh',
        'dikemaskini_pada',
        'is_hidden',
        'row_status',
    ];

    
}
