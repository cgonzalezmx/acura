<?php

namespace App\Http\Resources;

use App\Http\Resources\Traits\Blamable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ParameterResource extends JsonResource
{
    use Blamable;
    
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
            'name' => $this->name,
            'unit_volume' => $this->whenNotNull($this->unit_volume),
            'group_volume' => $this->whenNotNull($this->group_volume),
            'price' => $this->whenNotNull($this->price),
            'created_at' => $this->whenNotNull($this->created_at),
            'updated_at' => $this->whenNotNull($this->updated_at),
            'parameter_category_id' => $this->whenNotNull($this->parameter_category_id),
            'analysis_area_id' => $this->whenNotNull($this->analysis_area_id),
            'methodology_id' => $this->whenNotNull($this->methodology_id),
            'sample_container_id' => $this->whenNotNull($this->sample_container_id),
            'measurement_unit_id' => $this->whenNotNull($this->measurement_unit_id),
            'sample_storage_id' => $this->whenNotNull($this->sample_storage_id),
            'label_color_id' => $this->whenNotNull($this->label_color_id),
            'sample_preserver_id' => $this->whenNotNull($this->sample_preserver_id),
            'lab_matrix_id' => $this->whenNotNull($this->lab_matrix_id),
            'analysis_area' => $this->whenLoaded('analysisArea', fn() => $this->analysisArea->name),
            'lab_matrix' => $this->whenLoaded('labMatrix', fn() => $this->labMatrix->code),
            'sample_container' => $this->whenLoaded('sampleContainer', fn() => $this->sampleContainer->name),
            'measurement_unit' => $this->whenLoaded('measurementUnit', fn() => $this->measurementUnit->unit),
            'sample_storage' => $this->whenLoaded('sampleStorage', fn() => $this->sampleStorage->identifier),
            'label_color' => $this->whenLoaded('labelColor', fn() => $this->labelColor->color),
            'sample_preserver' => $this->whenLoaded('samplePreserver', fn() => $this->samplePreserver->name),
            'methodology' => $this->whenLoaded('methodology', fn() => $this->methodology->name),
            ...$this->blamableAttributes(),
            'quote_remarks' => $this->quoteRemarks->map(fn($remark) => $remark->id),
            'sampling_remarks' => $this->samplingRemarks->map(fn($remark) => $remark->id),
            'multiple' => $this->multiple,
            'quantification_low_range' => $this->quantification_low_range,
            'quantification_mid_range' => $this->quantification_mid_range,
            'quantification_high_range' => $this->quantification_high_range,
            'uncertainty_low_range' => $this->uncertainty_low_range,
            'uncertainty_mid_range' => $this->uncertainty_mid_range,
            'uncertainty_high_range' => $this->uncertainty_high_range,
        ];
    }
}
