<?php

namespace App\Models\Perunding;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PerundingYuranSupplimetaries extends Model
{
    use HasFactory;

    protected $table = 'yuran_perunding_suppliemtaries';

    protected $fillable = [
        'id',
        'yuran_id',
        'pemantauan_id',
        'perolehan',
        'no_bayaran',
        'supply_value',
        'dibuat_oleh',
        'dibuat_pada',
        'dikemaskini_oleh',
        'dikemaskini_pada',
        'row_status'
    ];

    public function yuran()
    {        
        return $this->belongsTo(\App\Models\Perunding\PerundingYuran::class, 'yuran_id', 'id')->withDefault();
    }
}
