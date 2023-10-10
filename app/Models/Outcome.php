<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Outcome extends Model
{
    use HasFactory;
    public $timestamps = false;

    protected $table = 'Projek_Outcome';

    protected $fillable = [        
        'id',
        'Permohonan_Projek_id',
        'Projek_Outcome',
        'Kuantiti',
        'unit_id',
        'dibuat_oleh',
        'dibuat_pada',
        'dikemskini_oleh',
        'dikemskini_pada',
        'row_status'
    ];

    public function unit()
    {
        return $this->belongsTo(\App\Models\Units::class, 'unit_id', 'id')->withDefault();
    }
}
