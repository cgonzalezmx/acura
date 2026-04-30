<?php

namespace App\View\Components\SamplingFormat;

use App\Models\Quotes\Client;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Reference extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(
        public string $header,
        public array $rows
    )
    {
        //
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.sampling-format.reference');
    }
}
