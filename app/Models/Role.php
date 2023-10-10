<?php

namespace App\Models;

class Role extends \Spatie\Permission\Models\Role
{

    protected $table = 'roles';
    
    protected $guarded = [
        'id',
    ];

    public function updatedBy()
    {        
        return $this->belongsTo(\App\Models\User::class, 'dikemaskini_oleh', 'id')->withDefault();
    }

    public function createdBy()
    {        
        return $this->belongsTo(\App\Models\User::class, 'dibuat_oleh', 'id')->withDefault();
    }
}
