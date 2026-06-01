<?php

namespace App\Services\Quotes;

use App\DTO\Client\SamplingSite;
use App\Models\Quotes\CommercialTerm;
use App\Models\Quotes\Note;
use App\Models\Quotes\Quote;
use App\PDF\Quote as PDFQuote;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Services\AddressService;

class QuoteFile
{
    public function __construct(
        private AddressService $addressService
    ){}

    public function process(Quote $quote)
    {
        $parameters = $this->getParameters($quote);
        $arranged = $this->arrange($parameters);
        $totalEntries = $quote->entries->count();
        $shouldSpliceFirstChunk = $this->shouldSpliceFirstChunk($totalEntries);
        $chunks = $this->getChunks($shouldSpliceFirstChunk, $arranged, $totalEntries);
        $pages = $this->groupByInEntries($chunks);
        $analysedMatrices = $quote
            ->entries
            ->map(fn($entry) => $entry->matrix->name)
            ->unique()
            ->join(', ');
        $samplingSite = $this->getSamplingSite($quote, $this->addressService);
        $maxResultTimeLapse = $quote->entries->max('result_time_lapse');
        $commercialTerms = CommercialTerm::all()->map(function($term) use ($maxResultTimeLapse) {
            return Str::swap([':tiempoResultados' => $maxResultTimeLapse], $term->text);
        });
        $quoteRemarks = $parameters
            ->pluck('quote_remarks')
            ->flatten(1)
            ->unique()
            ->sortBy('code', SORT_NATURAL);

        return (new PDFQuote([
            'quote' => $quote,
            'contact' => $quote->selectedContact,
            'analyzedMatrices' => $analysedMatrices,
            'samplingSite' => $samplingSite,
            'entries' => $quote->entries,
            'totalEntries' => $totalEntries,
            'parameterPages' => $pages,
            'notes' => Note::all(),
            'commercialTerms' => $commercialTerms,
            'userName' => $quote->createdBy->name,
            'signaturePath' => Storage::url($quote->createdBy->signature_path),
            'quoteRemarks' => $quoteRemarks,
            'address' => $this->addressService->getAddress($quote->client),
        ]));
    }

    private function getParameters(Quote $quote)
    {
        return $quote
            ->parameters()
            ->with([
                'systemInfo:id,name,methodology_id',
                'systemInfo.methodology:id,name',
                'systemInfo.quoteRemarks:id,code,description',
                'entry' => fn($query) => $query
                    ->select('id')
                    ->selectRaw('ROW_NUMBER() OVER (ORDER BY id) AS number')
            ])
            ->select(['id', 'parameter_id', 'quote_entry_id'])
            ->get()
            ->map(function($param) {
                return [
                    'name' => $param->systemInfo->name,
                    'methodology' => $param->systemInfo->methodology->name,
                    'entry_number' => $param->entry->number,
                    'quote_remarks' => $param->systemInfo->quoteRemarks->map(function($remark) {
                        return [
                            'code' => $remark->code,
                            'description' => $remark->description
                        ];
                    })->toArray()
                ];
            })
            ->sortBy('name');
    }

    private function arrange(Collection $parameters): Collection
    {
        return $parameters
            ->groupBy(fn($param) => "{$param['name']}:{$param['methodology']}")
            ->map(function(Collection $params) {
                $inEntries = $params->pluck('entry_number')->unique()->sort()->implode(',');
                $meaningful = $params->unique('name')->first();

                return [
                    'name' => $meaningful['name'],
                    'methodology' => $meaningful['methodology'],
                    'in_entries' => $inEntries,
                    'quote_remarks' => collect($meaningful['quote_remarks'])->pluck('code')
                ];
            })
            ->sortBy('in_entries');
    }

    private function getChunks(bool $spliceFirstPage, Collection $parameters, int $totalEntries)
    {
        if ($spliceFirstPage) {
            $spliced = $this->spliceFirstChunk($parameters, $totalEntries);
            $firstPage = collect([$parameters]);
            return $firstPage->merge($spliced->chunk(40));
        }

        return $parameters->chunk(40);
    }

    private function shouldSpliceFirstChunk(int $totalEntries): bool
    {
        return
            ($totalEntries >= 3 && (($totalEntries - 3) % 10) < 8);
    }

    private function spliceFirstChunk(Collection $parameters, int $totalEntries): Collection
    {
        $splicedQuantity = 8;

        $cycleOffset = ($totalEntries - 3) % 10;

        if ($cycleOffset === 0 && $totalEntries > 3) {
            $splicedQuantity = 0;
        }

        if ($cycleOffset > 0) {
            $splicedQuantity += 3 * ($cycleOffset);
        }

        return $parameters->splice(40 - $splicedQuantity);
    }

    private function groupByInEntries(Collection $parameters)
    {
        return $parameters
            ->map(function(Collection $params) {
                return $params
                    ->sortBy([
                        ['methodology', 'asc'],
                        ['name', 'asc']
                    ])
                    ->groupBy('in_entries')
                    ->sortKeys();
            });
    }

    private function getSamplingSite(Quote $quote, AddressService $address)
    {
        if ($quote->client_data_as_sampling_site) {
            $contact = $quote->selectedContact;
            return new SamplingSite([
                'name' => $quote->client->name,
                'address' => $address->getAddress($quote->client),
                'contact_name' => $contact->name,
                'contact_phone' => $contact->phone ?? $contact->cellphone
            ]);
        }

        $selectedSamplingSite = $quote->selectedSamplingSite;
        return new SamplingSite([
                ...$selectedSamplingSite->toArray(),
                'address' => $address->getAddress($selectedSamplingSite),
                'contact_name' => $selectedSamplingSite->contact_name,
                'contact_phone' => $selectedSamplingSite->phone,
            ]);
    }
}
