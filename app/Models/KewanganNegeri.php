<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KewanganNegeri extends Model
{
    use HasFactory;

    protected $table = 'kewangan_negeri';
    
        protected $fillable = [        
            'id',
            'pp_id',
            'negeri_id',
            'kos_data',
            'siling_yr1',
            'siling_yr2',
            'siling_yr3',
            'siling_yr4',
            'siling_yr5',
            'siling_yr6',
            'siling_yr7',
            'siling_yr8',
            'siling_yr9',
            'siling_yr10',
            'dibuat_oleh',
            'dibuat_pada',
            'dikemskini_oleh',
            'dikemskini_pada',
            'row_status'
        ];
}
