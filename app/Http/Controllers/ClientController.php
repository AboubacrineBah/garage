<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreClientRequest;
use App\Models\Client;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ClientController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Client::class);

        $query = Client::withCount('vehicules', 'ordres')->latest();

        if ($request->filled('search')) {
            $search = mb_substr(trim((string) $request->search), 0, 80);
            if ($search !== '') {
                $query->where(function ($q) use ($search) {
                    $q->where('nom', 'like', "%{$search}%")
                        ->orWhere('prenom', 'like', "%{$search}%")
                        ->orWhere('telephone', 'like', "%{$search}%");
                });
            }
        }

        $clients = $query->paginate(20)->withQueryString();

        return view('clients.index', compact('clients'));
    }

    public function create(): View
    {
        $this->authorize('create', Client::class);

        return view('clients.create');
    }

    public function store(StoreClientRequest $request): RedirectResponse
    {
        $this->authorize('create', Client::class);

        $client = Client::create(array_merge(
            $request->validated(),
            ['created_by' => auth()->id()]
        ));

        return redirect()
            ->route('clients.show', $client)
            ->with('success', 'Client créé avec succès.');
    }

    public function show(Client $client): View
    {
        $this->authorize('view', $client);

        $client->load(['vehicules', 'ordres' => fn ($q) => $q->latest()->take(10)]);

        return view('clients.show', compact('client'));
    }

    public function edit(Client $client): View
    {
        $this->authorize('update', $client);

        return view('clients.edit', compact('client'));
    }

    public function update(StoreClientRequest $request, Client $client): RedirectResponse
    {
        $this->authorize('update', $client);

        $client->update($request->validated());

        return redirect()
            ->route('clients.show', $client)
            ->with('success', 'Client mis à jour.');
    }
}
