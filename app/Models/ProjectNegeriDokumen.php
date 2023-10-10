<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class ProjectNegeriDokumen extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;
    protected $table = 'project_negeri_dokumen';

    protected $fillable = [
        'permohonan_Projek_id',
        'projek_negeri_dokumen_name',
        'keterangan',
        'row_status',
        'dibuat_oleh',
        'dibuat_pada',
        'dikemaskini_oleh',
        'dikemaskini_pada',
    ];
}
