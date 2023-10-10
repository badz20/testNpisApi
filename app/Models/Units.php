<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Units extends Model
{
    use HasFactory;

    protected $table = 'REF_Unit';

    protected $fillable = [        
        'id',
        'nama_unit',
        'dibuat_oleh',
        'dibuat_pada',
        'dikemaskini_oleh',
        'dikemaskini_pada',
        'IsActive',
        'row_status'       
    ];

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'dikemaskini_oleh', 'id')->withDefault();
    }
}
