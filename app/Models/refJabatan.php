<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class refJabatan extends Model
{
    use HasFactory;

    protected $table = 'ref_jabatan';

    protected $fillable = [        
        'kod_jabatan',
        'nama_jabatan',
        'penerangan_jabatan',
        'kementerian_id',
        'dibuat_oleh',
        'dibuat_pada',
        'dikemaskini_oleh',
        'dikemaskini_pada',
        'is_hidden',
        'row_status',
    ];

    

    public function createdBy()
    {        
        return $this->belongsTo(\App\Models\User::class, 'dibuat_oleh', 'id')->withDefault();
    }

    public function updatedBy()
    {        
        return $this->belongsTo(\App\Models\User::class, 'dikemaskini_oleh', 'id')->withDefault();
    }

    public function kementerian()
    {        
        return $this->belongsTo(\App\Models\refKementerian::class, 'kementerian_id', 'id')->withDefault();
    }        
}
