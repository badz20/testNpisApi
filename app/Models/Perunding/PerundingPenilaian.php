<?php

namespace App\Models\Perunding;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Base as Model;

class PerundingPenilaian extends Model
{
    public function pemantauanProject()
    {
        return $this->belongsTo(\App\Models\PemantauanProject::class, 'pemantauan_id', 'id')->withDefault();
    }

    public function perolehanProject()
    {
        return $this->belongsTo(\App\Models\Perunding\PemantauanPerolehan::class, 'perolehan_id', 'id')->withDefault();
    }

    public function deliverables()
    {
        return $this->belongsTo(\App\Models\RefDeliverable::class, 'deliverable', 'code')->withDefault();
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