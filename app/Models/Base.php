<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Auditable as AuditableTrait;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Base extends Model implements AuditableContract, HasMedia
{
    use HasFactory;        
    use InteractsWithMedia;
    use AuditableTrait;

    protected $guarded = [
        'id',
    ];
}
