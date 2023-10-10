<?php

namespace App\Models\VM;
use App\Models\Base as Model;

class VmButiranFasilitator extends Model
{
    
    public function fasilitator()
    {        
        return $this->belongsTo(\App\Models\PemantauanFasilitator::class, 'fasilitator_id', 'id')->withDefault();
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
