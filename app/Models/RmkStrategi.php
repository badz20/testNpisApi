<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use \App\Models\User;

class RmkStrategi extends Model
{
    use HasFactory;

    protected $table = 'REF_Strategi';

    protected $fillable = [        
        'id',
        'nama_strategi',
        'Tema_Pemangkin_Dasar',
        'Bab',
        'Bidang_Keutamaan',
        'Outcome_Nasional',
        'Catatan',
        'dibuat_oleh',
        'dibuat_pada',
        'dikemskini_oleh',
        'dikemskini_pada',
        'row_status'       
    ];
    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'dibuat_oleh', 'id')->withDefault();
    }

}
