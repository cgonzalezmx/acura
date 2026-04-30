<?php

namespace App\Http\Resources\Clients;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SamplingSiteResource extends JsonResource
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
            'name' => $this->name,
            'industry_sector' => $this->industry_sector,
            'is_main_site' => $this->is_main_site,
            'neighborhood' => $this->neighborhood,
            'city' => $this->city,
            'zip_code' => $this->zip_code,
            'state' => $this->state,
            'contact' => [
                'name' => $this->contact_name,
                'phone' => $this->when(!empty($this->contact_phone), $this->contact_phone, 'N.E.'),
                'cellphone' => $this->when(!empty($this->contact_cellphone), $this->contact_cellphone, 'N.E.'),
                'email' => $this->when(!empty($this->contact_email), $this->contact_email, 'N.E'),
                'alt_email' => $this->when(!empty($this->contact_alt_email), $this->contact_alt_email, 'N.E.')
            ],
            'address' => $this->when($request->query('context') === 'quote_edition', $this->address)
        ];
    }
}
