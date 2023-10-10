<?php

namespace App\Models;

use App\Models\Base as Model;

class RefDeliverable extends Model
{
 
    public function updatedBy()
    {        
        return $this->belongsTo(\App\Models\User::class, 'dikemaskini_oleh', 'id')->withDefault();
    }

    public function createdBy()
    {        
        return $this->belongsTo(\App\Models\User::class, 'dibuat_oleh', 'id')->withDefault();
    }

    public function heading()
    {        
        return $this->hasOne(\App\Models\RefDeliverableHeading::class,'id','heading_id');
    }
}
