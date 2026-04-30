<?php

namespace App\Http\Resources\Samples;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReportIndexResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $tz = fn($datetime) => $datetime->timeZone('America/Mexico_City')->format('d/m/Y H:i');
        return [
            'id' => $this->id,
            'identifier' => $this->identifier,
            'analyses_count' => $this->whenCounted('analyses_count'),
            'takes_count' => $this->whenCounted('takes_count'),
            'sampled_at' => $this->whenLoaded('takes', fn() => $tz($this->takes->first()->timestamp)),
            'sampled_by' => $this->whenNotNull($this->sampler->name, 'Cliente'),
            'reception_date' => $tz($this->reception_date),
            'notes' => $this->whenLoaded('quote', fn() => $this->quote->notes),
        ];
    }
}
