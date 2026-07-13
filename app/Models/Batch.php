<?php

namespace App\Models;

use App\Models\Catalog\AnalysisArea;
use App\Models\Catalog\SampleStorage;
use App\Models\Samples\Sample;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Batch extends Model
{
    public $fillable = [
        'name',
        'parameter',
        'checkin_time',
        'checkout_time',
        'log',
        'solutions_log',
        'range',
        'authorized',
        'authorized_at',
        'authorized_by',
        'minimal_quantification',
        'analysis_area_id',
        'sample_storage_id',
        'matrix',
        'refrigerators',
        'params',
        'controls',
        'analyzed_at',
    ];

    protected $casts = [
        'params' => 'json',
        'controls' => 'json',
    ];

    public function analyses(): HasMany
    {
        return $this->hasMany(Analysis::class);
    }

    public function analysisArea(): BelongsTo
    {
        return $this->belongsTo(AnalysisArea::class);
    }

    public function sampleStorages(): BelongsToMany
    {
        return $this->belongsToMany(SampleStorage::class);
    }

    public function sample(): BelongsTo
    {
        return $this->belongsTo(Sample::class);
    }
}
