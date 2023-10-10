<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class PSDA_model extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = 'pentadbir__selenggara__dashboard__analisis';
    public function Module()
    {
        return $this->belongsTo(\App\Models\Pentadbir_modules::class, 'modul_id', 'id')->withDefault();
    }
    public function Modulelist(){
        return $this->belongsTo(\App\Models\Pentadbir_modules::class,'modul_id', 'id');
    }
}
