<?php

namespace App\Models\VM;

use App\Models\Base as Model;

class VmButiran extends Model
{
 
    public function fasilitators()
    {        
        return $this->hasMany(\App\Models\VM\VmButiranFasilitator::class,'butiran_id');
    }

    public function updatedBy()
    {        
        return $this->belongsTo(\App\Models\User::class, 'dikemaskini_oleh', 'id')->withDefault();
    }

    public function createdBy()
    {        
        return $this->belongsTo(\App\Models\User::class, 'dibuat_oleh', 'id')->withDefault();
    }

    public function Va()
    {        
        return $this->hasOne(\App\Models\VM\VmMakmalKajianNilai::class,'pp_id','pp_id');
    }

    public function project()
    {
        return $this->belongsTo(\App\Models\PemantauanProject::class, 'pp_id', 'id')->withDefault();
    }
}
