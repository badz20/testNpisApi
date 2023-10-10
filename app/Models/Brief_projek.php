<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Brief_projek extends Model
{
    use HasFactory;

    protected $table = 'brief_projek';

    protected $fillable = [
        'jenis_permohonan_projek',
        'kategori_projek',
        'rancangan_malaysia_ke',
        'rolling_plan',
        'kod_projek',
        'nama_projek',
        'status_perlaksanaan',
        'jenis_kategori_projek',
        'bahagian',
        'sektor_utama',
        'sektor',
        'skop',
        'objektif',
        'keterangan_projek_komponen',
        'negeri',
        'daerah',
        'dibuat_oleh',
        'dibuat_pada',
        'dikemaskini_oleh',
        'dikemaskini_pada',
        'row_status'
    ];

    public function kos_projek()
    {
        return $this->belongsTo(\App\Models\Maklumat_keewangan::class, 'kod_projek', 'kod_projek')->withDefault();
    }

    public function bahagian()
    {        
        return $this->belongsTo(\App\Models\refBahagian::class, 'bahagian', 'id')->withDefault();
    }

}
