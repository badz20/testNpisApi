<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class projectLog extends Model
{
    use HasFactory;
    protected $table = 'projek_log';
    protected $fillable = [
        'id',
        'projek_id',
        'user_id',
        'section_name',
        'created_on',
    ];
    
    //public $timestamps = false;

    public function userdata()
    {        
        return $this->belongsTo(\App\Models\User::class, 'user_id', 'id')->withDefault();
    }

    public function projectdata()
    {        
        return $this->belongsTo(\App\Models\Project::class, 'projek_id', 'id')->withDefault();
    }


}
