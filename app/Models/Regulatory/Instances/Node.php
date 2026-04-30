<?php

namespace App\Models\Regulatory\Instances;

use App\Models\Regulatory\Structure\Regulation;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class Node extends Model
{
    protected $table = 'regulation_instance_tree';

    protected $fillable = [
        'name',
        'alias',
        'type',
        'parent_id',
        'regulation_id'
    ];

    public function children(): HasMany
    {
        return $this->hasMany(Node::class, 'parent_id');
    }

    public function thresholds(): HasMany
    {
        return $this->hasMany(Threshold::class, 'regulation_instance_id');
    }

    public function regulation(): BelongsTo
    {
        return $this->belongsTo(Regulation::class);
    }

    public function parentNode(): BelongsTo
    {
        return $this->belongsTo(Node::class, 'parent_id');
    }

    protected function leaf(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->loadCount('children')->children_count === 0 ? true : false
        );
    }
}
