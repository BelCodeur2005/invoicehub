@extends('layouts.app')

@section('title', 'Client - ' . $client->name)
@section('page-title', 'Détails du Client')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">{{ $client->name }}</h1>
            <p class="mt-2 text-sm text-gray-600">Informations détaillées du client</p>
        </div>
        <div class="flex space-x-2">
            <a href="{{ route('clients.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg transition-colors inline-flex items-center">
                <i class="fas fa-arrow-left mr-2"></i>
                Retour à la liste
            </a>
            <a href="{{ route('clients.edit', $client) }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors inline-flex items-center">
                <i class="fas fa-edit mr-2"></i>
                Modifier
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Client Information -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Basic Information -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Informations générales</h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Nom</label>
                            <p class="text-sm text-gray-900">{{ $client->name }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Email</label>
                            <p class="text-sm text-gray-900">
                                <a href="mailto:{{ $client->email }}" class="text-blue-600 hover:text-blue-800">
                                    {{ $client->email }}
                                </a>
                            </p>
                        </div>
                        @if($client->phone)
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Téléphone</label>
                            <p class="text-sm text-gray-900">
                                <a href="tel:{{ $client->phone }}" class="text-blue-600 hover:text-blue-800">
                                    {{ $client->phone }}
                                </a>
                            </p>
                        </div>
                        @endif
                        @if($client->niu)
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">NIU</label>
                            <p class="text-sm text-gray-900">{{ $client->niu }}</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Company Information -->
            @if($client->rccm || $client->bp)
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Informations entreprise</h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        @if($client->rccm)
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">RCCM</label>
                            <p class="text-sm text-gray-900">{{ $client->rccm }}</p>
                        </div>
                        @endif
                        @if($client->bp)
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Boîte Postale</label>
                            <p class="text-sm text-gray-900">{{ $client->bp }}</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            @endif

            <!-- Address Information -->
            @if($client->address || $client->street || $client->city || $client->country)
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Adresse</h3>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        @if($client->address)
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Adresse complète</label>
                            <p class="text-sm text-gray-900">{{ $client->address }}</p>
                        </div>
                        @endif
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            @if($client->street)
                            <div>
                                <label class="block text-sm font-medium text-gray-500 mb-1">Rue</label>
                                <p class="text-sm text-gray-900">{{ $client->street }}</p>
                            </div>
                            @endif
                            @if($client->city)
                            <div>
                                <label class="block text-sm font-medium text-gray-500 mb-1">Ville</label>
                                <p class="text-sm text-gray-900">{{ $client->city }}</p>
                            </div>
                            @endif
                            @if($client->country)
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-500 mb-1">Pays</label>
                                <p class="text-sm text-gray-900">{{ $client->country }}</p>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Banking Information -->
            @if($client->account_number || $client->bank)
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Informations bancaires</h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        @if($client->account_number)
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Numéro de compte</label>
                            <p class="text-sm text-gray-900">{{ $client->account_number }}</p>
                        </div>
                        @endif
                        @if($client->bank)
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Banque</label>
                            <p class="text-sm text-gray-900">{{ $client->bank }}</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Statistics -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Statistiques</h3>
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="p-2 bg-blue-100 rounded-lg">
                                    <i class="fas fa-file-invoice text-blue-600"></i>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-gray-900">{{ $client->invoices->count() }}</p>
                                    <p class="text-xs text-gray-500">Factures</p>
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="p-2 bg-green-100 rounded-lg">
                                    <i class="fas fa-file-contract text-green-600"></i>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-gray-900">{{ $client->proformas->count() }}</p>
                                    <p class="text-xs text-gray-500">Proformas</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Actions rapides</h3>
                    <div class="space-y-2">
                        <a href="{{ route('invoices.create', ['client_id' => $client->id]) }}" class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors inline-flex items-center justify-center">
                            <i class="fas fa-plus mr-2"></i>
                            Nouvelle facture
                        </a>
                        <a href="{{ route('proformas.create', ['client_id' => $client->id]) }}" class="w-full bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition-colors inline-flex items-center justify-center">
                            <i class="fas fa-plus mr-2"></i>
                            Nouveau proforma
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Invoices -->
    @if($client->invoices->count() > 0)
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="p-6 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-medium text-gray-900">Factures récentes</h3>
                <a href="{{ route('invoices.index', ['client_id' => $client->id]) }}" class="text-blue-600 hover:text-blue-700 text-sm font-medium">
                    Voir toutes <i class="fas fa-arrow-right ml-1"></i>
                </a>
            </div>
        </div>
        <div class="divide-y divide-gray-200">
            @foreach($client->invoices->take(5) as $invoice)
            <div class="p-4 hover:bg-gray-50 transition-colors">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <div class="flex items-center">
                            <span class="text-sm font-medium text-gray-900">{{ $invoice->number }}</span>
                            <span class="ml-2 px-2 py-1 text-xs rounded-full
                                @if($invoice->status === 'paid') bg-green-100 text-green-800
                                @elseif($invoice->status === 'sent') bg-blue-100 text-blue-800
                                @else bg-gray-100 text-gray-800
                                @endif">
                                {{ ucfirst($invoice->status) }}
                            </span>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">{{ $invoice->created_at->format('d/m/Y') }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-semibold text-gray-900">{{ number_format($invoice->total, 0, ',', ' ') }} FCFA</p>
                        <a href="{{ route('invoices.show', $invoice) }}" class="text-xs text-blue-600 hover:text-blue-800">
                            Voir détails
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Recent Proformas -->
    @if($client->proformas->count() > 0)
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="p-6 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-medium text-gray-900">Proformas récents</h3>
                <a href="{{ route('proformas.index', ['client_id' => $client->id]) }}" class="text-blue-600 hover:text-blue-700 text-sm font-medium">
                    Voir tous <i class="fas fa-arrow-right ml-1"></i>
                </a>
            </div>
        </div>
        <div class="divide-y divide-gray-200">
            @foreach($client->proformas->take(5) as $proforma)
            <div class="p-4 hover:bg-gray-50 transition-colors">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <div class="flex items-center">
                            <span class="text-sm font-medium text-gray-900">{{ $proforma->number }}</span>
                            <span class="ml-2 px-2 py-1 text-xs rounded-full
                                @if($proforma->status === 'accepted') bg-green-100 text-green-800
                                @elseif($proforma->status === 'sent') bg-blue-100 text-blue-800
                                @else bg-gray-100 text-gray-800
                                @endif">
                                {{ ucfirst($proforma->status) }}
                            </span>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">{{ $proforma->created_at->format('d/m/Y') }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-semibold text-gray-900">{{ number_format($proforma->total, 0, ',', ' ') }} FCFA</p>
                        <a href="{{ route('proformas.show', $proforma) }}" class="text-xs text-blue-600 hover:text-blue-800">
                            Voir détails
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif
</div>
@endsection
