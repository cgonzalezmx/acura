<?php

namespace App\Http\Resources\SamplingFormats;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class ListResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $contact = $this->quote->selectedContact->systemContact;
        $contactPhone = !empty($contact->phone) && isset($contact->phone)
            ? $contact->phone
            : $contact->cellphone;
        $contactEmail = !empty($contact->email) && isset($contact->email)
            ? $contact->email
            : $contact->alt_eamil;

        return [
            'id' => $this->id,
            'identifier' => $this->identifier,
            'quote_identifier' => $this->quote->identifier,
            'created_at' => $this->created_at->timezone('America/Mexico_City')->format('d/m/Y H:i'),
            'client_name' => $this->quote->client->name,
            'contact_name' => $contact->name,
            'contact_phone' => $contactPhone,
            'contact_email' => $contactEmail,
            'entry_index' => $this->entry_index,
        ];
    }
}
