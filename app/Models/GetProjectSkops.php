<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GetProjectSkops extends Model
{
    use HasFactory;
    protected $table = 'skop_projects';

    protected $fillable = [        
        'id',
        'project_id',
        'skop_project_code',
        'cost',
        'dibuat_oleh',
        'dibuat_pada',
        'dikemskini_oleh',
        'dikemskini_pada',
        'row_status',
        'created_at',
        'updated_at'

    ];
}
