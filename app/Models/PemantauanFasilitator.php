<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PemantauanFasilitator extends Model
{
    use HasFactory;

    protected $table = 'pemantauan_fasilitator';

    public function bahagian()
    {        
        return $this->belongsTo(\App\Models\refBahagian::class, 'bahagian_id', 'id')->withDefault();
    }

    public function gredJawatan()
    {        
        return $this->belongsTo(\App\Models\refGredJawatan::class, 'gred_id', 'id')->withDefault();
    }

    public function jabatan()
    {        
        return $this->belongsTo(\App\Models\refJabatan::class, 'jabatan_id', 'id')->withDefault();
    }

    public function jawatan()
    {
        return $this->belongsTo(\App\Models\refJawatan::class, 'jawatan_id', 'id')->withDefault();        
    }

    public function fasilitator()
    {        
        return $this->hasMany(\App\Models\VM\VmButiranFasilitator::class, 'fasilitator_id')->orderBy('id','desc');
    }

    public function newfasilitator()
    {        
        return $this->hasMany(\App\Models\VM\VmButiranFasilitator::class, 'fasilitator_id')->orderBy('id','desc');
    }

}
