<?php

namespace App\Http\Controllers\Quotes;

use App\DTO\Client\SamplingSite;
use App\Http\Controllers\Controller;
use App\Http\Requests\Quotes\StoreQuoteRequest;
use App\Http\Resources\Quotes\ListedQuoteResource;
use App\Http\Resources\Quotes\QuoteResource;
use App\Models\Quotes\CommercialTerm;
use App\Models\Quotes\Note;
use App\Models\Quotes\Quote;
use App\PDF\Quote as QuotePDF;
use App\Services\Catalog\AnalysisAreaService;
use App\Services\Quotes\ParameterService;
use App\Services\Quotes\QuoteCopyService;
use App\Services\Quotes\QuoteService;
use App\Services\Regulatory\Structure\TreeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Str;

class QuoteController extends Controller
{
    public function __construct(
        public QuoteService $quoteService,
        public QuoteCopyService $copyService,
    ){}

    private function displayForm(array $props = [])
    {
        return inertia('Quotes/Form', $props);
    }

    public function index(Request $request)
    {
        $from = $request->input('from', date('Y-m-01'));
        $until = $request->input('until', date('Y-m-d'));
        $quotes = Quote::with([
                'client:name,quote_id',
                'contactInSystem:name,phone,email,id'
            ])
            ->select(['id', 'identifier', 'created_at', 'gross_cost', 'net_cost'])
            ->from($from)
            ->until($until)
            ->get();
        return inertia('Quotes/List', [
            'quotes' => ListedQuoteResource::collection($quotes)
        ]);
    }

    public function create(TreeService $tree, AnalysisAreaService $areas)
    {
        return $this->displayForm([
            'root_nodes' => $tree->getRootNodes(),
            'areas' => $areas->asMap()
        ]);
    }

    public function store(StoreQuoteRequest $request)
    {
        $validated = $request->validated();
        $user = Auth::user();
        $quote = $this->quoteService->createQuote([
            'quote' => [
                ...$validated['quote'],
                ...$validated['costs'],
                'user_id' => $user->id,
                'user_alias' => $user->alias
            ],
            'client' => $validated['client'],
            'contact' => $validated['contact'],
            'site' => $validated['site'] ?? null,
            'entries' => $validated['entries']
        ]);

        $request->session()->flash('message', "Cotización {$quote->identifier} creada.");

        return to_route('quotes.edit', [ 'quote' => $quote->id ]);
    }

    public function edit(Quote $quote, TreeService $tree, QuoteService $quoteService, AnalysisAreaService $areas)
    {
        $quote->load([
            'client',
            'selectedContact',
            'selectedSamplingSite',
            'entries',
            'entries.parameters',
            'entries.reports',
            'entries.reports.thresholds'
        ]);

        $structureNodes = $quoteService->getStructureTreeNodes($quote);
        $instanceNodes = $quoteService->getInstanceTreeNodes($quote);
        $contacts = $quote->client->systemInfo->contacts;
        $samplingSites = $quote->client->systemInfo->samplingSites;
        $samplingSites->append('address');
        $samplingSites = $samplingSites->map(function($item) {
            return [
                ...$item->toArray(),
                'contact' => [
                    'name' => $item->contact_name,
                    'phone' => $item->contact_phone ?? $item->contact_cellphone,
                    'email' => $item->contact_email ?? $item->contact_alt_email,
                    'address' => $item
                ]
            ];
        });

        return $this->displayForm([
            'root_nodes' => $tree->loadTree($structureNodes, $instanceNodes),
            'contacts' => $contacts,
            'sites' => $samplingSites,
            'selectedContact' => $quote->selectedContact,
            'selectedSite' => $quote->selectedSamplingSite,
            'areas' => $areas->asMap(),
            'quote' => new QuoteResource($quote)
        ]);
    }

    public function show(Quote $quote, ParameterService $parameters)
    {
        $params = $parameters->withSystemInfo($quote);
        $arranged = $parameters->arrange($params)->values();
        $firstPage = null;
        $chunks = null;
        $totalEntries = $quote->entries->count();
        $needsFirstChunkSpliced = $parameters->shouldSpliceFirstChunk($totalEntries, isset($quote->price_adjustment));
        $pages = null;

        $quoteRemarks = $params
            ->pluck('quote_remarks')
            ->flatten(1)
            ->unique()
            ->sortBy('code', SORT_NATURAL);

        if ($needsFirstChunkSpliced) {
            $spliced = $parameters->spliceFirstChunk($arranged, $totalEntries);
            $firstPage = collect([$arranged]);
            $chunks = $firstPage->merge($spliced->chunk(40));
        }
        else {
            $chunks = $arranged->chunk(40);
        }

        $pages = $parameters->groupByInEntries($chunks);
        $AnalysedMatrices = $quote
            ->entries
            ->map(fn($entry) => $entry->matrix->name)
            ->unique()
            ->join(', ');

        $contact = $quote->authorized ? $quote->selectedContact : $quote->contactInSystem->first();
        $client = $quote->client;
        $fullAddres = [
            $client->address,
            "Col: {$client->neighborhood}",
            "CP: {$client->zip_code}",
            $client->city,
            $client->state,
        ];
        $samplingSite = $quote->client_data_as_sampling_site
            ? new SamplingSite([
                'name' => $quote->client->name,
                'address' => join(', ', $fullAddres),
                'contact_name' => $contact->name,
                'contact_phone' => $contact->phone ?? $contact->cellphone
            ])
            : new SamplingSite([
                'name' => $quote->samplingSiteInSystem()->first()->name,
                'address' => '',
                'conctact_name' => $quote->samplingSiteInSystem()->first()->contact_name,
                'conctact_phone' => $quote->samplingSiteInSystem()->first()->contact_phone
            ]);
        $maxResultTimeLapse = $quote->entries->max('result_time_lapse');
        $commercialTerms = CommercialTerm::all()->map(function($term) use ($maxResultTimeLapse) {
            return Str::swap([':tiempoResultados' => $maxResultTimeLapse], $term->text);
        });
        return (new QuotePDF([
            'quote' => $quote,
            'contact' => $contact,
            'analyzedMatrices' => $AnalysedMatrices,
            'samplingSite' => $samplingSite,
            'entries' => $quote->entries,
            'totalEntries' => $totalEntries,
            'parameterPages' => $pages,
            'notes' => Note::all(),
            'commercialTerms' => $commercialTerms,
            'userName' => $quote->createdBy->name,
            'signaturePath' => Storage::url($quote->createdBy->signature_path),
            'quoteRemarks' => $quoteRemarks,
            'address' => join(', ', $fullAddres),
        ]))->inline();
    }

    public function update(Quote $quote, StoreQuoteRequest $request)
    {
        $validated = $request->validated();
        $this->quoteService->updateQuote($quote, [
            'quote' => [
                ...$validated['quote'],
                ...$validated['costs']
            ],
            'client' => $validated['client'],
            'contact' => $validated['contact'],
            'site' => $validated['site'] ?? null,
            'entries' => $validated['entries']
        ]);

        $request->session()->flash('message', 'Cotización guardada.');
    }

    public function copy(Quote $quote)
    {
        $this->copyService->copyQuote($quote);
    }

    public function entries(Quote $quote)
    {
        return response()->json($quote->entries);
    }
}
