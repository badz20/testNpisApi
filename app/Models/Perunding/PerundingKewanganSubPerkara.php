<?php

namespace App\Models\perunding;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PerundingKewanganSubPerkara extends Model
{
    use HasFactory;

    protected $table = 'perunding_kewangan_sub_perkara';

    protected $fillable = [
            'id',
            'pemantauan_id',
            'perkara_id',
            'no_bayaran',
            'perolehan',
            'sub_perkara',
            'unit',
            'kelulusan_quantity',
            'kelulusan_kadar',
            'kelulusan_jumlah',
            'terdah_quantity',
            'terdah_jumlah',
            'semasa_quantity',
            'semasa_jumlah',
            'kumulatif_quantity',
            'kumulatif_jumlah',
            'baki',
            'dibuat_oleh',
            'dibuat_pada',
            'dikemaskini_oleh',
            'dikemaskini_pada',
            'row_status',
    ];

    public function perkara()
    {        
        return $this->belongsTo(\App\Models\Perunding\PerundingKewanganPerkara::class, 'perkara_id', 'id')->withDefault();
    }
    

}
