<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OutputPage extends Model
{
    use HasFactory;
    public $timestamps = false;

    protected $table = 'Projek_Output';

    protected $fillable = [        
        'id',
        'Permohonan_Projek_id',
        'output_proj',
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
