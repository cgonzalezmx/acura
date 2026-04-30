<?php

namespace App\Http\Resources\Clients;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ClientResource extends JsonResource
{
    private function fullContextAttributes(): array
    {
        return [
            'company' => $this->company,
            'street' => $this->street,
            'external_number' => $this->external_number,
            'internal_number' => $this->internal_number
        ];
    }
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $context = $request->context;
        return [
            'id' => $this->id,
            'name' => $this->name,
            'industry_sector' => $this->industry_sector,
            'neighborhood' => $this->neighborhood,
            'zip_code' => $this->zip_code,
            'city' => $this->city,
            'state' => $this->state,
            'version' => $this->version,
            $this->mergeWhen($context === 'full', $this->fullContextAttributes()),
            'address' => $this->when(
                $context === 'quote_edition',
                fn() => $this->address
            ),
            'contacts' => ContactResource::collection($this->whenLoaded('contacts')),
            'sampling_sites' => SamplingSiteResource::collection($this->whenLoaded('samplingSites'))
        ];
    }
}
