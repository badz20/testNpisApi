<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KalendarModel extends Model
{
    use HasFactory;
    protected $table = 'vm_perancagan_makmal';
    protected $fillable = [        
        'id',
        'pp_id',
        'kategori',
        'tarikh_mula',
        'tarikh_tamat',
        'dibuat_oleh',
        'dibuat_pada',
        'dikemskini_oleh',
        'dikemskini_pada',
        'created_at',
        'updated_at',
        'row_status'
    ];
}
