<?php

namespace App\Http\Controllers\Quotes;

use App\Http\Controllers\Controller;
use App\Http\Requests\Quotes\StoreQuoteRequest;
use App\Http\Resources\Quotes\ListedQuoteResource;
use App\Http\Resources\Quotes\QuoteResource;
use App\Models\Quotes\Quote;
use App\Services\Catalog\AnalysisAreaService;
use App\Services\Quotes\QuoteFile;
use App\Services\Quotes\QuoteCopyService;
use App\Services\Quotes\QuoteService;
use App\Services\Regulatory\Structure\TreeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use WeasyPrint\Facade as Weasyprint;

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
            ->select(['id', 'identifier', 'created_at', 'net_cost', 'authorized'])
            ->from($from)
            ->until($until)
            ->get();
        return inertia('Quotes/List', [
            'items' => ListedQuoteResource::collection($quotes)
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

        $request->session()->flash('message', "Cotización {$quote?->identifier} creada.");

        return to_route('quotes.edit', [ 'quote' => $quote?->id ]);
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

    public function show(Quote $quote, QuoteFile $file)
    {
        if ($quote->authorized) {
            return response(Storage::get($quote->file_path), headers: [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline',
            ]);
        }
        return $file->process($quote)->inline();
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
        $query = $quote->entries();
        $query->withExists('samplingFormat');
        $entries = $query->get()->map(function($entry) {
            return [
                'id' => $entry->id,
                'authorized' => $entry->sampling_format_exists,
                'title' => $entry->title
            ];
        });
        return response()->json($entries);
    }

    public function authorize(Quote $quote, QuoteFile $file)
    {
        $pdf = $file->process($quote);
        $filePath = "quotes/{$pdf->filename()}";
        $service = Weasyprint::prepareSource($pdf->source());
        $service->putFile($filePath, 'local');
        $quote->authorized = true;
        $quote->file_path = $filePath;
        $quote->save();
    }

    public function destroy(Quote $quote)
    {
        $quote->delete();
        return back();
    }
}
