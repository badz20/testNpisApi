<?php

namespace App\Models\RP;

use App\Models\Base as Model;

class RpSejarahBahagian extends Model
{
    public function project()
    {
        return $this->belongsTo(\App\Models\RP\RpPermohonan::class, 'rp_permohonan_id', 'id')->withDefault();
    }
}
