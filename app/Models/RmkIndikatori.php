<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use \App\Models\User;


class RmkIndikatori extends Model
{
    use HasFactory;

    protected $table = 'REF_Indikatori';

    protected $fillable = [        
        'id',
        'SDG_id',
        'Sasaran_id',
        'BIL',
        'Indikatori',
        'dibuat_oleh',
        'dibuat_pada',
        'dikemskini_oleh',
        'dikemskini_pada',
        'is_active',
        'row_status'       
    ];

    public function sdg()
    {        
        return $this->belongsTo(\App\Models\RmkSdg::class, 'SDG_id', 'id')->withDefault();
    } 
    public function updatedBy()
    {        
        return $this->belongsTo(\App\Models\User::class, 'dikemaskini_oleh', 'id')->withDefault();
    }
}
