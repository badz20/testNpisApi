<?php

namespace App\Models\Perunding;
use App\Models\Base as Model;

class PerundingMaklumatSa extends Model
{
    public function pemantauanProject()
    {
        return $this->belongsTo(\App\Models\PemantauanProject::class, 'pemantauan_id', 'id')->withDefault();
    }

    public function perolehanProject()
    {
        return $this->belongsTo(\App\Models\PemantauanPerolehan::class, 'perolehan_id', 'id')->withDefault();
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