<?php

namespace App\Models;

use App\Models\Catalog\LabMatrix;
use App\Models\Parameter;
use App\Models\Quotes\Threshold;
use App\Models\Samples\Sample;
use App\Models\User;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;

class Analysis extends Model
{
    public $timestamps = false;
    protected $fillable = [
        'sequence',
        'result',
        'reported_result',
        'measurement_units',
        'minimal_quantification',
        'method',
        'uncertainty',
        'take_id',
        'params',
        'analyzed_at',
    ];

    protected $casts = [
        'registered' => 'boolean',
        'params' => 'json',
    ];

    public function sample(): BelongsTo
    {
        return $this->belongsTo(Sample::class);
    }

    public function parameter(): BelongsTo
    {
        return $this->belongsTo(Parameter::class);
    }

    public function thresholds(): BelongsToMany
    {
        return $this->belongsToMany(Threshold::class);
    }

    public function reportedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reported_by');
    }

    public function authorizedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'authorized_by');
    }

    protected function isRanged(): Attribute
    {
        return Attribute::make(fn() => isset($this->parameter->uncertainty_low_range));
    }

    public function batches(): BelongsToMany
    {
        return $this->belongsToMany(Batch::class);
    }

    public function labMatrix(): BelongsTo
    {
        return $this->belongsTo(LabMatrix::class);
    }

    #[Scope]
    protected function sampledFrom(Builder $query, string $date)
    {
        $query->whereHas('sample', fn($query) => $query->whereDate('reception_date', '>=', $date));
    }

    #[Scope]
    protected function sampledUntil(Builder $query, string $date)
    {
        $query->whereHas('sample', fn($query) => $query->whereDate('reception_date', '<=', $date));
    }
}
