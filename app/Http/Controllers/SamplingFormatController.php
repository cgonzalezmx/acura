<?php

namespace App\Http\Controllers;

use App\Http\Resources\SamplingFormats\ListResource;
use App\Models\Quotes\Entry;
use App\Models\SamplingFormat;
use App\Services\Formats\ClientEntityAdapter;
use App\Services\Formats\SamplingFormatAdapter;
use App\Services\SamplingFormats\SamplingFormatService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use WeasyPrint\Facade as Weasyprint;

class SamplingFormatController extends Controller
{
    public function __construct(
        private SamplingFormatService $service
    )
    {}

    public function index(Request $request)
    {
        $from = $request->input('from', date('Y-m-01'));
        $until = $request->input('until', date('Y-m-d'));
        $samplingFormats = SamplingFormat::with([
                'quote' => function($query) {
                    $query->withTrashed();
                },
                'quote.client:quote_id,name',
                'quote.selectedContact',
                'quote.selectedContact.systemContact:id,name,phone,cellphone,email,alt_email',
                'entry'
            ])
            ->from($from)
            ->until($until)
            ->get();
        return inertia('SamplingFormats/Index', [
            'samplingFormats' => ListResource::collection($samplingFormats)
        ]);
    }

    public function store(Request $request, SamplingFormatAdapter $adapter)
    {
        $entry = Entry::find($request->entry_id);
        $format = $this->service->createSamplingFormat($entry);

        if ($format) {
            $data = $adapter->process($format);
            $source = view('sampling_format', $data);
            $service = Weasyprint::prepareSource($source);
            $filename = $format->identifier . '.pdf';
            $path = "sampling_formats/$filename";
            if ($service->putFile($path, 'local')) {
                $format->path = $path;
                $format->save();
            }
        }
    }

    public function show(SamplingFormat $samplingFormat)
    {
        $file_path = $samplingFormat->path;
        return response(Storage::get($file_path), headers: [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline',
        ]);
    }

    public function details(string | int $identifier, ClientEntityAdapter $adapter)
    {
        $samplingFormat = SamplingFormat::with(['quote.client', 'entry'])
            ->select(['id', 'quote_id', 'entry_id', 'identifier'])
            ->where('id', $identifier)
            ->orWhere('identifier', trim($identifier))
            ->first();
        $details = $this->service->getDetails($samplingFormat, $adapter);

        return response()->json([
            'id' => $samplingFormat->id,
            ...$details
        ]);
    }

    public function search(Request $request)
    {
        $samplingFormats = SamplingFormat::select(['id', 'identifier'])
            ->whereLike('identifier', "%{$request->str('term')}%")
            ->get();
        return response()->json($samplingFormats);
    }

    public function destroy(SamplingFormat $samplingFormat)
    {
        $samplingFormat->delete();
    }
}
