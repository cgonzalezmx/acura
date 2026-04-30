<?php

namespace App\Models\Regulatory\Instances;

use App\Models\Parameter;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Threshold extends Model
{
    protected $table = 'regulatory_thresholds';

    protected $fillable = [
        'min',
        'max',
        'parameter_id',
        'regulation_id',
        'regulation_instance_id'
    ];

    public function parameter(): BelongsTo
    {
        return $this->belongsTo(Parameter::class);
    }
}
