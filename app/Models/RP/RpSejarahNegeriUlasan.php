<?php

namespace App\Models\RP;

use App\Models\Base as Model;

class RpSejarahNegeriUlasan extends Model
{
    public function project()
    {
        return $this->belongsTo(\App\Models\RP\RpPermohonan::class, 'rp_permohonan_id', 'id')->withDefault();
    }

    public function bahagian()
    {
        return $this->belongsTo(\App\Models\refBahagian::class, 'bahagian_id', 'id')->withDefault();
    }
}
