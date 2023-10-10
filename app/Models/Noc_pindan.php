<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Noc_pindan extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $table = 'noc_pindan';
    protected $fillable = [        
        'pp_id',
        'noc_id',
        'lampiran_pindan_file_name',
        'agensi',
        'maklumat_pindan_date',
        'ringasakan_ulasan',
        'dibuat_oleh',
        'dibuat_pada',
        'dikemaskini_oleh',
        'dikemaskini_pada',
        'row_status'
    ];

    public function agensi()
    {        
        return $this->belongsTo(\App\Models\NamaAgensi::class, 'agensi', 'id')->withDefault();
    }
}
