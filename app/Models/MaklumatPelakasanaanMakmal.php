<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class MaklumatPelakasanaanMakmal extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;
    protected $table = 'vm_dokumen_upload';

    protected $fillable = [
        'pp_id',
        'tarikh_kemuka',
        'tarikh_terima',
        'vm_type',
        'kemuka_file_name',
        'terima_file_name',
        'dibuat_oleh',
        'dibuat_pada',
        'dikemaskini_oleh',
        'dikemaskini_pada',
        'row_status',
    ];

}
