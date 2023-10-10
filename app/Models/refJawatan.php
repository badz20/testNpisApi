<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class refJawatan extends Model
{
    use HasFactory;

    protected $table = 'ref_jawatan';

    // public function user()
    // {
    //     return $this->belongsTo(\App\Models\User::class, 'jawatan_id', 'id')->withDefault();
    // }

    protected $fillable = [        
        'kod_jawatan',
        'nama_jawatan',
        'penerangan_jawatan',
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
