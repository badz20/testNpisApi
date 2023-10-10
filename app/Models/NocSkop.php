<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NocSkop extends Model
{
    use HasFactory;

    protected $table = 'noc_skop';

    protected $fillable = [
        'noc_id',
        'skop_id',
        'sub_skop_id',
        'skop_kos',
        'dibuat_oleh',
        'dibuat_pada',
        'dikemaskini_oleh',
        'dikemaskini_pada',
        'row_status',
    ];
}
