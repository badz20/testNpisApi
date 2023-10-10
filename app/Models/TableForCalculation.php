<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TableForCalculation extends Model
{
    use HasFactory;

    protected $table = 'skop_kos_calculation';

    protected $fillable = [        
        'id',
        'total_cost',
        'P_min',
        'P_max'       
    ];

    public $timestamps = false;

}
