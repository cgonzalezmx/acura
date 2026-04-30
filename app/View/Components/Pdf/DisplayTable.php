<?php

namespace App\View\Components\Pdf;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class DisplayTable extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(
        public ?string $title,
        public array $rows
    )
    {}

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.pdf.display-table');
    }
}
