<?php

namespace App\Models\Samples;

use App\Models\Traits\LocalTimezone;
use Illuminate\Database\Eloquent\Model;

class Take extends Model
{
    use LocalTimezone;

    protected $fillable = [
        'timestamp',
        'color',
        'odour',
        'appearance',
        'sample_id',
        'sequence'
    ];

    protected $casts = [
        'timestamp' => 'datetime',
    ];
}
