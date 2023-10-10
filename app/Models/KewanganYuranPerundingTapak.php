<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KewanganYuranPerundingTapak extends Model
{
    use HasFactory;
    protected $table = 'Projek_Kewangan_yuran_perunding_tapak';

    protected $fillable = [        
        'id',
        'permohonan_projek_id',
        'jawatan',
        'man_month',
        'jumlahkos',
        'is_Profesional',
        'multiplier',
        'salary',
        'amount',
        'catatan',
        'dibuat_oleh',
        'dibuat_pada',
        'dikemskini_oleh',
        'dikemskini_pada',
        'row_status'
    ];
}
