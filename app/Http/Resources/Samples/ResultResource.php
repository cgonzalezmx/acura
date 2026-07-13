<?php

namespace App\Http\Resources\Samples;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Collection;

class ResultResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $this->whenAppended('client', fn() => $this->client);
        $analyses = $this->whenLoaded('analyses', function(Collection $analyses) {
            return $analyses->mapWithKeys(fn($analysis) => [$analysis->parameter_id => $analysis]);
        });

        return [
            'reports_count' => $this->whenCounted('reports'),
            'analyses' => $analyses,
            'takes' => $this->whenLoaded('takes'),
            'quote' => $this->whenLoaded('quote'),
            'reception_date' => $this->reception_date,
            'identifier' => $this->identifier,
            'sampling_point' => $this->sampling_point,
            'sampled_by' => $this->sampled_by,
            'thresholds' => $this->thresholds,
        ];
    }
}
