@extends('layouts.app')

@section('title', 'Clients')
@section('page-title', 'Gestion des Clients')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Liste des Clients</h1>
            <p class="mt-2 text-sm text-gray-600">Gérez vos clients et leurs informations</p>
        </div>
        <div class="mt-4 sm:mt-0">
            <a href="{{ route('clients.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors inline-flex items-center">
                <i class="fas fa-plus mr-2"></i>
                Nouveau Client
            </a>
        </div>
    </div>

    <!-- Search and Filter -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <form method="GET" action="{{ route('clients.index') }}">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between space-y-4 md:space-y-0">
                <div class="flex-1 max-w-md">
                    <div class="relative">
                        <input type="text"
                            name="search"
                            value="{{ request('search') }}"
                            placeholder="Rechercher un client..."
                            class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                    </div>
                </div>
                <div class="flex space-x-2">
                    <!-- Menu déroulant pour les filtres -->
                    <div x-data="{ open: false }" class="relative">
                        <button @click="open = !open" type="button" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg transition-colors flex items-center">
                            <i class="fas fa-filter mr-2"></i>
                            Filtres
                        </button>
                        <div x-show="open" @click.away="open = false" class="absolute right-0 mt-2 w-64 bg-white rounded-lg shadow-lg z-10 p-4 border border-gray-200">
                            <div class="space-y-4">
                                {{-- <!-- Filtre par type -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Type de client</label>
                                    <select name="type" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500">
                                        <option value="">Tous types</option>
                                        <option value="particulier" {{ request('type') == 'particulier' ? 'selected' : '' }}>Particulier</option>
                                        <option value="entreprise" {{ request('type') == 'entreprise' ? 'selected' : '' }}>Entreprise</option>
                                    </select>
                                </div> --}}
                                <!-- Filtre par statut -->
                                {{-- <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Statut</label>
                                    <select name="status" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500">
                                        <option value="">Tous statuts</option>
                                        <option value="actif" {{ request('status') == 'actif' ? 'selected' : '' }}>Actif</option>
                                        <option value="inactif" {{ request('status') == 'inactif' ? 'selected' : '' }}>Inactif</option>
                                    </select>
                                </div> --}}
                                <!-- Boutons d'action -->
                                <div class="flex space-x-2 pt-2">
                                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded-lg text-sm">
                                        Appliquer
                                    </button>
                                    <a href="{{ route('clients.index') }}" class="border border-gray-300 hover:bg-gray-100 text-gray-700 px-3 py-1 rounded-lg text-sm">
                                        Réinitialiser
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                   <a href="{{ route('clients.export', request()->query()) }}"
                    class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg transition-colors flex items-center">
                        <i class="fas fa-download mr-2"></i>
                        Exporter
                    </a>
                </div>
            </div>
        </form>
    </div>

    <!-- Clients Table -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Client
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Contact
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Adresse
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Statistiques
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($clients as $client)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center">
                                            <span class="text-blue-600 font-medium text-sm">
                                                {{ strtoupper(substr($client->name, 0, 2)) }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $client->name }}</div>
                                        @if($client->niu)
                                            <div class="text-sm text-gray-500">NIU: {{ $client->niu }}</div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $client->email }}</div>
                                @if($client->phone)
                                    <div class="text-sm text-gray-500">{{ $client->phone }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">
                                    @if($client->city)
                                        {{ $client->city }}
                                    @endif
                                    @if($client->country)
                                        {{ $client->country ? ', ' . $client->country : '' }}
                                    @endif
                                </div>
                                @if($client->address)
                                    <div class="text-sm text-gray-500">{{ Str::limit($client->address, 30) }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex space-x-4">
                                    <div class="text-center">
                                        <div class="text-sm font-medium text-gray-900">{{ $client->invoices_count }}</div>
                                        <div class="text-xs text-gray-500">Factures</div>
                                    </div>
                                    <div class="text-center">
                                        <div class="text-sm font-medium text-gray-900">{{ $client->proformas_count }}</div>
                                        <div class="text-xs text-gray-500">Proformas</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex space-x-2">
                                    <a href="{{ route('clients.show', $client) }}"
                                       class="text-blue-600 hover:text-blue-900 transition-colors"
                                       title="Voir">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('clients.edit', $client) }}"
                                       class="text-yellow-600 hover:text-yellow-900 transition-colors"
                                       title="Modifier">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form method="POST" action="{{ route('clients.destroy', $client) }}"
                                          onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce client ?')"
                                          class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="text-red-600 hover:text-red-900 transition-colors"
                                                title="Supprimer">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center">
                                <div class="text-gray-500">
                                    <i class="fas fa-users text-4xl mb-4"></i>
                                    <p class="text-lg font-medium">Aucun client trouvé</p>
                                    <p class="text-sm mt-2">Commencez par créer votre premier client</p>
                                    <a href="{{ route('clients.create') }}" class="mt-4 inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors">
                                        <i class="fas fa-plus mr-2"></i>
                                        Créer un client
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($clients->hasPages())
        <div class="bg-white px-4 py-3 border border-gray-200 rounded-md">
            {{ $clients->appends(request()->query())->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
