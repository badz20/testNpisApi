<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Base as Model;

class Sektor extends Model
{
    use HasFactory;

    protected $table = 'ref_sektor';  
    
    public function updatedBy()
    {        
        return $this->belongsTo(\App\Models\User::class, 'dikemaskini_oleh', 'id')->withDefault();
    }

    public function createdBy()
    {        
        return $this->belongsTo(\App\Models\User::class, 'dibuat_oleh', 'id')->withDefault();
    }

    public function subSektor()
    {        
        return $this->hasMany(\App\Models\SubSektor::class,'sektor_id');
    }

    public function sektorUtama()
    {        
        return $this->belongsTo(\App\Models\SektorUtama::class, 'sektor_utama_id', 'id')->withDefault();
    }

    public function bahagian()
    {        
        return $this->belongsTo(\App\Models\BahagianEpuJpm::class, 'bahagian_id', 'id')->withDefault();
    }

}
