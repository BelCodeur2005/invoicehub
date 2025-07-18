<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Client;
use App\Exports\ClientsExport;
use Maatwebsite\Excel\Facades\Excel;
class ClientController extends Controller
{

    public function index(Request $request)
    {
        $query = Client::withCount(['invoices', 'proformas']);

        // Filtre par recherche
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                ->orWhere('email', 'like', "%$search%")
                ->orWhere('phone', 'like', "%$search%");
            });
        }

        // // Filtre par type de client (optionnel)
        // if ($request->has('type') && $request->type != '') {
        //     $query->where('type', $request->type);
        // }

        // Filtre par statut (optionnel)
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        // Tri des résultats
        $sort = $request->get('sort', 'name');
        $direction = $request->get('direction', 'asc');
        $query->orderBy($sort, $direction);

        $clients = $query->paginate(15);

        return view('clients.index', compact('clients'));
    }

    public function create()
    {
        return view('clients.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:clients,email,' . ($client->id ?? 'NULL'),
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'niu' => 'nullable|string|max:255',
            'rccm' => 'nullable|string|max:255',
            'bp' => 'nullable|string|max:255',
            'account_number' => 'nullable|string|max:255',
            'bank' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:255',
            'street' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
        ]);
        Client::create($validated);

        return redirect()->route('clients.index')
                        ->with('success', 'Client créé avec succès');
    }

    public function show(Client $client)
    {
        $client->load(['invoices', 'proformas']);
        return view('clients.show', compact('client'));
    }

    public function edit(Client $client)
    {
        return view('clients.edit', compact('client'));
    }

    public function update(Request $request, Client $client)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:clients,email,' . $client->id,
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'niu' => 'nullable|string|max:255',
            'rccm' => 'nullable|string|max:255',
            'bp' => 'nullable|string|max:255',
            'account_number' => 'nullable|string|max:255',
            'bank' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:255',
            'street' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
        ]);

        $client->update($validated);

        return redirect()->route('clients.index')
                        ->with('success', 'Client mis à jour avec succès');
    }


    public function destroy(Client $client)
    {
        if ($client->invoices()->count() > 0 || $client->proformas()->count() > 0) {
            return redirect()->route('clients.index')
                            ->with('error', 'Impossible de supprimer ce client car il a des factures ou proformas associées');
        }

        $client->delete();

        return redirect()->route('clients.index')
                        ->with('success', 'Client supprimé avec succès');
    }
    public function export(Request $request)
    {
        $filters = $request->query(); // Récupère tous les paramètres de filtre

        return Excel::download(
            new ClientsExport($filters),
            'clients-export-'.now()->format('Y-m-d').'.xlsx'
        );
    }
}
