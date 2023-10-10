<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PejabatProjek extends Model
{
    use HasFactory;

    protected $table = 'pejabat_projek';

    protected $fillable = [        
        'id',
        'pajabat_projek',
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
