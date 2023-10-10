<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RmkObb extends Model
{
    use HasFactory;

    // protected $table = 'REF_RMK_OBB_Aktivity';

    // protected $fillable = [        
    //     'id',
    //     'nama_aktivity',
    //     'kod_aktivity',
    //     'dibuat_oleh',
    //     'dibuat_pada',
    //     'dikemskini_oleh',
    //     'dikemskini_pada',
    //     'row_status'

        
    // ];
    protected $table = 'ref_rkm_obb_aktivity';
    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'dibuat_oleh', 'id')->withDefault();
    }


}
