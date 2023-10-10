<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class CmsHeader extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $table = 'cms_header';

    protected $fillable = [
        'uuid',
        'header_navbar_logo_1',
        'header_navbar_logo_2',
        'header_navbar_logo_3',
        'dibuat_oleh',
        'dibuat_pada',
        'dikemaskini_oleh',
        'dikemaskini_pada',
        'row_status',
    ];
}
