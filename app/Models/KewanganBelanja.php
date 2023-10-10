<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KewanganBelanja extends Model
{
    use HasFactory;

    protected $table = 'projek_kewangan_kategori_belanja';

    protected $fillable = [        
        'id',
        'permohonan_projek_id',
        'kategori_nama',
        'kategori_1_yr',
        'kategori_2_yr',
        'kategori_3_yr',
        'kategori_4_yr',
        'kategori_5_yr',
        'kategori_6_yr',
        'kategori_7_yr',
        'kategori_8_yr',  
        'kategori_9_yr',
        'kategori_10_yr',
        'jumlah_kos',
        'dibuat_oleh',
        'dibuat_pada',
        'dikemskini_oleh',
        'dikemskini_pada',
        'row_status'
    ];
}
