<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterPeranan extends Model
{
    use HasFactory;

    protected $table = 'master_peranan';
    
        protected $fillable = [        
            'id',
            'nama_peranan',
            'peranan_desc',
            'penyedia',
            'penyemak_1',
            'penyemak_2',
            'pengesah',
            'dibuat_oleh',
            'dibuat_pada',
            'dikemskini_oleh',
            'dikemskini_pada',
            'row_status'
        ];
}
