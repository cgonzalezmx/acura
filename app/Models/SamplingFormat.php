<?php

namespace App\Models;

use App\Models\Quotes\Entry;
use App\Models\Quotes\Quote;
use App\Models\Traits\Blamable;
use App\Models\Traits\FilterableByDate;
use App\Models\Traits\HasYearlySequence;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SamplingFormat extends Model
{
    use Blamable, HasYearlySequence, FilterableByDate;

    protected $fillable = [
        'identifier',
        'path',
        'sequence_index',
        'year',
    ];

    public function quote(): BelongsTo
    {
        return $this->belongsTo(Quote::class);
    }

    public function entry(): BelongsTo
    {
        return $this->belongsTo(Entry::class);
    }

    protected function matrix(): Attribute
    {
        return Attribute::make(fn() => $this->entry->matrix);
    }
}
