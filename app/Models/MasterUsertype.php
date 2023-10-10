<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterUsertype extends Model
{
    use HasFactory;
    protected $table = 'master_usertype';
    protected $fillable = [        
        'id',
        'module_id',
        'user_type',
        'dibuat_oleh',
        'dibuat_pada',
        'dikemskini_oleh',
        'dikemskini_pada',
        'row_status'
    ];

}
