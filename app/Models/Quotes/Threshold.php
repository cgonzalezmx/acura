<?php

namespace App\Models\Quotes;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Threshold extends Model
{
    use SoftDeletes;

    public $timestamps = false;
    protected $table = 'report_thresholds';
    protected $fillable = [
        'min',
        'max',
        'custom_boundary',
        'report_id',
        'parameter_id'
    ];
}
