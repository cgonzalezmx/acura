<?php

namespace App\Models\Regulatory\Structure;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Node extends Model
{
    protected $table = 'regulation_tree';
    protected $fillable = [
        'name',
        'alias',
        'type',
        'parent_id',
        'nodable_id',
        'nodable_type',
    ];

    public function children(): HasMany
    {
        return $this->hasMany(Node::class, 'parent_id');
    }

    public function nodable(): MorphTo
    {
        return $this->morphTo();
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
