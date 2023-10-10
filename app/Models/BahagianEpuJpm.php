<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Base as Model;

class BahagianEpuJpm extends Model
{
    use HasFactory;

    protected $table = 'ref_bahagian_epu_jpm';

    public function updatedBy()
    {        
        return $this->belongsTo(\App\Models\User::class, 'dikemaskini_oleh', 'id')->withDefault();
    }

    public function createdBy()
    {        
        return $this->belongsTo(\App\Models\User::class, 'dibuat_oleh', 'id')->withDefault();
    }

    public function utama()
    {        
        return $this->hasMany(\App\Models\SektorUtama::class,'bahagian_id');
    }

    public function sektor()
    {        
        return $this->hasMany(\App\Models\Sektor::class,'bahagian_id');
    }

    public function subSektor()
    {        
        return $this->hasMany(\App\Models\SubSektor::class,'bahagian_id');
    }
}
