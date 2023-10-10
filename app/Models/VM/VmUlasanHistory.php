<?php

namespace App\Models\VM;
use App\Models\Base as Model;

class VmUlasanHistory extends Model
{
    public function Va()
    {        
        return $this->hasOne(\App\Models\VM\VmMakmalKajianNilai::class,'pp_id','pp_id');
    }
}
