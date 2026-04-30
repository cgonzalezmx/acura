<?php

namespace App\Services\Samples;

use App\Models\Samples\Sample;
use App\Services\UserService;
use Illuminate\Support\Collection;
use Inertia\Inertia;

class SampleIndex
{
    public function __construct(
        private UserService $user_service
    )
    {}

    public function view()
    {
        $samplers = $this->user_service->listSamplers();
        return inertia('Samples/Index', [
            'samples' => $this->samples(),
            'samplers' => $samplers,
        ]);
    }

    private function samples(): Collection
    {
        $samples = Sample::with(['takes'])->get();
        $samples->loadCount('takes');
        $samples->append(['client']);

        return $samples;
    }
}
