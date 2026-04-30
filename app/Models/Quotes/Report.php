<?php

namespace App\Models\Quotes;

use Illuminate\Database\Eloquent\Casts\AsArrayObject;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Report extends Model
{
    use SoftDeletes;

    public $timestamps = false;
    protected $table = 'quote_entry_reports';
    protected $fillable = [
        'report_id',
        'structure_expanded_keys',
        'structure_selected_keys',
        'instance_expanded_keys',
        'instance_selected_keys',
        'is_main_report',
        'observation'
    ];

    protected $casts = [
        'structure_expanded_keys' => AsArrayObject::class,
        'structure_selected_keys' => AsArrayObject::class,
        'instance_expanded_keys' => AsArrayObject::class,
        'instance_selected_keys' => AsArrayObject::class
    ];

    protected static function booted()
    {
        static::deleting(function(Report $report) {
            $report->thresholds()->each(function(Threshold $threshold) {
                $threshold->delete();
            });
        });
    }

    public function thresholds(): HasMany
    {
        return $this->hasMany(Threshold::class);
    }

    public function entry(): BelongsTo
    {
        return $this->belongsTo(Entry::class);
    }
}
