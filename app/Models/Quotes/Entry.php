<?php

namespace App\Models\Quotes;

use App\Models\Catalog\LabMatrix;
use App\Models\SamplingFormat;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Entry extends Model
{
    use SoftDeletes;

    public $timestamps = false;
    protected $table = 'quote_entries';
    protected $fillable = [
        'entry_id',
        'title',
        'is_urgent',
        'form_factor',
        'objective',
        'concept',
        'matrix_id',
        'bundle_price',
        'price_offset',
        'price_offset_notes',
        'extras',
        'takes',
        'result_time_lapse',
        'sample_type',
        'sample_reception_date',
        'sampling_date',
        'sample_temperature',
        'sample_container_type',
        'total_containers',
        'total_volume',
        'refrigerated',
        'observation',
        'quote_id',
        'quantity',
        'index',
    ];
    protected $casts = [
        'is_urgent' => 'boolean',
        'refrigerated' => 'boolean',
        'sample_reception_date' => 'datetime'
    ];

    protected static function booted()
    {
        static::deleting(function(Entry $entry) {
            $entry->parameters()->each(function(Parameter $parameter) {
                $parameter->delete();
            });

            $entry->reports()->each(function (Report $report) {
                $report->delete();
            });
        });
    }

    public function parameters(): HasMany
    {
        return $this->hasMany(Parameter::class, 'quote_entry_id');
    }

    public function reports(): HasMany
    {
        return $this->hasMany(Report::class, 'entry_id');
    }

    public function matrix(): HasOne
    {
        return $this->hasOne(LabMatrix::class, 'id', 'matrix_id');
    }

    public function quote(): BelongsTo
    {
        return $this->belongsTo(Quote::class);
    }

    public function samplingFormat(): HasOne
    {
        return $this->hasOne(SamplingFormat::class, 'entry_id');
    }

    protected function index(): Attribute
    {
        return Attribute::make(
            get: function() {
                return DB::table('quote_entries')
                    ->select(DB::raw('count(id) as `index`'))
                    ->where('quote_id', $this->quote_id)
                    ->where('entry_id', '<=', $this->entry_id)
                    ->first()
                    ->index;
        });
    }
}
