<?php

namespace App\Models;

class Permission extends \Spatie\Permission\Models\Permission
{
    public const DEFAULT_PERMISSIONS = [
        'Create',
        'Read',
        'Update',
        'Delete',
    ];

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
