<?php

namespace App\Models\Parameters;

use App\Models\Catalog\SampleContainer;
use App\Models\Catalog\SamplePreserver;
use App\Models\Parameter;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Group extends Model
{
    protected $table = 'parameter_groups';
    protected $fillable = [
        'name',
        'order',
        'description',
        'required_sample_volume',
        'sample_container_id',
        'sample_preserver_id',
        'label_color_id'
    ];

    public function parameters(): HasMany
    {
        return $this->hasMany(Parameter::class, 'parameter_group_id');
    }

    public function container(): BelongsTo
    {
        return $this->belongsTo(SampleContainer::class, 'sample_container_id');
    }

    public function preserver(): BelongsTo
    {
        return $this->belongsTo(SamplePreserver::class, 'sample_preserver_id');
    }
}
