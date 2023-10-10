<?php

namespace App\Models\VM;
use App\Models\Base as Model;

class VmMakmalKajianNilai extends Model
{
    public function project()
    {
        return $this->belongsTo(\App\Models\PemantauanProject::class, 'pp_id', 'id')->withDefault();
    }
}
