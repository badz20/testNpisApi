<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class vm_tandatangan extends Model  implements HasMedia
{
    use HasFactory, InteractsWithMedia;
    protected $table = 'vm_tandatangan';

    protected $fillable = [
        'pp_id',
      'kategori_tandatangan',
      'tarikh_tandatangan',
      'tandatangan_file_name',
      'dibuat_oleh',
      'dibuat_pada',
      'dikemaskini_oleh',
      'dikemaskini_pada',
      'row_status',
    ];
}
