<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class mapService extends Model
{
    use HasFactory;
    protected $table = 'pentadbir_map_services'; 
    public function Module()
    {
        return $this->belongsTo(\App\Models\Pentadbir_modules::class, 'modul_id', 'id')->withDefault();
    }
}
