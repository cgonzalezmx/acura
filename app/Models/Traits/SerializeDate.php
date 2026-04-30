<?php

namespace App\Models\Traits;

use DateTimeInterface;

trait SerializeDate {
    protected function serializeDate(DateTimeInterface $datetime): string
    {
        return $datetime->timeZone('America/Mexico_City')->format('d/m/Y H:i');
    }
}
