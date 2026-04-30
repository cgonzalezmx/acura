<?php

namespace App\Services\Formats;

use App\DTO\Client\SamplingSite;
use App\Models\Client\Contact;
use App\Models\Quotes\Quote;
use App\Models\Quotes\SelectedContact;

class ClientEntityAdapter
{
    public function contact(Quote $quote): SelectedContact | Contact
    {
        return $quote->authorized
            ? $quote->selectedContact
            : $quote->contactInSystem->first();
    }

    public function samplingSite(Quote $quote)
    {
        $contact = $this->contact($quote);
        $samplingSite = $quote->authorized
            ? $quote->selectedSamplingSite
            : $quote->samplingSiteInSystem->first();

        return $quote->client_data_as_sampling_site
            ? new SamplingSite([
                'name' => $quote->client->name,
                'address' => $quote->client->address,
                'contact_name' => $contact->name,
                'contact_phone' => $contact->phone ?? $contact->cellphone,
                'contact_email' => $contact->email ?? $contact->alt_email
            ])
            : new SamplingSite([
                'name' => $samplingSite->name,
                'address' => $samplingSite->address,
                'contact_name' => $samplingSite->contact_name,
                'contact_phone' => $samplingSite->contact_phone ?? $samplingSite->contact_cellphone,
                'contact_email' => $samplingSite->contact_email ?? $samplingSite->contact_alt_email
            ]);
    }
}