<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lookup extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'name',
        'json_value',
        'catatan',
        'description',
        'dikemaskini_oleh',
        'row_status',
    ];

    public function users()
    {        
        return $this->belongsTo(\App\Models\User::class, 'dikemaskini_oleh', 'id')->withDefault();
    }
}
