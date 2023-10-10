<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class refAgensi extends Model
{
    use HasFactory;
    protected $table = 'ref_agensi';

    // public function pdCoordinator()
    // {
    //     return $this->belongsTo(\App\Models\User::class, 'agensi_id', 'id')->withDefault();
    // }
}
