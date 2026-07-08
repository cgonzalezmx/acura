<?php

namespace App\Http\Resources\Samples;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ResultResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $client = $this->whenAppended('client', fn() => $this->client);

        return [
            'reports_count' => $this->whenCounted('reports'),
            'analyses' => $this->whenLoaded('analyses'),
            'takes' => $this->whenLoaded('takes'),
            'quote' => $this->whenLoaded('quote'),
            'reception_date' => $this->reception_date,
            'identifier' => $this->identifier,
            'sampling_point' => $this->sampling_point
        ];
    }
}
