<?php

namespace App\Models\Permohonan;
use App\Models\Base as Model;

class KewanganBelanjaMengurus extends Model
{
    protected $table = 'project_kewangan_belanja_mengurus';


    public function project()
    {
        return $this->belongsTo(\App\Models\Project::class, 'project_id', 'id')->withDefault();
    }

    public function belanjaDetails()
    {        
        return $this->hasMany(\App\Models\Permohonan\KewanganBelanjaMengurusDetails::class,'belanja_mengurus_id');
    }

    public function belanjaTuntutan()
    {        
        return $this->hasOne(\App\Models\Permohonan\ProjectKewanganBelanjaMengurusTuntutan::class,'belanja_mengurus_id');
    }
    
    public function updatedBy()
    {        
        return $this->belongsTo(\App\Models\User::class, 'dikemaskini_oleh', 'id')->withDefault();
    }

    public function createdBy()
    {        
        return $this->belongsTo(\App\Models\User::class, 'dibuat_oleh', 'id')->withDefault();
    }
}