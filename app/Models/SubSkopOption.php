<?php

namespace App\Models;

use App\Models\Base as Model;

class SubSkopOption extends Model
{
    protected $table = 'sub_skop_options';  

    public function updatedBy()
    {        
        return $this->belongsTo(\App\Models\User::class, 'dikemaskini_oleh', 'id')->withDefault();
    }

    public function createdBy()
    {        
        return $this->belongsTo(\App\Models\User::class, 'dibuat_oleh', 'id')->withDefault();
    }

    public function skop()
    {        
        return $this->belongsTo(\App\Models\SkopOption::class, 'skop_id', 'id')->withDefault();
    }
}
