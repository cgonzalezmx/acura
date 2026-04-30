<?php

namespace App\Http\Resources\SamplingFormats;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SamplingDetialsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $json = [];
        return parent::toArray($request);
    }
}
