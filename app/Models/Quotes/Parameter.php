<?php

namespace App\Models\Quotes;

use App\Models\Parameter as SystemParameter;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Parameter extends Model
{
    use SoftDeletes;

    public $timestamps = false;
    protected $table = 'quote_entry_parameters';
    protected $fillable = [
        'quantity',
        'expected_quantity',
        'from_system',
        'from_main_report',
        'quote_id',
        'parameter_id',
        'quote_entry_id'
    ];

    protected $casts = [
        'from_system' => 'boolean',
        'from_main_report' => 'boolean'
    ];

    public function systemInfo(): BelongsTo
    {
        return $this->belongsTo(SystemParameter::class, 'parameter_id', 'id');
    }

    public function entry(): BelongsTo
    {
        return $this->belongsTo(Entry::class, 'quote_entry_id', 'id');
    }
}
