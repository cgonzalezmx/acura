<?php

namespace App\Models\Samples;

use Illuminate\Database\Eloquent\Model;

class Threshold extends Model
{
    public $timestamps = false;
    protected $table = 'sample_thresholds';
    protected $fillable = [
        'min',
        'min_numeric_value',
        'max',
        'max_numeric_value',
        'letter',
        'parameter_id',
    ];
}
