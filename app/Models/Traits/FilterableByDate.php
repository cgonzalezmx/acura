<?php

namespace App\Models\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Attributes\Scope;

trait FilterableByDate {
    #[Scope]
    protected function from(Builder $query, string $date)
    {
        $query->whereDate('created_at', '>=', $date);
    }

    #[Scope]
    protected function until(Builder $query, string $date)
    {
        $query->whereDate('created_at', '<=', $date);
    }
}
