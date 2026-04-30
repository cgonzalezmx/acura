<?php

namespace App\Http\Resources\Quotes;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EntryResource extends JsonResource
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
            'entry_id' => $this->entry_id,
            'title' => $this->title,
            'is_urgent' => $this->is_urgent,
            'form_factor' => $this->form_factor,
            'result_time_lapse' => $this->result_time_lapse,
            'takes' => $this->takes,
            'concept' => $this->concept,
            'objective' => $this->objective,
            'price_offset' => $this->price_offset,
            'sampling_date' => $this->sampling_date,
            'sample_reception_date' => $this->sample_reception_date,
            'sample_type' => $this->sample_type,
            'sample_container_type' => $this->sample_container_type,
            'sample_temperature' => $this->sample_temperature,
            'total_containers' => $this->total_containers,
            'total_volume' => $this->total_volume,
            'refrigerated' => $this->refrigerated,
            'observation' => $this->observation,
            'matrix_id' => $this->matrix_id,
            'quantity' => $this->quantity,
            'bundle_price' => $this->bundle_price,
            'extras' => $this->extras,
            'included_parameters' => $this->parameters->mapWithKeys(function($param) {
                return [$param->parameter_id => $param];
            }),
            'reports' => $this->reports->mapWithKeys(function($report) {
                return [$report->report_id => new ReportResource($report)];
            }),
            'price_offset_notes' => $this->whenNotNull($this->price_offset_notes)
        ];
    }
}
