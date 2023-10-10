<?php

namespace App\Models\Perunding;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class YuranPerundingTotalModel extends Model
{
    use HasFactory;

    protected $table = 'yuran_perunding_total';

    protected $fillable = [
        'id',
        'pemantauan_id',
        'perolehan',
        'no_bayaran',
        'supplier_data', 
        'dibuat_oleh',
        'dibuat_pada',
        'dikemaskini_oleh',
        'dikemaskini_pada',
        'row_status',
    ];
}
