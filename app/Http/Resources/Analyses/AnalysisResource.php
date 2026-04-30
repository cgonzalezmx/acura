<?php

namespace App\Http\Resources\Analyses;

use App\Http\Resources\Catalog\LabMatrixResource;
use App\Http\Resources\Samples\SampleResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AnalysisResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'index' => $this->index,
            'total_indexes' => $this->whenNotNull($this->total_indexes),
            'parameter' => $this->whenLoaded('parameter', fn() => $this->whenNotNull($this->parameter->name)),
            'is_urgent' => $this->whenLoaded('sample', fn() => $this->whenNotNull($this->sample->is_urgent)),
            'threshold' => $this->whenLoaded('thresholds', $this->thresholds[0]->max),
            'refrigerator' => $this->whenNotNull($this->sample->refrigerator),
            'is_ranged' => $this->whenAppended('isRanged'),
            'registered' => $this->whenNotNull($this->registered),
            'registration_counter' => $this->whenNotNull($this->registration_counter),
            'lab_matrix' => new LabMatrixResource($this->whenLoaded('labMatrix')),
            'parameter_id' => $this->whenNotNull($this->parameter_id),
            'sample' => new SampleResource($this->whenLoaded('sample')),
            'params' => $this->whenNotNull($this->params),
            'result' => $this->whenNotNull($this->result),
            'reported_result' => $this->whenNotNull($this->reported_result),
            'analyzed_at' => $this->whenNotNull($this->analyzed_at),
        ];
    }
}
