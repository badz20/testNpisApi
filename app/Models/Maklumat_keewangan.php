<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Maklumat_keewangan extends Model
{
    use HasFactory;

    protected $table = 'maklumat_keewangan';

    protected $fillable = [
        'kod_projek',
        'kos_keseluruhan',
        'tahun_kewangan',
        'dijana_oleh_tarikh_jana',
        'dibuat_oleh',
        'dibuat_pada',
        'dikemaskini_oleh',
        'dikemaskini_pada',
        'row_status'
    ];
}
