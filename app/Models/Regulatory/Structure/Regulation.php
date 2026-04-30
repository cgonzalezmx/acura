<?php

namespace App\Models\Regulatory\Structure;

use App\Models\Catalog\LabMatrix;
use App\Models\Parameter;
use App\Models\Regulatory\Instances\Node as InstanceNode;
use App\Models\Regulatory\Structure\Node as StructureNode;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class Regulation extends Model
{
    protected $fillable = ['lab_matrix_id'];

    public function labMatrix(): BelongsTo
    {
        return $this->belongsTo(LabMatrix::class);
    }

    public function parameters(): BelongsToMany
    {
        return $this->belongsToMany(Parameter::class, 'regulation_parameter');
    }

    public function instances(): HasMany
    {
        return $this->hasMany(InstanceNode::class)->where('parent_id', null);
    }

    public function node(): MorphOne
    {
        return $this->morphOne(StructureNode::class, 'nodable');
    }

    public function bundles(): HasMany
    {
        return $this->hasMany(Bundle::class);
    }
}
