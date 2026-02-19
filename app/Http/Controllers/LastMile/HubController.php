<?php

namespace App\Http\Controllers\LastMile;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Hub;

class HubController extends Controller
{
    public function index()
    {
        // Simplified eager loading to avoid circular references and timeout
        $hubs = Hub::with(['hubLead:id,name,email', 'client:id,name,color_code'])->get();
        return response()->json($hubs);
    }


    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'client_id' => 'required|exists:clients,id',
        ]);

        $hub = Hub::create($validated);
        return response()->json($hub, 201);
    }

    public function show($id)
    {
        $hub = Hub::with(['hubLead:id,name,email', 'client:id,name,color_code', 'comments'])->findOrFail($id);
        return response()->json($hub);
    }
}
