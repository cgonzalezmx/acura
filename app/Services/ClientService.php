<?php

namespace App\Services;

use App\Models\Client\Client;

class ClientService
{
    private array $baseClientColumns = [
        'id',
        'name',
        'is_main_contact',
        'industry_sector',
        'street',
        'external_number',
        'internal_number',
        'neighborhood',
        'zip_code',
        'city',
        'state',
        'version'
    ];

    public function quoteEditionContext(string $clienId)
    {
        $client = Client::select($this->baseClientColumns)->find($clienId);
        $client->load(
            [
                'contacts' => function($query) {
                    $query->orderBy('is_main_contact', 'desc');
                    $query->orderBy('id', 'desc');
                },
                'samplingSites' => function($query) {
                    $query->orderBy('is_main_site', 'desc');
                    $query->orderBy('id', 'desc');
                }
            ]
        );

        return $client;
    }
}