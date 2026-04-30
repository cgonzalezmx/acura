<?php

namespace App\Services\SamplingFormats;

use App\Models\Quotes\Entry;
use App\Models\SamplingFormat;
use App\Services\Formats\ClientEntityAdapter;
use Illuminate\Support\Facades\DB;

class SamplingFormatService
{
    public function __construct(
        private SamplingFormatIdentifierService $serviceIdentifier
    )
    {}

    public function createSamplingFormat(Entry $entry): SamplingFormat | false
    {
        $format = false;
        DB::transaction(function() use($entry, &$format) {
            $quote = $entry->quote;
            $clientId = $quote->client->client_id;
            $samplingFormat = new SamplingFormat();
            $samplingFormat->identifier = $this->serviceIdentifier->makeIdentifier($clientId);
            $samplingFormat->sequence_index = SamplingFormat::getMaxYearlySequence() + 1;
            $samplingFormat->year = date('Y');
            $samplingFormat->entry_index = $entry->index;
            $samplingFormat->quote()->associate($quote);
            $samplingFormat->entry()->associate($entry);
            $samplingFormat->save();
            $format = $samplingFormat->refresh();
        });

        return $format;
    }

    public function getDetails(SamplingFormat $samplingFormat, ClientEntityAdapter $adapter): array
    {
        $quote = $samplingFormat->quote;
        $entry = $samplingFormat->entry->load('matrix:id,code');
        $client = $quote->client;
        $clientContact = $adapter->contact($quote);
        $site = $adapter->samplingSite($quote);
        $deliveredByClient = $quote->sample_delivered_by_client;
        $sampleType = collect(explode(',', $entry->concept));
        $json = [
            'client' => [
                'name' => $client->name,
                'address' => $client->address,
                'contact_name' => $clientContact->name,
                'contact_phone' => $clientContact->phone,
                'contact_email' => $clientContact->email
            ],
            'site' => (array) $site,
            'takes_count' => $entry->takes,
            'is_urgent' => $entry->is_urgent,
            'matrix' => $entry->matrix->code,
            'delivered_by_client' => $deliveredByClient,
            'format' => [
                'id' => $samplingFormat->id,
                'identifier' => $samplingFormat->identifier
            ],
            'points' => $entry->title,
            'total_points' => count(explode(';', $entry->title)),
            'sample_type' => $deliveredByClient ? $entry->sample_type : $sampleType->slice(0, 2)->join(' - '),
        ];

        if ($deliveredByClient) {
            $json = array_merge($json, [
                'sample_temperature' => $entry->sample_temperature,
                'total_containers' => $entry->total_containers
            ]);
        }

        return $json;
    }
}
