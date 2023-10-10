<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserPeranan extends Model
{
    use HasFactory;

    protected $table = 'user_peranan';
    
    protected $fillable = [        
        'id',
        'user_id',
        'peranan_id',
        'dibuat_oleh',
        'dibuat_pada',
        'dikemskini_oleh',
        'dikemskini_pada',
        'row_status'
    ];
}
