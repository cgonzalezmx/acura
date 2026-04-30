<?php

namespace App\Http\Resources\Regulatory\Instances;

use App\Http\Resources\Regulatory\Instances\ThresholdResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NodeResource extends JsonResource
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
            'key' => $this->id,
            'label' => $this->name,
            'leaf' => $this->leaf,
            'type' => $this->type,
            'parent_id' => $this->parent_id,
            'icon' => $this->when($this->type === 'definition', 'fa-solid fa-scale-balanced text-primary-500'),
            'thresholds' => $this->when($this->type === 'definition', ThresholdResource::collection($this->thresholds)),
            'children' => NodeResource::collection($this->whenLoaded('children'))
        ];
    }
}
