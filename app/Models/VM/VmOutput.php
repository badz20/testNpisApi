<?php

namespace App\Models\VM;
use App\Models\Base as Model;

class VmOutput extends Model
{
    public function Va()
    {        
        return $this->hasOne(\App\Models\VM\VmMakmalKajianNilai::class,'pp_id','pp_id');
    }

    public function project()
    {
        return $this->belongsTo(\App\Models\PemantauanProject::class, 'pp_id', 'id')->withDefault();
    }

    public function unit()
    {
        return $this->belongsTo(\App\Models\Units::class, 'unit_selepas', 'id')->withDefault();
    }
}
