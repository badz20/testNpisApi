<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PemantauanKewanganSkop extends Model
{
    use HasFactory;

    protected $table = 'pemantauan_kewangan_skop_komponen';

    protected $fillable = [        
        'id',
        'pp_id',
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
        return $this->belongsTo(\App\Models\PemantauanSkopProjects::class, 'skop_id', 'id')->withDefault();
    }

    public function project()
    {
        return $this->belongsTo(\App\Models\PemantauanProject::class, 'pp_id', 'id')->withDefault();
    }
}
