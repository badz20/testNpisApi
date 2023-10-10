<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class refNegeri extends Model
{
    use HasFactory;

    protected $table = 'ref_negeri';

    protected $fillable = [
        'uuid',
        'kod_negeri',
        'nama_negeri',
        'penerangan_negeri',
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

    public function daerah()
    {        
        return $this->hasMany(\App\Models\refDaerah::class,'negeri_id');        
    }
}
