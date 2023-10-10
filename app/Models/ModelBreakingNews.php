<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ModelBreakingNews extends Model
{
    use HasFactory;
    protected $table = 'cms_breaking_news';

    protected $fillable = [        
        'id',
        'news',
        'dibuat_oleh',
        'dibuat_pada',
        'dikemskini_oleh',
        'dikemskini_pada',
        'row_status'
    ];
}
