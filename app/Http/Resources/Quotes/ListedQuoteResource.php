<?php

namespace App\Http\Resources\Quotes;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ListedQuoteResource extends JsonResource
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
            'identifier' => $this->identifier,
            'created_at' => $this->created_at,
            'gross_cost' => $this->gross_cost,
            'net_cost' => $this->net_cost,
            'client' => $this->client->name,
            'contact_name' => $this->contactInSystem->first()->name,
            'contact_phone' => $this->contactInSystem->first()->phone,
            'contact_email' => $this->contactInSystem->first()->email,
            'authorized' => $this->authorized,
        ];
    }
}
