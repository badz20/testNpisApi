<?php

namespace App\Models\RP;

use App\Models\Base as Model;

class RpPermohonan extends Model
{

    public function bahagians()
    {        
        return $this->hasMany(\App\Models\RP\RpPermohonanBahagian::class,'rp_permohonan_id');
    }

    public function negeris()
    {        
        return $this->hasOne(\App\Models\RP\RpPermohonanNegeri::class,'rp_permohonan_id');
    }

    public function negeriDetails()
    {        
        return $this->hasOne(\App\Models\RP\RpPermohonanNegeriDetail::class,'rp_permohonan_id');
    }

    public function butirans()
    {        
        return $this->hasMany(\App\Models\RP\RpPermohonanButiran::class,'rp_permohonan_id');
    }

    // public function details()
    // {        
    //     return $this->hasOne(\App\Models\RP\RpPermohonanDetail::class,'rp_permohonan_id');
    // }

    public function sejarahNegeri()
    {        
        return $this->hasMany(\App\Models\RP\RpSejarahNegeri::class,'rp_permohonan_id');
    }

    public function sejarahNegeriUlasan()
    {        
        return $this->hasMany(\App\Models\RP\RpSejarahNegeriUlasan::class,'rp_permohonan_id');
    }

    public function sejarahBahagian()
    {        
        return $this->hasMany(\App\Models\RP\RpSejarahBahagian::class,'rp_permohonan_id');
    }

    public function sejarahBahagianUlasan()
    {        
        return $this->hasMany(\App\Models\RP\RpSejarahBahagianUlasan::class,'rp_permohonan_id');
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