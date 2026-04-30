<?php

namespace App\Models\Regulatory\Structure;

use App\Models\Parameter;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class Bundle extends Model
{
    protected $fillable = [
        'name',
        'price',
        'takes',
        'regulation_id'
    ];

    public function regulation(): BelongsTo
    {
        return $this->belongsTo(Regulation::class);
    }

    public function parameters(): BelongsToMany
    {
        return $this->belongsToMany(Parameter::class);
    }

    public function node(): MorphOne
    {
        return $this->morphOne(Node::class, 'nodable');
    }
}
