<?php

namespace App\Http\Resources\Regulatory\Instances;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ThresholdResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            $this->parameter_id, [
                'id' => $this->id,
                'min' => $this->min,
                'max' => $this->max
            ]
        ];
    }
}
