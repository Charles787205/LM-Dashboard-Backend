<?php

namespace App\Http\Controllers\LastMile;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Client;

class ClientController extends Controller
{
    public function index()
    {
        $clients = Client::all();
        return response()->json($clients);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'color_code' => 'nullable|string|max:7',
        ]);

        $client = Client::create($validated);
        return response()->json($client, 201);
    }

    public function destroy($id)
    {
        $client = Client::findOrFail($id);
        $client->delete();
        
        return response()->json([
            'message' => 'Client deleted successfully'
        ], 200);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'color_code' => 'nullable|string|max:7',
        ]);

        $client = Client::findOrFail($id);
        $client->update($validated);

        return response()->json($client);
    }
}
