<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserTypeRole extends Model
{
    use HasFactory;

    protected $guarded = [
        'id',
    ];

    protected $fillable = [
        'user_type_id',
        'role_id',
    ];


    public function role()
    {        
        return $this->belongsTo(\App\Models\Role::class, 'role_id', 'id')->withDefault();
    }

    public function userType()
    {        
        return $this->belongsTo(\App\Models\UserType::class, 'user_type_id', 'id')->withDefault();
    }
}
