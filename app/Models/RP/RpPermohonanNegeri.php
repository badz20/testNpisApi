<?php

namespace App\Models\RP;

use App\Models\Base as Model;

class RpPermohonanNegeri extends Model
{
    public function project()
    {
        return $this->belongsTo(\App\Models\RP\RpPermohonan::class, 'rp_permohonan_id', 'id')->withDefault();
    }

    public function negeri()
    {
        return $this->belongsTo(\App\Models\refNegeri::class, 'negeri_id', 'id')->withDefault();
    }

    public function daerah()
    {
        return $this->belongsTo(\App\Models\refDaerah::class, 'daerah_id', 'id')->withDefault();
    }

    public function parliment()
    {
        return $this->belongsTo(\App\Models\refParlimen::class, 'parliment_id', 'id')->withDefault();
    }

    public function dun()
    {
        return $this->belongsTo(\App\Models\refDun::class, 'dun_id', 'id')->withDefault();
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
