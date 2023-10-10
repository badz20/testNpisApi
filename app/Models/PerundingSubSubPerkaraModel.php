<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PerundingSubSubPerkaraModel extends Model
{
    use HasFactory;

    protected $table = 'sub_sub_perkera_terdhahulu';

    protected $fillable = [
        'id',
        'pemantauan_id',
        'perkara_id',
        'perolehan',
        'sub_perkara_id',
        'terdah_quantity',
        'terdah_jumlah',
        'kumulatif_quantity',
        'kumulatif_jumlah',
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
