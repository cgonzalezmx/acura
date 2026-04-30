<?php

namespace App\Http\Resources\Samples;

use App\Http\Resources\Quotes\EntryRefResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SampleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $entry = new EntryRefResource($this->whenLoaded('entry'));
        return [
            'id' => $this->id,
            'identifier' => $this->whenNotNull($this->identifier),
            'entry' => $entry,
        ];
    }
}
