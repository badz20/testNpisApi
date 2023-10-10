<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Base as Model;

class JenisKategori extends Model
{
    use HasFactory;

    protected $table = 'ref_jenis_kategori';

    public function updatedBy()
    {        
        return $this->belongsTo(\App\Models\User::class, 'dikemaskini_oleh', 'id')->withDefault();
    }

    public function createdBy()
    {        
        return $this->belongsTo(\App\Models\User::class, 'dibuat_oleh', 'id')->withDefault();
    }

    public function subJenis()
    {        
        return $this->hasMany(\App\Models\JenisSubKategori::class,'kategori_id');        
    }
}
