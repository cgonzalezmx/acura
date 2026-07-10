<?php

namespace App\PDF;

use Illuminate\Contracts\Support\Renderable;
use WeasyPrint\Objects\Source;
use App\Support\PDF;

class SamplingFormat extends PDF
{
    public function __construct(
        private readonly array $props
    )
    {}

    public function source(): Source|Renderable|string
    {
        return view('sampling_format', $this->props);
    }

    public function filename(): string
    {
        return $this->props['identifier'] . '.pdf';
    }
}
