<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;


class Penjilidan extends Model  implements HasMedia
{
    use HasFactory, InteractsWithMedia;
    protected $table = 'penjilidan';

    protected $fillable = [
      'pp_id',
      'tarikh',
      'peranan',
      'type',
      'penjilidan_file',
      'lampiran',
      'dibuat_oleh',
      'dibuat_pada',
      'dikemaskini_oleh',
      'dikemaskini_pada',
      'row_status',
    ];
}
