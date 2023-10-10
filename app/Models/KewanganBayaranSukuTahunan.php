<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KewanganBayaranSukuTahunan extends Model
{
    use HasFactory;

    protected $table = 'projek_kewangan_bayaran_suku_tahunan';
    
        protected $fillable = [        
            'id',
            'permohonan_projek_id',
            'skop_id',
            'yr1_quarters1',
            'yr1_quarters2',
            'yr1_quarters3',
            'yr1_quarters4',
            'yr1_quarters5',
            'yr1_quarters6',
            'yr1_quarters7',
            'yr1_quarters8',
            'yr1_quarters9',
            'yr1_quarters10',
            'yr1_quarters11',
            'yr1_quarters12',
            'yr1_quarters13',
            'yr1_quarters14',
            'yr1_quarters15',
            'yr1_quarters16',
            'yr1_quarters17',
            'yr1_quarters18',
            'yr1_quarters19',
            'yr1_quarters20',
            'yr1_quarters21',
            'yr1_quarters22',
            'yr1_quarters23',
            'yr1_quarters24',
            'yr1_quarters25',
            'yr1_quarters26',
            'yr1_quarters27',
            'yr1_quarters28',
            'yr1_quarters29',
            'yr1_quarters30',
            'yr1_quarters31',
            'yr1_quarters32',
            'yr1_quarters33',
            'yr1_quarters34',
            'yr1_quarters35',
            'yr1_quarters36',
            'yr1_quarters37',
            'yr1_quarters38',
            'yr1_quarters39',
            'yr1_quarters40',
            'dibuat_oleh',
            'dibuat_pada',
            'dikemskini_oleh',
            'dikemskini_pada',
            'row_status'
        ];


    public function skop()
    {        
        return $this->belongsTo(\App\Models\SkopOption::class, 'skop_id', 'id')->withDefault();
    }
}
