<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RmkObbPage extends Model
{

    use HasFactory;
    public $timestamps = false;
    protected $table = 'RMK_OBB_SDG';

    protected $fillable = [        
        'id',
        'permohonan_projek_id',
        'Pemangkin_Dasar',
        'Bab',
        'Bidang_Keutamaan',
        'Outcome_Nasional',
        'Strategi',
        'OBB_Program',
        'OBB_Aktiviti',
        'OBB_Output_Aktiviti_id',
        'SDG_id',
        'Indikatori_id',
        'Sasaran_id',
        'dibuat_oleh',
        'dibuat_pada',
        'dikemskini_oleh',
        'dikemskini_pada',
        'row_status'        
    ];

    public function strategi()
    {        
        return $this->belongsTo(\App\Models\RmkStrategi::class,'Strategi','id');
    }

    public function obbOutputAktiviti()
    {        
        return $this->belongsTo(\App\Models\RmkObb::class,'OBB_Output_Aktiviti_id','id');
    }
}
