<?php

namespace App\Http\Resources\Regulatory\Structure;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BundleResource extends JsonResource
{
    public static $wrap = null;

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'regulation' => new RegulationResource($this->whenLoaded('regulation')),
            'price' => $this->price,
            'takes' => $this->takes,
            'parameters' => $this->whenLoaded('parameters', fn() => $this->parameters
                ->map(fn($param) => ['id' => $param->id]))
        ];
    }
}
