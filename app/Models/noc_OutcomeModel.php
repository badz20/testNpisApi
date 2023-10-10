<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class noc_OutcomeModel extends Model
{
    use HasFactory;
    protected $table = 'noc_outcome';
    protected $fillable = [        
        'pp_id',
        'noc_id',
        'no_rujukan',
        'Permohonan_Projek_id',
        'Projek_Outcome',
        'Kuantiti',
        'unit_id',
        'dibuat_oleh',
        'dibuat_pada',
        'dikemaskini_oleh',
        'dikemaskini_pada',
        'row_status',
    ];
}
