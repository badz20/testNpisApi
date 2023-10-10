<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GetSubSkopOptions extends Model
{
    use HasFactory;

    protected $table = 'sub_skop_options';

    protected $fillable = [        
        'id',
        'sub_skop_code',
        'sub_skop_name',        
        'skop_id',
        'dibuat_oleh',
        'dibuat_pada',
        'dikemskini_oleh',
        'dikemskini_pada',
        'row_status',
        'created_at',
        'updated_at'

    ];
}
