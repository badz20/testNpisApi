<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class totalBayangan extends Model
{
    use HasFactory;

    protected $table = 'total_bayangan';

    protected $fillable = [        
        'id',
        'year',
        'siling_bayangan',
        'bkor_id',
        'dibuat_oleh',
        'dibuat_pada',
        'dikemaskini_oleh',
        'dikemaskini_pada',
        'row_status'       
    ];
}
