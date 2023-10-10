<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class nocKementerianEconomiSilling extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;
    protected $table = 'noc_kementerian_economi_silling';

    protected $fillable = [
        'noc_id',
        'economi_tarikh',
        'economi_file_name',
        'economi_surat_tarikh',
        'economi_surat_file_name',
        'status',
        'dibuat_oleh',
        'dibuat_pada',
        'dikemaskini_oleh',
        'dikemaskini_pada',
        'row_status',
    ];
}
