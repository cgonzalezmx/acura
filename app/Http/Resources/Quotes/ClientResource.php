<?php

namespace App\Http\Resources\Quotes;

use App\Http\Resources\Clients\ContactResource;
use App\Http\Resources\Clients\SamplingSiteResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ClientResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => isset($this->client_id) ? $this->id : null,
            'client_id' => isset($this->client_id) ? $this->client_id : $this->id,
            'name' => $this->name,
            'industry_sector' => $this->industry_sector,
            'address' => $this->address,
            'neighborhood' => $this->neighborhood,
            'city' => $this->city,
            'state' => $this->state,
            'zip_code' => $this->zip_code,
            'contacts' => ContactResource::collection($this->whenLoaded('contacts')),
            'sampling_sites' => SamplingSiteResource::collection($this->whenLoaded('samplingSites'))
        ];
    }
}
