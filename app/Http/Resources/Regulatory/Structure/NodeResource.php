<?php

namespace App\Http\Resources\Regulatory\Structure;

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
            'alias' => $this->alias,
            'leaf' => $this->leaf,
            'type' => $this->type,
            'parent_id' => $this->parent_id,
            'icon' => $this->when($this->type !== 'node', function() {
                return 'fa-solid ' . ($this->type === 'regulation' ?
                    'fa-rectangle-list text-primary-500' :
                    'fa-cube text-amber-500');
            }),
            $this->mergeWhen(isset($this->nodable_id), function() {
                $class = match($this->type) {
                    'regulation' => RegulationResource::class,
                    'bundle' => BundleResource::class
                };

                return [$this->type => new $class($this->whenLoaded('nodable'))];
            }),
            'children' => NodeResource::collection($this->whenLoaded('children'))
        ];
    }
}