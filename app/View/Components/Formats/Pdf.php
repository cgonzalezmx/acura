<?php

namespace App\View\Components\Formats;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Pdf extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(
        public string $title,
        public string $formatVersion
    ){}

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.formats.pdf');
    }
}
