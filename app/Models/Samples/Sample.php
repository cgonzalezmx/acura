<?php

namespace App\Models\Samples;

use App\Models\Analysis;
use App\Models\Quotes\Entry;
use App\Models\Quotes\Quote;
use App\Models\Quotes\Report;
use App\Models\SamplingFormat;
use App\Models\Traits\Blamable;
use App\Models\Traits\LocalTimezone;
use App\Models\User;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;

class Sample extends Model
{
    use Blamable, LocalTimezone;

    protected $fillable = [
        'sample_temperature',
        'total_containers',
        'refrigerator',
        'sampling_format_id',
        'sampling_point',
        'reception_date',
        'sampled_by',
        'observation',
        'entry_id',
    ];

    protected $casts = [
        'reception_date' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::created(function(Sample $sample) {
            $sequence = str_pad($sample->id, 6, "0", STR_PAD_LEFT);
            $sample->identifier = "$sequence-{$sample->matrix->code}";
            $sample->save();
        });
    }

    public function samplingFormat(): BelongsTo
    {
        return $this->belongsTo(SamplingFormat::class);
    }

    public function takes(): HasMany
    {
        return $this->hasMany(Take::class);
    }

    public function analyses(): HasMany
    {
        return $this->hasMany(Analysis::class);
    }

    public function entry(): HasOneThrough
    {
        return $this->hasOneThrough(
            Entry::class,
            SamplingFormat::class,
            'id',
            'id',
            'sampling_format_id',
            'entry_id',
        );
    }

    public function quote(): HasOneThrough
    {
        return $this->hasOneThrough(
            Quote::class,
            SamplingFormat::class,
            'id',
            'id',
            'sampling_format_id',
            'quote_id'
        );
    }

    public function reports(): HasManyThrough
    {
        return $this->hasManyThrough(Report::class, Entry::class, 'id', 'entry_id', 'entry_id');
    }

    public function sampler(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sampled_by', 'id');
    }

    public function thresholds(): HasMany
    {
        return $this->hasMany(Threshold::class);
    }

    protected function matrix(): Attribute
    {
        return Attribute::make(fn() => $this->samplingFormat->matrix);
    }

    protected function client(): Attribute
    {
        return Attribute::make(function() {
            return $this->quote->client;
        });
    }

    protected function isUrgent(): Attribute
    {
        return Attribute::make(fn() => $this->entry()->first()->is_urgent);
    }
}
