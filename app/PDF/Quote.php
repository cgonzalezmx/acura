<?php

namespace App\PDF;

use Illuminate\Contracts\Support\Renderable;
use WeasyPrint\Objects\Source;
use WeasyPrint\PDF;

class Quote extends PDF
{
    public function __construct(
        private readonly array $props
    ){}

    public function source(): Source|Renderable|string
    {
        return view('quote', $this->props);
    }

    public function filename(): string
    {
        return "cotizacion-{$this->props['quote']->identifier}.pdf";
    }
}