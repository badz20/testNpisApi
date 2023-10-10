<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KewanganKomponen extends Model
{
    use HasFactory;

    protected $table = 'REF_Komponen';

    protected $fillable = [
        'id',
        'kod',
        'nama_komponen',
        'dibuat_oleh',
        'dibuat_pada',
        'dikemaskini_oleh',
        'dikemaskini_pada',
        'created_at',
        'updated_at',
    ];

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'dikemaskini_oleh', 'id')->withDefault();
    }
}
