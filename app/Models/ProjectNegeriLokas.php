<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class ProjectNegeriLokas extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;
    protected $table = 'project_negeri_lokas';

    protected $fillable = [
        'negeri_id',
        'daerah_id',
        'mukim_id',
        'parlimen_id',
        'dun_id',
        'permohonan_Projek_id',
        'koordinat_latitude',
        'koordinat_longitude',
        'row_status',
        'dibuat_oleh',
        'dibuat_pada',
        'dikemaskini_oleh',
        'dikemaskini_pada',
    ];


    public function project()
    {        
        return $this->belongsTo(\App\Models\Project::class, 'permohonan_Projek_id', 'id')->withDefault();
    }

    public function negeri()
    {        
        return $this->belongsTo(\App\Models\refNegeri::class, 'negeri_id', 'id')->withDefault();
    }

    public function daerah()
    {
        return $this->belongsTo(\App\Models\refDaerah::class, 'daerah_id', 'id')->withDefault();
    }

    public function parlimen()
    {
        return $this->belongsTo(\App\Models\refParlimen::class, 'parlimen_id', 'id')->withDefault();
    }

    public function dun()
    {
        return $this->belongsTo(\App\Models\refDun::class, 'dun_id', 'id')->withDefault();
    }
    

    

}
