<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserAuditLog extends Model
{
    use HasFactory;

    protected $table = 'user_logging_audit';

    protected $fillable = [
        'auditable_type',
        'event',
        'url',
        'ip_address',
        'user_agent',
        'created_at',
        'updated_at',
        'user_id'
    ];


    public function user()
    {        
        return $this->belongsTo(\App\Models\User::class, 'user_id', 'id')->withDefault();
    }
}
