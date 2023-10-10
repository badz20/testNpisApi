<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GetSkopOptions extends Model
{
    use HasFactory;

    protected $table = 'skop_options';

    protected $fillable = [        
        'id',
        'skop_code',
        'skop_name',        
        'dibuat_oleh',
        'dibuat_pada',
        'dikemskini_oleh',
        'dikemskini_pada',
        'row_status',
        'created_at',
        'updated_at'

    ];
}
