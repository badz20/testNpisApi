<?php

namespace App\Models\RP;
use App\Models\Base as Model;

class RpPermohonanBahagian extends Model
{

    public function project()
    {
        return $this->belongsTo(\App\Models\RP\RpPermohonan::class, 'rp_permohonan_id', 'id')->withDefault();
    }

    public function bahagian()
    {
        return $this->belongsTo(\App\Models\refBahagian::class, 'bahagian_id', 'id')->withDefault();
    }

    public function bahagianDetail()
    {
        return $this->hasOne(\App\Models\RP\RpPermohonanDetail::class,'bahagian_id');
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
