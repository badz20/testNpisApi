<?php

namespace App\Models\Perunding;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PerundingKewanganHistoryModel extends Model
{
    use HasFactory;

    protected $table = 'perunding_kewangan_history';

    protected $fillable = [
        'id',
        'tindakan',
        'pemantauan_id',
        'perolehan',
        'no_bayaran',
        'tarikh',
        'bahagian_id',
        'nama',
        'bahagian_kod',
        'dibuat_oleh',
        'dibuat_pada',
        'dikemaskini_oleh',
        'dikemaskini_pada',
        'row_status',
    ];

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'dibuat_oleh', 'id')->withDefault();
    }
}
