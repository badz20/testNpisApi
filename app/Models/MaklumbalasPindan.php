<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class MaklumbalasPindan extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $table = 'maklumbalas_pindan';
    protected $fillable = [        
        'pp_id',
        'noc_id',
        'maklubalas_file_name',
        'maklubalas_date',
        'dibuat_oleh',
        'dibuat_pada',
        'dikemaskini_oleh',
        'dikemaskini_pada',
        'row_status'
    ];
}
