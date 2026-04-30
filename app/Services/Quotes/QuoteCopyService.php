<?php

namespace App\Services\Quotes;

use App\Models\Quotes\Client;
use App\Models\Quotes\Entry;
use App\Models\Quotes\Expense;
use App\Models\Quotes\Parameter;
use App\Models\Quotes\Quote;
use App\Models\Quotes\Report;
use App\Models\Quotes\SelectedContact;
use App\Models\Quotes\SelectedSamplingSite;
use App\Models\Quotes\Threshold;
use App\Traits\ConvertIndexToLetter;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class QuoteCopyService
{
    use ConvertIndexToLetter;

    public function __construct(
        private QuoteIdentifierService $identifierService
    ){}

    public function copyQuote(Quote $originalQuote)
    {
        DB::transaction(function() use($originalQuote) {
            $currentLetterIndex = DB::table('quotes')
                ->where('original_ancestor_id', '=', $originalQuote->original_ancestor_id)
                ->max('letter_index');
            
            $quoteCopy =  $originalQuote
                            ->replicate()
                            ->fill([
                                'created_by' => Auth::id(),
                                'letter_index' => ++$currentLetterIndex,
                                'identifier' => $this->identifierService->makeIdentifier(
                                    $originalQuote->sequence_index,
                                    $originalQuote->originalCreator->alias
                                ) . $this->getLetterIndex($currentLetterIndex)
                            ]);
            $quoteCopy->save();
            $originalEntries = $originalQuote->entries;
            $this->copyClientToQuote($originalQuote->client, $quoteCopy);
            $this->copySelectedContactToQuote($originalQuote->selectedContact, $quoteCopy);

            if ($site = $originalQuote->selectedSamplingSite) {
                $this->copySelectedSiteToQuote($site, $quoteCopy);
            }

            $this->copyEntriesToQuote($originalEntries, $quoteCopy);
            $this->copyExpensesToQuote($originalQuote->expenses, $quoteCopy);
        });
    }

    private function copyEntriesToQuote(Collection $entries, Quote $quote)
    {
        $entries->each(function(Entry $entry) use($quote) {
            $copy = $entry->replicate()->fill([
                'entry_id' => Str::ulid()->toString()
            ]);
            $newEntry = $quote->entries()->save($copy);
            $originalReports = $entry->reports;
            $this->copyEntryParametersToEntry($entry->parameters, $newEntry);
            $this->copyReportsToEntry($originalReports, $newEntry);
        });
    }

    private function copyReportsToEntry(Collection $reports, Entry $entry)
    {
        $reports->each(function(Report $report) use($entry) {
            $copy = $report->replicate()->fill([
                'report_id' => Str::ulid()->toString()
            ]);
            $newReport = $entry->reports()->save($copy);
            $originalThresholds = $report->thresholds;
            $this->copyThresholdsToReport($originalThresholds, $newReport);
        });
    }

    private function copyThresholdsToReport(Collection $thresholds, Report $report)
    {
        $thresholds->each(function(Threshold $threshold) use($report) {
            $copy = $threshold->replicate();
            $report->thresholds()->save($copy); 
        });
    }

    private function copyEntryParametersToEntry(Collection $parameters, Entry $entry)
    {
        $parameters->each(function(Parameter $parameter) use($entry) {
            $copy = $parameter->replicate()->fill([
                'quote_id' => $entry->quote_id
            ]);
            $entry->parameters()->save($copy);
        });
    }

    public function copyExpensesToQuote(Collection $expenses, Quote $quote)
    {
        $decoupledExpenses = collect($expenses->toArray());
        
        $quote->expenses()->saveMany($decoupledExpenses->mapInto(Expense::class)->all());
        $quote->refresh();
    }

    public function copyClientToQuote(Client $client, Quote $quote)
    {
        $copy = $client->replicate();
        $quote->client()->save($copy);
    }

    public function copySelectedContactToQuote(SelectedContact $contact, Quote $quote)
    {
        $copy = $contact->replicate();
        $quote->selectedContact()->save($copy);
    }

    public function copySelectedSiteToQuote(SelectedSamplingSite $site, Quote $quote)
    {
        $copy = $site->replicate();
        $quote->selectedSamplingSite()->save($copy);
    }
}