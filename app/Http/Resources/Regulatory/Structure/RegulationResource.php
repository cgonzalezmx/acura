<?php

namespace App\Http\Resources\Regulatory\Structure;

use App\Http\Resources\Parameters\RegulationResource as ParameterResource;
use App\Http\Resources\Regulatory\Instances\NodeResource as InstanceNodeResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RegulationResource extends JsonResource
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
            'matrix' => $this->whenLoaded('labMatrix', function() {
                $labMatrix = $this->labMatrix;
                return [
                    'id' => $labMatrix->id,
                    'name' => $labMatrix->name,
                    'code' => $labMatrix->code
                ];
            }),
            'parameters' => ParameterResource::collection($this->whenLoaded('parameters')),
            'instances' => InstanceNodeResource::collection($this->instances),
            'observation' => $this->observation ?? ''
        ];
    }
}
