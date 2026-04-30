<?php

namespace App\Http\Resources\Clients;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ContactResource extends JsonResource
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
            'phone' => $this->when(!empty($this->phone), $this->phone, 'N.E.'),
            'cellphone' => $this->when(!empty($this->cellphone), $this->cellphone, 'N.E.'),
            'email' => $this->email,
            'is_main_contact' => $this->is_main_contact
        ];
    }
}
