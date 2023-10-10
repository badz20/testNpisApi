<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KewanganSubSkop extends Model
{
    use HasFactory;

    protected $table = 'Projek_Kewangan_Sub_Skop_Komponen';

    protected $fillable = [        
        'id',
        'permohonan_projek_id',
        'skop_id',
        'sub_skop_id',
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
        'row_status',
        'created_at',
        'updated_at',
    ];


    public function skop()
    {        
        return $this->belongsTo(\App\Models\SkopProject::class, 'skop_id', 'id')->withDefault();
    }

    public function subskop()
    {        
        return $this->belongsTo(\App\Models\KewanganSkop::class, 'sub_skop_id', 'id')->withDefault();
    }

    
}
