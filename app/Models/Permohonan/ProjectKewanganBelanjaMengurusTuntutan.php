<?php

namespace App\Models\Permohonan;
use App\Models\Base as Model;

class ProjectKewanganBelanjaMengurusTuntutan extends Model
{
    protected $table = 'project_kewangan_belanja_mengurus_tuntutan';

    public function project()
    {
        return $this->belongsTo(\App\Models\Project::class, 'project_id', 'id')->withDefault();
    }

    public function belanjaMengurus()
    {
        return $this->belongsTo(\App\Models\KewanganBelanjaMengurus::class, 'belanja_mengurus_id', 'id')->withDefault();
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
