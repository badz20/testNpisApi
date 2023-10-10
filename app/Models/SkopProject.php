<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SkopProject extends Model
{
    use HasFactory;

    protected $table = 'skop_projects';

    protected $guarded = [
        'id',
    ];

    public function project()
    {
        return $this->belongsTo(\App\Models\Project::class, 'project_id', 'id')->withDefault();
    }

    public function subskopProjects()
    {        
        return $this->hasMany(\App\Models\KewanganSkop::class,'skop_id');
    }

    public function skopOptions()
    {
        return $this->belongsTo(\App\Models\SkopOption::class, 'skop_project_code', 'id')->withDefault();
    }
}
