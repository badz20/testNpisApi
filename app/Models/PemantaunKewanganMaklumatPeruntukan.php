<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PemantaunKewanganMaklumatPeruntukan extends Model
{
    use HasFactory;

    protected $table = 'pemantaun_kewangan_maklumat_peruntukan';

    protected $fillable = [        
        'id',
        'pp_id',
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
