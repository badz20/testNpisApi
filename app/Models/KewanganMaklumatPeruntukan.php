<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KewanganMaklumatPeruntukan extends Model
{
    use HasFactory;

    protected $table = 'projek_kewangan_maklumat_peruntukan';

    protected $fillable = [        
        'id',
        'permohonan_projek_id',
        'perkra_id',
        'year1',
        'year2',
        'year3',
        'year4',
        'year5',
        'year6',
        'year7',
        'year8',  
        'year9',
        'year10',
        'jumlah_kos',
        'dibuat_oleh',
        'dibuat_pada',
        'dikemskini_oleh',
        'dikemskini_pada',
        'row_status'
    ];
}
