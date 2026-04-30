<?php

namespace App\Models\Client;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class SamplingSite extends Model
{
    protected $table = 'client_sampling_sites';

    protected function address(): Attribute
    {
        return Attribute::make(
            get: function(mixed $_, array $attributes) {
                $street = $attributes['street'];
                $externalNumber = $attributes['external_number'];
                $internal_number = $attributes['internal_number'];

                return trim("$street $internal_number $externalNumber");
            }
        );
    }
}
