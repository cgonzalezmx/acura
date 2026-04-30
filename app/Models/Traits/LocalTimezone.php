<?php

namespace App\Models\Traits;

use Carbon\Carbon;
use DateTimeInterface;

trait LocalTimezone
{
    protected function serializeDate(DateTimeInterface $dateTime)
    {
        return new Carbon($dateTime)->timezone('America/Mexico_City');
    }
}
