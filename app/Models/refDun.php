<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class refDun extends Model
{
    use HasFactory;

    protected $table = 'ref_dun';

    protected $fillable = [        
        'kod_dun',
        'nama_dun',
        'penerangan_dun',
        'negeri_id',
        'daerah_id',
        'parlimen_id',
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

    public function negeri()
    {        
        return $this->belongsTo(\App\Models\refNegeri::class, 'negeri_id', 'id')->withDefault();
    }    

    public function parlimen()
    {        
        return $this->belongsTo(\App\Models\refParlimen::class, 'parlimen_id', 'id')->withDefault();
    }
}
