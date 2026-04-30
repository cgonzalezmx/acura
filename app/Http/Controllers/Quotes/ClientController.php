<?php

namespace App\Http\Controllers\Quotes;

use App\Http\Controllers\Controller;
use App\Models\Quotes\Client;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    public function search(Request $request)
    {
        $clients = Client::select('name')->whereLike('name', "%{$request->str('term')}%")->get();
        $suggestions = $clients->unique('name')->pluck('name');

        return response()->json($suggestions);
    }
}
