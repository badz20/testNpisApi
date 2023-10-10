<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Base as Model;

class JenisSubKategori extends Model
{
    use HasFactory;

    protected $table = 'ref_jenis_sub_kategori';

    public function updatedBy()
    {        
        return $this->belongsTo(\App\Models\User::class, 'dikemaskini_oleh', 'id')->withDefault();
    }

    public function createdBy()
    {        
        return $this->belongsTo(\App\Models\User::class, 'dibuat_oleh', 'id')->withDefault();
    }

    public function jenis()
    {        
        return $this->belongsTo(\App\Models\JenisKategori::class, 'kategori_id', 'id')->withDefault();
    }
}
