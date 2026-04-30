<?php

namespace App\Http\Resources\Quotes;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReportResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $thresholds = $this->thresholds;
        $parameters = $this->entry->parameters
            ->whereIn('parameter_id', $thresholds->pluck('parameter_id'))
            ->mapWithKeys(function($param) {
                return [$param->parameter_id => [
                    'from_system' => $param->from_system,
                    'quantity' => $param->quantity
                ]];
            });

        return [
            'report_id' => $this->report_id,
            'structure' => [
                'expandedKeys' => $this->structure_expanded_keys,
                'selectedKeys' => $this->structure_selected_keys
            ],
            'instance' => [
                'expandedKeys' => $this->instance_expanded_keys,
                'selectedKeys' => $this->instance_selected_keys
            ],
            'thresholds' => [
                'system' => $this->thresholds->mapWithKeys(function($threshold) {
                    return [$threshold->parameter_id => $threshold];
                })
            ],
            'parameters' => $parameters,
            'observation' => $this->observation,
            'is_main_report' => $this->is_main_report,
        ];
    }
}
