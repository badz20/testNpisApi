<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class noc_projectModel extends Model  implements HasMedia
{
    use HasFactory, InteractsWithMedia;
    protected $table = 'noc_project';
    protected $fillable = [        
        'pp_id',
        'kod_projek',
        'kos_projek',
        'skop',
        'keterangan',
        'komponen',
        'nama_projek',
        'objektif',
        'no_rujukan',
        'dibuat_oleh',
        'dibuat_pada',
        'dikemaskini_oleh',
        'dikemaskini_pada',
        'is_hidden',
        'row_status',
        'status_id',
        'justifikasi'
    ];

    public function projects()
    {
        return $this->belongsTo(\App\Models\PemantauanProject::class, 'pp_id', 'id')->withDefault();
    }

    public function statuses()
    {
        return $this->belongsTo(\App\Models\Status::class, 'status_id', 'status')->withDefault();
    }
}
