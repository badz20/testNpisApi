<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class NOCPeruntukan extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $table = 'noc_peruntukan';
    protected $fillable = [        
        'id',
        'bilangan',
        'tahun',
        'tarikh_buka',
        'tarikh_tutup',
        'status_permohonan',
        'status',
        'dibuat_oleh',
        'dibuat_pada',
        'dikemaskini_oleh',
        'dikemaskini_pada',
        'row_status',
    ];
    
}
