<?php

namespace App\Models\VM;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class vr_dockumen extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;
    protected $table = 'vr_dockumen';

    protected $fillable = [
        'pp_id',
        'type',
        'objektif_file',
        'dibuat_oleh',
        'dibuat_pada',
        'dikemaskini_oleh',
        'dikemaskini_pada',
        'row_status'
    ];
}
