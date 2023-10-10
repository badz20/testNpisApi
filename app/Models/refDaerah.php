<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class refDaerah extends Model
{
    use HasFactory;

    protected $table = 'ref_daerah';

    protected $fillable = [        
        'kod_daerah',
        'kod_negeri',
        'nama_daerah',
        'penerangan_daerah',
        'negeri_id',
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

    public function mukim()
    {        
        return $this->hasMany(\App\Models\refMukim::class,'daerah_id');
    }
}
