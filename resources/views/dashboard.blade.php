@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
<div class="space-y-8">
    <!-- En-tête avec sélecteurs de période -->
    <div class="bg-gradient-to-r from-blue-600 to-blue-700 rounded-xl shadow-lg p-6 text-white">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
            <div class="mb-4 lg:mb-0">
                <h1 class="text-3xl font-bold mb-2">
                    @if($viewType === 'general')
                        Tableau de bord général
                    @else
                        Tableau de bord - {{ $selectedDate->translatedFormat('F Y') }}
                    @endif
                </h1>
                <p class="text-blue-100">
                    @if($viewType === 'general')
                        Vue d'ensemble de toutes vos données
                    @else
                        Statistiques et activités du mois sélectionné
                    @endif
                </p>
            </div>

            <div class="flex flex-col sm:flex-row items-start sm:items-center space-y-4 sm:space-y-0 sm:space-x-4">
                <!-- Bouton Vue Générale -->
                <button id="general-view-btn" class="bg-white/10 hover:bg-white/20 rounded-lg p-3 transition-colors">
                    <span class="text-sm font-medium text-blue-100">Vue Générale</span>
                </button>

                <!-- Sélecteur d'année -->
                <div class="bg-white/10 backdrop-blur-sm rounded-lg p-3">
                    <label for="year-selector" class="block text-sm font-medium text-blue-100 mb-1">
                        Année
                    </label>
                    <select id="year-selector" class="bg-white/20 backdrop-blur-sm border border-white/30 text-white rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-white/50 focus:border-white/50">
                        @foreach($availableYears as $year)
                            <option value="{{ $year }}" {{ $year == $selectedYear && $viewType !== 'general' ? 'selected' : '' }}>
                                {{ $year }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Sélecteur de mois -->
                <div class="bg-white/10 backdrop-blur-sm rounded-lg p-3">
                    <label for="month-selector" class="block text-sm font-medium text-blue-100 mb-1">
                        Mois
                    </label>
                    <select id="month-selector" class="bg-white/20 backdrop-blur-sm border border-white/30 text-white rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-white/50 focus:border-white/50">
                        @foreach($availableMonths as $monthNum => $monthName)
                            <option value="{{ $monthNum }}" {{ $monthNum == $selectedMonth && $viewType !== 'general' ? 'selected' : '' }}>
                                {{ $monthName }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistiques principales -->
    @if($viewType === 'general')
        <!-- Vue générale -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-blue-500 hover:shadow-xl transition-shadow duration-300">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <p class="text-sm font-semibold text-gray-600 uppercase tracking-wide">Total Factures</p>
                        <p class="text-3xl font-bold text-gray-900 mt-2">{{ number_format($stats['total_invoices']) }}</p>
                    </div>
                    <div class="p-3 bg-blue-100 rounded-full">
                        <i class="fas fa-file-invoice text-2xl text-blue-600"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-green-500 hover:shadow-xl transition-shadow duration-300">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <p class="text-sm font-semibold text-gray-600 uppercase tracking-wide">Total Proformas</p>
                        <p class="text-3xl font-bold text-gray-900 mt-2">{{ number_format($stats['total_proformas']) }}</p>
                    </div>
                    <div class="p-3 bg-green-100 rounded-full">
                        <i class="fas fa-file-alt text-2xl text-green-600"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-purple-500 hover:shadow-xl transition-shadow duration-300">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <p class="text-sm font-semibold text-gray-600 uppercase tracking-wide">Total Clients</p>
                        <p class="text-3xl font-bold text-gray-900 mt-2">{{ number_format($stats['total_clients']) }}</p>
                    </div>
                    <div class="p-3 bg-purple-100 rounded-full">
                        <i class="fas fa-users text-2xl text-purple-600"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-yellow-500 hover:shadow-xl transition-shadow duration-300">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <p class="text-sm font-semibold text-gray-600 uppercase tracking-wide">Total Produits</p>
                        <p class="text-3xl font-bold text-gray-900 mt-2">{{ number_format($stats['total_products']) }}</p>
                    </div>
                    <div class="p-3 bg-yellow-100 rounded-full">
                        <i class="fas fa-box text-2xl text-yellow-600"></i>
                    </div>
                </div>
            </div>
        </div>
    @else
        <!-- Vue mensuelle -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6">
            <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-emerald-500 hover:shadow-xl transition-shadow duration-300">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <p class="text-sm font-semibold text-gray-600 uppercase tracking-wide">Revenus</p>
                        <p class="text-2xl font-bold text-gray-900 mt-2">{{ number_format($monthlyStats['monthly_revenue'], 0, ',', ' ') }}</p>
                        <p class="text-xs text-gray-500 mt-1">FCFA</p>
                    </div>
                    <div class="p-3 bg-emerald-100 rounded-full">
                        <i class="fas fa-dollar-sign text-2xl text-emerald-600"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-blue-500 hover:shadow-xl transition-shadow duration-300">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <p class="text-sm font-semibold text-gray-600 uppercase tracking-wide">Factures</p>
                        <p class="text-2xl font-bold text-gray-900 mt-2">{{ number_format($monthlyStats['monthly_invoices']) }}</p>
                        <p class="text-xs text-gray-500 mt-1">Ce mois</p>
                    </div>
                    <div class="p-3 bg-blue-100 rounded-full">
                        <i class="fas fa-file-invoice text-2xl text-blue-600"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-green-500 hover:shadow-xl transition-shadow duration-300">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <p class="text-sm font-semibold text-gray-600 uppercase tracking-wide">Proformas</p>
                        <p class="text-2xl font-bold text-gray-900 mt-2">{{ number_format($monthlyStats['monthly_proformas']) }}</p>
                        <p class="text-xs text-gray-500 mt-1">Ce mois</p>
                    </div>
                    <div class="p-3 bg-green-100 rounded-full">
                        <i class="fas fa-file-alt text-2xl text-green-600"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-purple-500 hover:shadow-xl transition-shadow duration-300">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <p class="text-sm font-semibold text-gray-600 uppercase tracking-wide">Clients</p>
                        <p class="text-2xl font-bold text-gray-900 mt-2">{{ number_format($monthlyStats['monthly_clients']) }}</p>
                        <p class="text-xs text-gray-500 mt-1">Nouveaux</p>
                    </div>
                    <div class="p-3 bg-purple-100 rounded-full">
                        <i class="fas fa-users text-2xl text-purple-600"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-yellow-500 hover:shadow-xl transition-shadow duration-300">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <p class="text-sm font-semibold text-gray-600 uppercase tracking-wide">Produits</p>
                        <p class="text-2xl font-bold text-gray-900 mt-2">{{ number_format($monthlyStats['monthly_products']) }}</p>
                        <p class="text-xs text-gray-500 mt-1">Nouveaux</p>
                    </div>
                    <div class="p-3 bg-yellow-100 rounded-full">
                        <i class="fas fa-box text-2xl text-yellow-600"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistiques annuelles -->
        <div class="bg-white rounded-xl shadow-lg p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-bold text-gray-900">Statistiques annuelles {{ $selectedYear }}</h3>
                <div class="flex items-center space-x-2">
                    <div class="w-3 h-3 bg-blue-500 rounded-full"></div>
                    <span class="text-sm text-gray-600">Année {{ $selectedYear }}</span>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                <div class="bg-gradient-to-br from-emerald-50 to-emerald-100 rounded-lg p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-emerald-700">Revenus annuels</p>
                            <p class="text-xl font-bold text-emerald-900">{{ number_format($yearlyStats['yearly_revenue'], 0, ',', ' ') }}</p>
                            <p class="text-xs text-emerald-600">FCFA</p>
                        </div>
                        <i class="fas fa-chart-line text-emerald-600"></i>
                    </div>
                </div>

                <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-lg p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-blue-700">Factures</p>
                            <p class="text-xl font-bold text-blue-900">{{ number_format($yearlyStats['yearly_invoices']) }}</p>
                        </div>
                        <i class="fas fa-file-invoice text-blue-600"></i>
                    </div>
                </div>

                <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-lg p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-green-700">Proformas</p>
                            <p class="text-xl font-bold text-green-900">{{ number_format($yearlyStats['yearly_proformas']) }}</p>
                        </div>
                        <i class="fas fa-file-alt text-green-600"></i>
                    </div>
                </div>

                <div class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-lg p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-purple-700">Clients</p>
                            <p class="text-xl font-bold text-purple-900">{{ number_format($yearlyStats['yearly_clients']) }}</p>
                        </div>
                        <i class="fas fa-users text-purple-600"></i>
                    </div>
                </div>

                <div class="bg-gradient-to-br from-yellow-50 to-yellow-100 rounded-lg p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-yellow-700">Produits</p>
                            <p class="text-xl font-bold text-yellow-900">{{ number_format($yearlyStats['yearly_products']) }}</p>
                        </div>
                        <i class="fas fa-box text-yellow-600"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistiques générales condensées -->
        <div class="bg-white rounded-xl shadow-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                <i class="fas fa-chart-bar text-gray-600 mr-2"></i>
                Statistiques générales
            </h3>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="text-center p-4 bg-gray-50 rounded-lg">
                    <div class="text-2xl font-bold text-gray-900">{{ number_format($stats['total_invoices']) }}</div>
                    <div class="text-sm text-gray-600">Total factures</div>
                </div>
                <div class="text-center p-4 bg-gray-50 rounded-lg">
                    <div class="text-2xl font-bold text-gray-900">{{ number_format($stats['total_proformas']) }}</div>
                    <div class="text-sm text-gray-600">Total proformas</div>
                </div>
                <div class="text-center p-4 bg-gray-50 rounded-lg">
                    <div class="text-2xl font-bold text-gray-900">{{ number_format($stats['total_clients']) }}</div>
                    <div class="text-sm text-gray-600">Total clients</div>
                </div>
                <div class="text-center p-4 bg-gray-50 rounded-lg">
                    <div class="text-2xl font-bold text-gray-900">{{ number_format($stats['total_products']) }}</div>
                    <div class="text-sm text-gray-600">Total produits</div>
                </div>
            </div>
        </div>
    @endif

    <!-- Indicateurs de statut -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-orange-500 hover:shadow-xl transition-shadow duration-300">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-sm font-semibold text-gray-600 uppercase tracking-wide">Factures en attente</p>
                    <p class="text-3xl font-bold text-orange-600 mt-2">{{ number_format($stats['pending_invoices']) }}</p>
                    <p class="text-xs text-gray-500 mt-1">À traiter</p>
                </div>
                <div class="p-3 bg-orange-100 rounded-full">
                    <i class="fas fa-clock text-2xl text-orange-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-red-500 hover:shadow-xl transition-shadow duration-300">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-sm font-semibold text-gray-600 uppercase tracking-wide">Factures en retard</p>
                    <p class="text-3xl font-bold text-red-600 mt-2">{{ number_format($stats['overdue_invoices']) }}</p>
                    <p class="text-xs text-gray-500 mt-1">Urgentes</p>
                </div>
                <div class="p-3 bg-red-100 rounded-full">
                    <i class="fas fa-exclamation-triangle text-2xl text-red-600"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Activités récentes -->
    <div class="grid grid-cols-1 xl:grid-cols-2 gap-8">
        <!-- Factures récentes -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <div class="bg-gradient-to-r from-blue-500 to-blue-600 px-6 py-4">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-white flex items-center">
                        <i class="fas fa-file-invoice mr-2"></i>
                        Factures récentes
                        @if($viewType !== 'general')
                            <span class="text-sm text-blue-100 ml-2">({{ $selectedDate->translatedFormat('F Y') }})</span>
                        @endif
                    </h3>
                    <a href="{{ route('invoices.index') }}" class="text-sm text-blue-100 hover:text-white transition-colors flex items-center">
                        Voir tout <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
            </div>

            <div class="max-h-96 overflow-y-auto">
                @forelse($recent_invoices as $invoice)
                <div class="px-6 py-4 border-b border-gray-100 hover:bg-gray-50 transition-colors">
                    <div class="flex items-center justify-between">
                        <div class="flex-1">
                            <p class="text-sm font-semibold text-gray-900">{{ $invoice->number }}</p>
                            <p class="text-sm text-gray-600 flex items-center mt-1">
                                <i class="fas fa-user text-xs mr-1"></i>
                                {{ $invoice->client->name }}
                            </p>
                            <p class="text-xs text-gray-500 mt-1">
                                {{ $invoice->created_at->diffForHumans() }}
                            </p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-bold text-gray-900">{{ number_format($invoice->total, 0, ',', ' ') }} FCFA</p>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium mt-1
                                {{ $invoice->status === 'paid' ? 'bg-green-100 text-green-800' :
                                   ($invoice->status === 'sent' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800') }}">
                                {{ ucfirst($invoice->status) }}
                            </span>
                        </div>
                    </div>
                </div>
                @empty
                <div class="px-6 py-12 text-center">
                    <div class="w-16 h-16 mx-auto bg-gray-100 rounded-full flex items-center justify-center mb-4">
                        <i class="fas fa-file-invoice text-2xl text-gray-400"></i>
                    </div>
                    <p class="text-gray-500 font-medium">
                        @if($viewType === 'general')
                            Aucune facture récente
                        @else
                            Aucune facture pour ce mois
                        @endif
                    </p>
                    <p class="text-gray-400 text-sm mt-1">Les factures apparaîtront ici</p>
                </div>
                @endforelse
            </div>
        </div>

        <!-- Proformas récents -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <div class="bg-gradient-to-r from-green-500 to-green-600 px-6 py-4">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-white flex items-center">
                        <i class="fas fa-file-alt mr-2"></i>
                        Proformas récents
                        @if($viewType !== 'general')
                            <span class="text-sm text-green-100 ml-2">({{ $selectedDate->translatedFormat('F Y') }})</span>
                        @endif
                    </h3>
                    <a href="{{ route('proformas.index') }}" class="text-sm text-green-100 hover:text-white transition-colors flex items-center">
                        Voir tout <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
            </div>

            <div class="max-h-96 overflow-y-auto">
                @forelse($recent_proformas as $proforma)
                <div class="px-6 py-4 border-b border-gray-100 hover:bg-gray-50 transition-colors">
                    <div class="flex items-center justify-between">
                        <div class="flex-1">
                            <p class="text-sm font-semibold text-gray-900">{{ $proforma->number }}</p>
                            <p class="text-sm text-gray-600 flex items-center mt-1">
                                <i class="fas fa-user text-xs mr-1"></i>
                                {{ $proforma->client->name }}
                            </p>
                            <p class="text-xs text-gray-500 mt-1">
                                {{ $proforma->created_at->diffForHumans() }}
                            </p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-bold text-gray-900">{{ number_format($proforma->total, 0, ',', ' ') }} FCFA</p>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium mt-1
                                {{ $proforma->status === 'accepted' ? 'bg-green-100 text-green-800' :
                                   ($proforma->status === 'sent' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800') }}">
                                {{ ucfirst($proforma->status) }}
                            </span>
                        </div>
                    </div>
                </div>
                @empty
                <div class="px-6 py-12 text-center">
                    <div class="w-16 h-16 mx-auto bg-gray-100 rounded-full flex items-center justify-center mb-4">
                        <i class="fas fa-file-alt text-2xl text-gray-400"></i>
                    </div>
                    <p class="text-gray-500 font-medium">
                        @if($viewType === 'general')
                            Aucun proforma récent
                        @else
                            Aucun proforma pour ce mois
                        @endif
                    </p>
                    <p class="text-gray-400 text-sm mt-1">Les proformas apparaîtront ici</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Actions rapides -->
    <div class="bg-white rounded-xl shadow-lg p-6">
        <h3 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
            <i class="fas fa-bolt text-yellow-500 mr-2"></i>
            Actions rapides
        </h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <a href="{{ route('invoices.create') }}" class="group relative overflow-hidden bg-gradient-to-r from-blue-500 to-blue-600 rounded-xl p-6 text-white hover:from-blue-600 hover:to-blue-700 transition-all duration-300 transform hover:scale-105">
                <div class="absolute inset-0 bg-white/10 transform -skew-x-12 -translate-x-full group-hover:translate-x-full transition-transform duration-700"></div>
                <div class="relative text-center">
                    <i class="fas fa-plus-circle text-3xl mb-3"></i>
                    <p class="font-semibold">Nouvelle facture</p>
                    <p class="text-xs text-blue-100 mt-1">Créer une facture</p>
                </div>
            </a>

            <a href="{{ route('proformas.create') }}" class="group relative overflow-hidden bg-gradient-to-r from-green-500 to-green-600 rounded-xl p-6 text-white hover:from-green-600 hover:to-green-700 transition-all duration-300 transform hover:scale-105">
                <div class="absolute inset-0 bg-white/10 transform -skew-x-12 -translate-x-full group-hover:translate-x-full transition-transform duration-700"></div>
                <div class="relative text-center">
                    <i class="fas fa-plus-circle text-3xl mb-3"></i>
                    <p class="font-semibold">Nouveau proforma</p>
                    <p class="text-xs text-green-100 mt-1">Créer un devis</p>
                </div>
            </a>

            <a href="{{ route('clients.create') }}" class="group relative overflow-hidden bg-gradient-to-r from-purple-500 to-purple-600 rounded-xl p-6 text-white hover:from-purple-600 hover:to-purple-700 transition-all duration-300 transform hover:scale-105">
                <div class="absolute inset-0 bg-white/10 transform -skew-x-12 -translate-x-full group-hover:translate-x-full transition-transform duration-700"></div>
                <div class="relative text-center">
                    <i class="fas fa-user-plus text-3xl mb-3"></i>
                    <p class="font-semibold">Nouveau client</p>
                    <p class="text-xs text-purple-100 mt-1">Ajouter un client</p>
                </div>
            </a>

            <a href="{{ route('products.create') }}" class="group relative overflow-hidden bg-gradient-to-r from-yellow-500 to-yellow-600 rounded-xl p-6 text-white hover:from-yellow-600 hover:to-yellow-700 transition-all duration-300 transform hover:scale-105">
                <div class="absolute inset-0 bg-white/10 transform -skew-x-12 -translate-x-full group-hover:translate-x-full transition-transform duration-700"></div>
                <div class="relative text-center">
                    <i class="fas fa-box text-3xl mb-3"></i>
                    <p class="font-semibold">Nouveau produit</p>
                    <p class="text-xs text-yellow-100 mt-1">Ajouter un produit</p>
                </div>
            </a>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const yearSelector = document.getElementById('year-selector');
    const monthSelector = document.getElementById('month-selector');
    const generalViewBtn = document.getElementById('general-view-btn');

    // Gestion du changement d'année
    yearSelector.addEventListener('change', function() {
        updateView('monthly', monthSelector.value, this.value);
    });

    // Gestion du changement de mois
    monthSelector.addEventListener('change', function() {
        updateView('monthly', this.value, yearSelector.value);
    });

    // Gestion du bouton Vue Générale
    generalViewBtn.addEventListener('click', function() {
        updateView('general');
    });

    function updateView(viewType, month = null, year = null) {
        // Ajouter un indicateur de chargement
        const loader = document.createElement('div');
        loader.className = 'fixed inset-0 bg-black/50 flex items-center justify-center z-50';
        loader.innerHTML = `
            <div class="bg-white rounded-lg p-6 flex items-center space-x-3">
                <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-blue-600"></div>
                <span class="text-gray-700">Chargement...</span>
            </div>
        `;
        document.body.appendChild(loader);

        // Créer l'URL de redirection
        const url = new URL(window.location.href);

        if (viewType === 'general') {
            url.searchParams.set('view', 'general');
            url.searchParams.delete('month');
            url.searchParams.delete('year');
        } else {
            url.searchParams.set('view', 'monthly');
            url.searchParams.set('month', month);
            url.searchParams.set('year', year);
        }

        // Effectuer la redirection
        window.location.href = url.toString();
    }

    // Animation des cartes de statistiques
    const statCards = document.querySelectorAll('.hover\\:shadow-xl');
    statCards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-2px)';
        });
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
        });
    });

    // Animation des cartes d'actions rapides
    const actionCards = document.querySelectorAll('.hover\\:scale-105');
    actionCards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'scale(1.05)';
        });
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'scale(1)';
        });
    });

    // Gestion des tooltips pour les statuts
    const statusBadges = document.querySelectorAll('.inline-flex.items-center.px-2\\.5');
    statusBadges.forEach(badge => {
        const status = badge.textContent.trim().toLowerCase();
        let tooltipText = '';

        switch(status) {
            case 'paid': tooltipText = 'Facture payée'; break;
            case 'sent': tooltipText = 'Facture envoyée'; break;
            case 'draft': tooltipText = 'Brouillon'; break;
            case 'accepted': tooltipText = 'Proforma accepté'; break;
            default: tooltipText = 'Statut: ' + status;
        }

        badge.setAttribute('title', tooltipText);
    });

    // Effet de ripple au clic
    const interactiveElements = document.querySelectorAll('a, button, .cursor-pointer');
    interactiveElements.forEach(element => {
        element.addEventListener('click', function(e) {
            const ripple = document.createElement('span');
            ripple.className = 'absolute inset-0 bg-white/20 rounded-full scale-0 animate-ping';

            const rect = this.getBoundingClientRect();
            const size = Math.max(rect.width, rect.height);

            ripple.style.width = size + 'px';
            ripple.style.height = size + 'px';
            ripple.style.left = (e.clientX - rect.left - size/2) + 'px';
            ripple.style.top = (e.clientY - rect.top - size/2) + 'px';

            if (this.style.position !== 'relative' && this.style.position !== 'absolute') {
                this.style.position = 'relative';
            }

            this.appendChild(ripple);

            setTimeout(() => {
                ripple.remove();
            }, 600);
        });
    });

    console.log('Dashboard initialisé avec succès');
});
</script>
@endsection
