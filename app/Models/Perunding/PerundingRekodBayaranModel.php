<?php

namespace App\Models\Perunding;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PerundingRekodBayaranModel extends Model
{
    use HasFactory;

    protected $table = 'perunding_rekod_bayran';

    protected $fillable = [
        'id',
        'no_bayaran',
        'pemantauan_id',
        'perolehan',
        'yuran_perunding',
        'inbuhan_balik',
        'lad_value',
        'penjanjian_asal',
        'cukai_perkhidmatan',
        'jumlah_bayaran',
        'tarik_baucer',
        'no_baucer',
        'row_status',
    ];
}
