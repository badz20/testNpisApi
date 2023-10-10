<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectKpi extends Model
{
    use HasFactory;
    protected $table = 'project_kpi';

    protected $fillable = [
        'id',
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
        'row_status',
        'dibuat_oleh',
        'dibuat_pada',
        'dikemaskini_oleh',
        'dikemaskini_pada',
    ];

    public function OutputUnit()
    {        
        return $this->belongsTo(\App\Models\OutputUnit::class, 'unit', 'id')->withDefault();
    }
}
