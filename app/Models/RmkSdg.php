<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use \App\Models\User;


class RmkSdg extends Model
{
    use HasFactory;

    protected $table = 'REF_RMK_SDG';

    protected $fillable = [        
        'id',
        'nama_sdg',
        'kod_sdg',
        'dibuat_oleh',
        'dibuat_pada',
        'dikemskini_oleh',
        'dikemskini_pada',
        'is_active',
        'row_status'        
    ];

    public function updatedBy()
    {        
        return $this->belongsTo(\App\Models\User::class, 'dikemaskini_oleh', 'id')->withDefault();
    }
}
