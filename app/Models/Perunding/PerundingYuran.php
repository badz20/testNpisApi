<?php

namespace App\Models\Perunding;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PerundingYuran extends Model
{
    use HasFactory;

    protected $table = 'yuran_perunding';

    protected $fillable = [
        'id',
        'pemantauan_id',
        'perolehan',
        'no_bayaran',
        'perjanjian_text', 
        'perjanjian', 
        'bayaran_terdhulu', 
        'tututan_terkini', 
        'kumulatif', 
        'cukai_tamba', 
        'dibuat_oleh',
        'dibuat_pada',
        'dikemaskini_oleh',
        'dikemaskini_pada',
        'row_status',
    ];

    public function suppliment()
    {        
        return $this->hasMany(\App\Models\Perunding\PerundingYuranSupplimetaries::class,'yuran_id')->orderBy('id','asc');
    }
}
