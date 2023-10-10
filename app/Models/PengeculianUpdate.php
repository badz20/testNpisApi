<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class PengeculianUpdate extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;
    protected $table = 'pengeculian';

    protected $fillable = [
        'pp_id',
        'pengecualian',
        'pengeculian_khas',
        'surat_lampiran',
        'type',
        'row_status',
        'dibuat_oleh',
        'dibuat_pada',
        'dikemaskini_oleh',
        'dikemaskini_pada',
    ];
}
