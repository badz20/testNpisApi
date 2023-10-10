<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KewanganSkop extends Model
{
    use HasFactory;

    protected $table = 'Projek_Kewangan_Skop_Komponen';

    protected $fillable = [        
        'id',
        'permohonan_projek_id',
        'skop_id',
        'skop_project_code',
        'lain_lain',
        'sub_skop_project_code',
        'nama_componen',
        'jumlahkos',
        'is_parent',
        'Kuantiti',
        'units',
        'Kos',
        'Catatan',
        'dibuat_oleh',
        'dibuat_pada',
        'dikemskini_oleh',
        'dikemskini_pada',
        'row_status'
    ];

    public function subSkopOptions()
    {        
        return $this->belongsTo(\App\Models\SubSkopOption::class, 'sub_skop_project_code', 'sub_skop_code')->withDefault();
    }

    public function skop()
    {        
        return $this->belongsTo(\App\Models\SkopProject::class, 'skop_id', 'id')->withDefault();
    }

    public function project()
    {
        return $this->belongsTo(\App\Models\Project::class, 'permohonan_projek_id', 'id')->withDefault();
    }

    public function subsubskopProjects()
    {        
        return $this->hasMany(\App\Models\KewanganSubSkop::class,'sub_skop_id');
    }
}
