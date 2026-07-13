<?php

namespace App\Models;

use App\Models\Catalog\LabMatrix;
use App\Models\Parameter;
use App\Models\Samples\Sample;
use App\Models\Samples\Threshold;
use App\Models\User;
use App\Traits\ResolvesNumericExpression;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Builder;

class Analysis extends Model
{
    use ResolvesNumericExpression;

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
        'analyzed_by'
    ];

    protected $casts = [
        'registered' => 'boolean',
        'params' => 'json',
        'authorized' => 'boolean',
        'cancelled' => 'boolean',
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

    public function batch(): BelongsTo
    {
        return $this->belongsTo(Batch::class);
    }

    public function labMatrix(): BelongsTo
    {
        return $this->belongsTo(LabMatrix::class);
    }

    public function analyzedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'analyzed_by');
    }

    protected function smallestMaxThreshold(): Attribute
    {
        return Attribute::make(
            get: function() {
                return $this->thresholds
                    ->sortBy(fn($thres) => $this->sortKey($thres->max))
                    ->first()
                    ?->max;
            }
        );
    }

    private function sortKey(string $value): array
    {
        $value = trim($value);

        if ($value === 'N.A.') {
            return [2, 0];
        }

        if ($value === 'N.E.') {
            return [3, 0];
        }

        $step = .001;
        [$operator, $number] = $this->resolveNumericExpession($value, $step);

        return match($operator) {
            '<' => [1, $number - $step],
            default => [1, $number]
        };
    }

    #[Scope]
    protected function from(Builder $query, string $date)
    {
        $query->whereHas('sample', function(Builder $subQuery) use ($date) {
            $subQuery->whereDate('reception_date', '>=', $date);
        });
    }

    #[Scope]
    protected function until(Builder $query, string $date)
    {
        $query->whereHas('sample', function(Builder $subQuery) use ($date) {
            $subQuery->whereDate('reception_date', '<=', $date . ' 23:59:59');
        });
    }
}
