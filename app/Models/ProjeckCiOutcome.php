<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Base as Model;

class ProjeckCiOutcome extends Model
{
    protected $table = 'project_ci_outcome';

    public function project()
    {
        return $this->belongsTo(\App\Models\Project::class, 'project_id', 'id')->withDefault();
    }
}
