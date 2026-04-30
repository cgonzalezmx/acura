<?php

namespace App\Http\Resources\Parameters;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class QuoteResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'parameter_id' => $this->id,
            'name' => $this->name,
            'methodology' => $this->methodology->name,
            'area_id' => $this->analysisArea->id,
            'price' => $this->price,
            'version' => $this->version,
            'multiple' => $this->multiple
        ];
    }
}
