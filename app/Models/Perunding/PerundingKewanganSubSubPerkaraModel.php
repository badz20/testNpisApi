<?php

namespace App\Models\Perunding;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PerundingKewanganSubSubPerkaraModel extends Model
{
    use HasFactory;

    protected $table = 'perunding_kewangan_sub_sub_perkara';

    protected $fillable = [
            'id',
            'pemantauan_id',
            'perkara_id',
            'perolehan',
            'no_bayaran',
            'sub_perkara_id',
            'sub_sub_perkara',
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
