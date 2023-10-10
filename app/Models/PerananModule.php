<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PerananModule extends Model
{
    use HasFactory;

    protected $table = 'peranan_module';
    
    protected $fillable = [        
        'id',
        'peranan_id',
        'module_id',
        'dibuat_oleh',
        'dibuat_pada',
        'dikemskini_oleh',
        'dikemskini_pada',
        'row_status'
    ];
}
