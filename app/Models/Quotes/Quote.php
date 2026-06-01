<?php

namespace App\Models\Quotes;

use App\Models\Client\Contact;
use App\Models\Client\SamplingSite;
use App\Models\Samples\Sample;
use App\Models\SamplingFormat;
use App\Models\Traits\Blamable;
use App\Models\Traits\FilterableByDate;
use App\Models\Traits\HasYearlySequence;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Quote extends Model
{
    use SoftDeletes, Blamable, HasYearlySequence, FilterableByDate;

    protected $fillable = [
        'year',
        'sequence_index',
        'letter_index',
        'iva',
        'gross_cost',
        'net_cost',
        'subtotal',
        'identifier',
        'objective',
        'notes',
        'sample_delivered_by_client',
        'client_data_as_sampling_site',
        'tree',
        'authorized',
        'original_creator',
        'client_id',
        'contact_client_id',
        'client_sampling_site_id',
        'parent_id',
        'referenced_quote_id',
        'price_adjustment',
        'price_adjustment_percentage',
        'price_adjustment_notes',
        'payment_method',
        'global_expenses_concept',
        'global_expenses_quantity',
        'validity'
    ];

    protected $casts = [
        'tree' => 'array',
        'tree_leaves' => 'array',
        'sample_delivered_by_client' => 'boolean',
        'client_data_as_sampling_site' => 'boolean',
        'authorized' => 'boolean',
    ];

    public function client(): HasOne
    {
        return $this->hasOne(Client::class);
    }

    public function contactInSystem(): BelongsToMany
    {
        return $this->belongsToMany(Contact::class, 'quote_selected_contacts', 'quote_id', 'client_contact_id');
    }

    public function selectedContact(): HasOne
    {
        return $this->hasOne(SelectedContact::class);
    }

    public function samplingSiteInSystem(): BelongsToMany
    {
        return $this->belongsToMany(SamplingSite::class, 'quote_selected_sampling_sites', 'quote_id', 'client_sampling_site_id');
    }

    public function selectedSamplingSite(): HasOne
    {
        return $this->hasOne(SelectedSamplingSite::class);
    }

    public function entries(): HasMany
    {
        return $this->hasMany(Entry::class);
    }

    public function parameters(): HasMany
    {
        return $this->hasMany(Parameter::class);
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function reports(): HasManyThrough
    {
        return $this->hasManyThrough(Report::class, Entry::class);

    }

    public function samples(): HasManyThrough
    {
        return $this->hasManyThrough(Sample::class, SamplingFormat::class);
    }

    public function originalCreator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'original_creator');
    }
}
