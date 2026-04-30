<?php

namespace App\DTO\Client;

class SamplingSite
{
    public string $name;
    public string $address;
    public string $contact_name;
    public string $contact_phone;
    public string $contact_email;

    public function __construct(array $data)
    {
        $this->name = $data['name'] ?? '';
        $this->address = $data['address'] ?? '';
        $this->contact_name = $data['contact_name'] ?? '';
        $this->contact_phone = $data['contact_phone'] ?? '';
        $this->contact_email = $data['contact_email'] ?? '';
    }
}