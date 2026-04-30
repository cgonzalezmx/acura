<?php

namespace App\Http\Resources\Regulatory\Instances;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class QueryThresholdResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'parameter_id' => $this->parameter_id,
            'min' => $this->min,
            'max' => $this->max,
            'source' => 'system'
        ];
    }
}
