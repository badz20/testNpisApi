<?php

namespace App\Models;
use App\Models\Base as Model;


class RefDeliverableHeading extends Model
{
    public function updatedBy()
    {        
        return $this->belongsTo(\App\Models\User::class, 'dikemaskini_oleh', 'id')->withDefault();
    }

    public function createdBy()
    {        
        return $this->belongsTo(\App\Models\User::class, 'dibuat_oleh', 'id')->withDefault();
    }

    public function deliverables()
    {        
        return $this->hasMany(\App\Models\RefDeliverable::class,'heading_id');
    }
}
