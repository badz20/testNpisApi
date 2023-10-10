<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class refBahagian extends Model
{
    use HasFactory;

    protected $table = 'ref_bahagian';

    protected $fillable = [        
        'kod_bahagian',
        'acym',
        'nama_bahagian',
        'penerangan_bahagian',
        'kementerian_id',
        'jabatan_id',
        'dibuat_oleh',
        'dibuat_pada',
        'dikemaskini_oleh',
        'dikemaskini_pada',
        'is_hidden',
        'row_status',
    ];

    public function updatedBy()
    {        
        return $this->belongsTo(\App\Models\User::class, 'dikemaskini_oleh', 'id')->withDefault();
    }

    public function createdBy()
    {        
        return $this->belongsTo(\App\Models\User::class, 'dibuat_oleh', 'id')->withDefault();
    }

    public function kementerian()
    {        
        return $this->belongsTo(\App\Models\refKementerian::class, 'kementerian_id', 'id')->withDefault();
    }    

    public function jabatan()
    {        
        return $this->belongsTo(\App\Models\refJabatan::class, 'jabatan_id', 'id')->withDefault();
    }
}
