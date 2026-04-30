<?php

namespace App\Http\Resources\Quotes;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class QuoteResource extends JsonResource
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
            'objective' => $this->objective,
            'identifier' => $this->identifier,
            'client' => new ClientResource($this->whenLoaded('client')),
            'entries' => EntryResource::collection($this->entries),
            'priceAdjustment' => $this->when($this->price_adjustment > 0, [
                'total' => $this->price_adjustment,
                'percentage' => $this->price_adjustment_percentage ?? 0,
                'type' => $this->gross_cost > $this->subtotal ? 'discount' : 'charge',
                'hasPercentage' => $this->price_adjustment_percentage > 0 ? true : false,
                'notes' => $this->price_adjustment_notes
            ]),
            'validity' => $this->validity,
            'client_data_as_sampling_site' => $this->client_data_as_sampling_site,
            'selectedContact' => $this->selectedContact,
            'subtotal' => $this->subtotal,
            'gross_cost' => $this->gross_cost,
            'sample_delivered_by_client' => $this->sample_delivered_by_client,
            'expenses' => $this->whenNotNull($this->expenses),
            'payment_method' => $this->payment_method,
            'notes' => $this->notes,
            'global_expenses_concept' => $this->global_expenses_concept,
            'global_expenses_quantity' => $this->global_expenses_quantity
        ];
    }
}
