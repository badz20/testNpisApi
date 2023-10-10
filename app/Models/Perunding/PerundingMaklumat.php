<?php

namespace App\Models\Perunding;
use App\Models\Base as Model;

class PerundingMaklumat extends Model
{
    public function pemantauanProject()
    {
        return $this->belongsTo(\App\Models\PemantauanProject::class, 'pemantauan_id', 'id')->withDefault();
    }

    public function perolehanProject()
    {
        return $this->belongsTo(\App\Models\Perunding\PemantauanPerolehan::class, 'perolehan_id', 'id')->withDefault();
    }

    public function eocp()
    {        
        return $this->hasMany(\App\Models\Perunding\PerundingMaklumatEocp::class,'maklumat_id');
    }

    public function sa()
    {        
        return $this->hasMany(\App\Models\Perunding\PerundingMaklumatSa::class,'maklumat_id');
    }

    public function perlindugan()
    {        
        return $this->hasMany(\App\Models\Perunding\PerundingMaklumatPerlindungan::class,'maklumat_id');
    }

    public function updatedBy()
    {        
        return $this->belongsTo(\App\Models\User::class, 'dikemaskini_oleh', 'id')->withDefault();
    }

    public function createdBy()
    {        
        return $this->belongsTo(\App\Models\User::class, 'dibuat_oleh', 'id')->withDefault();
    }

   
}