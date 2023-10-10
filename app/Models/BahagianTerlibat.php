<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Base as Model;

class BahagianTerlibat extends Model
{
    protected $table = 'bahagian_terlibat';

    public function bahagian()
    {
        return $this->belongsTo(\App\Models\refBahagian::class, 'bahagian_id', 'id')->withDefault();
    }

    public function project()
    {
        return $this->belongsTo(\App\Models\Project::class, 'project_id', 'id')->withDefault();
    }
}
