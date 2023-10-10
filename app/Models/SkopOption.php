<?php

namespace App\Models;

use App\Models\Base as Model;

class SkopOption extends Model
{
    protected $table = 'skop_options';
    
    public function updatedBy()
    {        
        return $this->belongsTo(\App\Models\User::class, 'dikemaskini_oleh', 'id')->withDefault();
    }

    public function createdBy()
    {        
        return $this->belongsTo(\App\Models\User::class, 'dibuat_oleh', 'id')->withDefault();
    }

    public function subskop()
    {        
        return $this->hasMany(\App\Models\SubSkopOption::class,'skop_id');
    }

}
