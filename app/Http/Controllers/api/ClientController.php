<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ClientStoreRequest;
use App\Http\Requests\ClientUpdateRequest;
use App\Http\Resources\ClientResource;
use App\Models\Client;
use App\Models\User;
use Auth;
use Illuminate\Http\Request;
use Ramsey\Uuid\Type\Integer;

class ClientController extends Controller
{
    public function index()
    {
        $data = Auth::user()->clients()->simplePaginate(10);
        return ClientResource::collection($data);

    }
    public function store(ClientStoreRequest $request)
    {
        $validated = $request->validated();
        $validated['user_id'] = Auth::id();
        $data = Client::create($validated);
        return response()->json([
            'msg' => 'client added successfully',
            'data' => new ClientResource($data)
        ], 201);
    }
    public function show($ClientId)
    {
        $data = Auth::user()->clients()->where('id', $ClientId)->firstOrFail();
        return new ClientResource($data);
    }
    public function update(ClientUpdateRequest $request, $ClientId)
    {
        $client = Auth::user()->clients->where('id', $ClientId)->firstOrFail();
        if (!$client) {
            return response()->json([
                "msg" => "Client not found"
            ], 404);
        }
        $data = $client->update($request->validated());
        return response()->json([
            "msg" => "Client updated successfully",
            "client" => new ClientResource($data)
        ], 200);
    }
    public function destroy($ClientId)
    {
        $client = Auth::user()->clients->where('id', $ClientId)->first();
        if (!$client) {
            return response()->json([
                "msg" => "Client not found"
            ], 404);
        }
        $client->delete();
        return response()->json([
            "msg" => "Client deleted successfully"
        ], 200);
    }
}
