<?php

namespace App\Models\perunding;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PerundingKewanganPerkara extends Model
{
    use HasFactory;

    protected $table = 'perunding_kewangan_perkara';

    protected $fillable = [
        'id',
        'pemantauan_id',
        'perkara',
        'perolehan',
        'no_bayaran',
        'dibuat_oleh',
        'dibuat_pada',
        'dikemaskini_oleh',
        'dikemaskini_pada',
        'row_status',
    ];

    public function subperkara()
    {        
        return $this->hasMany(\App\Models\Perunding\PerundingKewanganSubPerkara::class,'perkara_id')->orderBy('id','desc');
    }

    public function subsubperkara()
    {        
        return $this->hasMany(\App\Models\Perunding\PerundingKewanganSubSubPerkaraModel::class,'perkara_id')->orderBy('id','desc');
    }

    public function subterdahulu()
    {        
        return $this->hasMany(\App\Models\PerundingSubPerkaraModel::class,'perkara_id')->orderBy('id','desc');
    }

    public function subsubterdahulu()
    {        
        return $this->hasMany(\App\Models\PerundingSubSubPerkaraModel::class,'perkara_id')->orderBy('id','desc');
    }

}
