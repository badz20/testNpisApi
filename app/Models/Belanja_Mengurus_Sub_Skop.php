<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Belanja_Mengurus_Sub_Skop extends Model
{
    use HasFactory;

    protected $table = 'belanga_menguru_sub_skop';

    protected $fillable = [
        'belanga_skop_id',
        'nama_sub_skop',
        'dibuat_oleh',
        'dibuat_pada',
        'dikemaskini_oleh',
        'dikemaskini_pada',
        'row_status',
        'is_hidden',
    ];

    public function updatedBy()
    {        
        return $this->belongsTo(\App\Models\User::class, 'dikemaskini_oleh', 'id')->withDefault();
    }

    public function skop()
    {        
        return $this->belongsTo(\App\Models\Belanja_Mengurus_Skop::class, 'belanga_skop_id', 'id')->withDefault();
    }
}
