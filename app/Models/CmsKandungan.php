<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class CmsKandungan extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $table = 'cms_kandungan';

    protected $fillable = [
        'unique_key',
        'tajuk',
        'keterangan',
        'json_values',
        'is_video',
        'row_status',
        'dibuat_oleh',
        'dibuat_pada',
        'dikemaskini_oleh',
        'dikemaskini_pada',
    ];
}
