<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class DokumenLampiran extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $table = 'project_dokumen_lampiran';

    protected $fillable = [
        'permohonan_projek_id',
        'lfm_dokumen_nama',
        'lfm_dokumen',
        'perakuan_pengesahan_dokumen_nama',
        'perakuan_pengesahan_dokumen',
        'lain_lain_dokumen_nama1',
        'lain_lain_dokumen1',
        'lain_lain_dokumen_nama2',
        'lain_lain_dokumen2',
        'lain_lain_dokumen_nama3',
        'lain_lain_dokumen3',
        'lain_lain_dokumen_nama4',
        'lain_lain_dokumen4',
        'lain_lain_dokumen_nama5',
        'lain_lain_dokumen5',
        'dibuat_oleh',
        'dibuat_pada',
        'dikemaskini_oleh',
        'dikemaskini_pada',
        'row_status'
    ];
}
