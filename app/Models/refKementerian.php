<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class refKementerian extends Model
{
    use HasFactory;
    protected $table = 'ref_kementerian';

    protected $fillable = [
        'uuid',
        'kod_kementerian',
        'nama_kementerian',
        'penerangan_kementerian',
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
}

