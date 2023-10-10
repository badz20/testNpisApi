<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Base as Model;

class SektorUtama extends Model
{    
    protected $table = 'ref_sektor_utama';

    public function updatedBy()
    {        
        return $this->belongsTo(\App\Models\User::class, 'dikemaskini_oleh', 'id')->withDefault();
    }

    public function createdBy()
    {        
        return $this->belongsTo(\App\Models\User::class, 'dibuat_oleh', 'id')->withDefault();
    }

    public function sektor()
    {        
        return $this->hasMany(\App\Models\Sektor::class,'sektor_utama_id');
    }

    public function subSektor()
    {        
        return $this->hasMany(\App\Models\SubSektor::class,'sektor_utama_id');
    }

    public function bahagian()
    {        
        return $this->belongsTo(\App\Models\BahagianEpuJpm::class, 'bahagian_id', 'id')->withDefault();
    }
}
