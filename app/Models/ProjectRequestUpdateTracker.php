<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectRequestUpdateTracker extends Model
{
    use HasFactory;

    protected $table = 'project_request_update_tracker';
    protected $fillable = [
        'id',
        'project_id',
        'requested_by',
        'requested_on',
        'catatan',
    ];
}
