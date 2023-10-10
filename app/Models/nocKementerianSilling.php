<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;


class nocKementerianSilling extends Model implements HasMedia
{
  use HasFactory, InteractsWithMedia;
  protected $table = 'noc_kementerian_silling';

    protected $fillable = [
      'noc_id',
      'kementerian_tarikh',
      'kementerian_file_name',
      'kelulusan_tarikh',
      'kelulusan_file_name', 
      'dibuat_oleh',
      'dibuat_pada',
      'dikemaskini_oleh',
      'dikemaskini_pada',
      'row_status',
    ];
}
