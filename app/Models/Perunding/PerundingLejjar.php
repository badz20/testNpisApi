<?php

namespace App\Models\Perunding;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PerundingLejjar extends Model
{
    use HasFactory;

    protected $table = 'perunding_lejjar';

    protected $fillable = [
        'id',
        'pemantauan_id',
        'perolehan',
        'no_bayaran',
        'yuran_perunding', 
        'inbuhan_balik', 
        'jps_yuran_perunding', 
        'jps_inbuhan_balik', 
        'dibuat_oleh',
        'dibuat_pada',
        'dikemaskini_oleh',
        'dikemaskini_pada',
        'row_status',
    ];
}
