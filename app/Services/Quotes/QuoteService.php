<?php
namespace App\Services\Quotes;

use App\Models\Client\Contact;
use App\Models\Client\SamplingSite;
use App\Models\Quotes\Entry;
use App\Models\Quotes\Quote;
use App\Models\Quotes\Report;
use App\Traits\ConvertIndexToLetter;
use ArrayObject;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class QuoteService {
    use ConvertIndexToLetter;

    public function __construct(
        private QuoteIdentifierService $identifierService
    )
    {}

    public function createQuote(array $data)
    {
        $quote = null;

        DB::transaction(function() use($data, &$quote) {
            $quote = $this->newQuote($data['quote']);
            $this->syncClient($quote, $data['client']);
            $this->syncSelectedContact($quote, $data['contact']);
            if (!$data['quote']['client_data_as_sampling_site']) {
                $this->syncSelectedSite($quote, $data['site']);
            }

            if (isset($data['quote']['expenses'])) {
                $this->syncExpenses($quote, $data['quote']['expenses']);
            }


            $this->syncEntries($quote, $data['entries']);
        });

        return $quote;
    }

    public function updateQuote(Quote $quote, array $data)
    {
        DB::transaction(function() use($quote, $data) {
            $this->fillQuote($quote, $data['quote']);
            $quote->save();
            $this->syncClient($quote, $data['client']);
            $this->syncSelectedContact($quote, $data['contact']);

            if (!$data['quote']['client_data_as_sampling_site']) {
                $this->syncSelectedSite($quote, $data['site']);
            }

            if (isset($data['quote']['expenses'])) {
                $this->syncExpenses($quote, $data['quote']['expenses']);
            }

            $this->syncEntries($quote, $data['entries']);
        });
    }

    private function newQuote(array $data): Quote
    {
        $nextInSequence = Quote::getMaxYearlySequence() + 1;
        $quote = new Quote;
        $quote->fill([
            'year' => date('Y'),
            'sequence_index' => $nextInSequence,
            'letter_index' => 1,
            'identifier' => $this->identifierService->makeIdentifier($nextInSequence, $data['user_alias']) . $this->getLetterIndex(1),
            'original_creator' => $data['user_id']
        ]);
        $this->fillQuote($quote, $data);

        $quote->save();
        $quote->original_ancestor_id = $quote->id;
        $quote->save();
        return $quote;
    }

    public function getTreeNodes(Quote $quote, string $treeIdentifier): Collection
    {
        $extractedKeys = fn(ArrayObject $arrObj) => array_keys($arrObj->getArrayCopy());

        return $quote->entries->flatMap(function($entry) use($treeIdentifier, $extractedKeys) {
            $reports =$entry->reports;
            $baseNodes = $reports
                ->pluck($treeIdentifier.'_expanded_keys')
                ->map($extractedKeys);
            $selectedNode = $reports
                ->pluck($treeIdentifier.'_selected_keys')
                ->map($extractedKeys);
            return $baseNodes->merge($selectedNode);
        })->flatten();
    }

    public function getStructureTreeNodes(Quote $quote): Collection
    {
        return $this->getTreeNodes($quote, 'structure');
    }

    public function getInstanceTreeNodes(Quote $quote): Collection
    {
        return $this->getTreeNodes($quote, 'instance');
    }

    private function fillQuote(Quote $quote, array $data)
    {
        $quote->fill($data);
    }

    private function syncClient(Quote $quote, array $data)
    {
        $quote->client()->updateOrCreate(
            ['quote_id' => $quote->id],
            $data
        );
    }

    private function syncSelectedContact(Quote $quote, array $data)
    {
        $contact = Contact::find($data['client_contact_id']);
        $info = [
            'name',
            'is_main_contact',
            'phone',
            'cellphone',
            'email',
        ];
        $quote->selectedContact()->updateOrCreate(
            ['quote_id' => $quote->id],
            [...$contact->only($info), 'client_contact_id' => $contact->id]
        );
    }

    private function syncSelectedSite(Quote $quote, array $data)
    {
        $samplingSite = SamplingSite::find($data['client_sampling_site_id']);
        $info = [
            'name',
            'industry_sector',
            'is_main_site',
            'neighborhood',
            'city',
            'state',
            'contact_name',
            'contact_phone',
            'zip_code',
        ];
        $quote->selectedSamplingSite()->updateOrCreate(
            ['quote_id' => $quote->id],
            [
                ...$samplingSite->only($info),
                'address' => join(', ', $samplingSite->only(['street', 'external_number', 'internal_number'])),
                'client_sampling_site_id' => $samplingSite->id,
                'phone' => $samplingSite->contact_phone,
            ]
        );
    }

    public function syncEntries(Quote $quote, array $entries)
    {
        $incomingIds = collect($entries)->pluck('entry_id')->filter()->all();
        $quote->entries()->whereNotIn('entry_id', $incomingIds)->delete();

        foreach($entries as $item) {
            $reports = collect($item['reports']);
            $entry = $quote->entries()->updateOrCreate(
                ['entry_id' => $item['entry_id']],
                $item
            );
            $this->syncEntryParameters($entry, $item['included_parameters']);
            $this->syncReports($entry, $reports);
        }
    }

    private function syncReports(Entry $entry, Collection $rows)
    {
        $incomingIds = $rows->pluck('report_id')->filter();
        $entry->reports()->whereNotIn('report_id', $incomingIds)->delete();

        return $rows->map(function($row) use($entry) {
            $report = $entry->reports()->updateOrCreate(['report_id' => $row['report_id']], $row);
            $this->syncReportThresholds($report, $row['thresholds']);

            return $report;
        });
    }

    private function syncReportThresholds(Report $report, array $thresholds)
    {
        $thresholds = collect($thresholds);
        $incomingIds = $thresholds->pluck('parameter_id')->filter()->all();
        $report->thresholds()->whereNotIn('parameter_id', $incomingIds)->delete();
        $report->thresholds()
            ->whereIn('id', $thresholds->whereNull('max')->pluck('id')->all())
            ->delete();

        $report->thresholds()->upsert(
            $thresholds->whereNotNull('max')->toArray(),
            uniqueBy: ['report_id', 'parameter_id'],
            update: ['min', 'max', 'custom_boundary']
        );
    }

    private function syncEntryParameters(Entry $entry, array $parameters)
    {
        $incoming = collect($parameters)->map(fn($p) => [
            ...$p,
            'quote_id' => $entry->quote_id
        ]);

        $incomingIds = $incoming->pluck('parameter_id')->filter()->all();
        $entry->parameters()->whereNotIn('parameter_id', $incomingIds)->delete();

        $entry->parameters()->upsert(
            $incoming->all(),
            uniqueBy: ['quote_entry_id', 'parameter_id'],
            update: ['quantity']
        );
    }

    private function syncExpenses(Quote $quote, array $data)
    {
        $incoming = collect($data);
        $incomingIds = $incoming->pluck('id')->filter()->all();
        $quote->expenses()->whereNotIn('id', $incomingIds)->delete();

        $quote->expenses()->upsert(
            $incoming->all(),
            uniqueBy: ['id'],
            update: ['cost', 'concept', 'quantity']
        );
    }
}
