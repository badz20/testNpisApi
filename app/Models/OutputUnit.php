<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OutputUnit extends Model
{
    use HasFactory;

    protected $table = 'REF_Unit';

    protected $fillable = [        
        'id',
        'nama_unit',
        'dibuat_oleh',
        'dibuat_pada',
        'dikemskini_oleh',
        'dikemskini_pada',
        'row_status'
    ];

    public function ProjectKpi()
    {
        return $this->hasMany(\App\Models\ProjectKpi::class,'id');

    }
}
