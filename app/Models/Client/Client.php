<?php

namespace App\Models\Client;

use App\Models\Traits\Blamable;
use App\Models\Traits\SerializeDate;
use App\Models\Traits\Versioning;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Client extends Model
{
    use SoftDeletes, Blamable, Versioning, SerializeDate;
    protected $fillable = [];

    public function contacts(): HasMany
    {
        return $this->hasMany(Contact::class);
    }

    protected function address(): Attribute
    {
        return Attribute::make(
            get: function(mixed $values, array $attr) {
                return trim("{$attr['street']} {$attr['external_number']} {$attr['internal_number']}");
            }
        );
    }

    public function samplingSites(): HasMany
    {
        return $this->hasMany(SamplingSite::class);
    }

    protected function active(): Attribute
    {
        return Attribute::make(fn() => $this->detelet_at ? false : true);
    }
}
