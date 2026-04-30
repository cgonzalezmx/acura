<?php

namespace App\Http\Controllers;

use App\Http\Resources\Quotes\ClientResource;
use App\Models\Client\Client;
use App\Services\ClientService;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    public function search(Request $request)
    {
        $suggestions = Client::select(['id', 'name'])
            ->where('name', 'like', "%{$request->str('term')}%")
            ->get();
        return response()->json($suggestions);
    }

    public function show(string $clientId, ClientService $service, Request $request)
    {
        if ($request->query('context') === 'quote_edition') {
            return new ClientResource($service->quoteEditionContext($clientId));
        }
    }

    public function index()
    {
        $clients = Client::orderBy('name')->get();
        $clients->append('active');

        return inertia('Clients/Index', [
            'clients' => $clients,
        ]);
    }
}
