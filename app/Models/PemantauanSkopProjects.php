<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PemantauanSkopProjects extends Model
{
    use HasFactory;

    protected $table = 'pemantauan_skop_projects';

    public function pemantauansubskopProjects()
    {        
        return $this->hasMany(\App\Models\PemantauanKewanganSkop::class,'skop_id');
    }

    public function pemantauanskopOptions()
    {
        return $this->belongsTo(\App\Models\SkopOption::class, 'skop_project_code', 'id')->withDefault();
    }

}
