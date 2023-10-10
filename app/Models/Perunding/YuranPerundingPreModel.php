<?php

namespace App\Models\Perunding;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class YuranPerundingPreModel extends Model
{
    use HasFactory;

    protected $table = 'yuran_perunding_pre';

    protected $fillable = [
        'id',
        'pemantauan_id',
        'perolehan',
        'no_bayaran',
        'perjanjian', 
        'bayaran_terdhulu', 
        'tututan_terkini', 
        'kumulatif', 
        'cukai_tamba', 
        'dibuat_oleh',
        'dibuat_pada',
        'dikemaskini_oleh',
        'dikemaskini_pada',
        'row_status',
    ];
}
