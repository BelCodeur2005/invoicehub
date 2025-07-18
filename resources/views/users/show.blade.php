@extends('layouts.app')
@section('title', 'Détails Utilisateur')
@section('page-title', 'Détails de l\'Utilisateur')
@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Détails de l'Utilisateur</h1>
            <p class="mt-2 text-sm text-gray-600">Informations complètes de {{ $user->name }}</p>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('users.edit', $user) }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors inline-flex items-center">
                <i class="fas fa-edit mr-2"></i>
                Modifier
            </a>
            <a href="{{ route('users.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg transition-colors inline-flex items-center">
                <i class="fas fa-arrow-left mr-2"></i>
                Retour
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- User Information -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="p-6 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">Informations personnelles</h2>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Name -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Nom complet</label>
                            <div class="text-sm text-gray-900">{{ $user->name }}</div>
                        </div>

                        <!-- Email -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Adresse email</label>
                            <div class="text-sm text-gray-900">{{ $user->email }}</div>
                        </div>

                        <!-- Role -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Rôle</label>
                            <div class="text-sm">
                                @if($user->role === 'admin')
                                    <span class="bg-red-100 text-red-800 px-2 py-1 rounded-full text-xs font-medium">Administrateur</span>
                                @elseif($user->role === 'manager')
                                    <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded-full text-xs font-medium">Manager</span>
                                @else
                                    <span class="bg-green-100 text-green-800 px-2 py-1 rounded-full text-xs font-medium">Utilisateur</span>
                                @endif
                            </div>
                        </div>

                        <!-- Status -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Statut</label>
                            <div class="text-sm">
                                @if($user->is_active)
                                    <span class="bg-green-100 text-green-800 px-2 py-1 rounded-full text-xs font-medium">Actif</span>
                                @else
                                    <span class="bg-red-100 text-red-800 px-2 py-1 rounded-full text-xs font-medium">Inactif</span>
                                @endif
                            </div>
                        </div>

                        <!-- Created At -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Date de création</label>
                            <div class="text-sm text-gray-900">{{ $user->created_at->format('d/m/Y H:i') }}</div>
                        </div>

                        <!-- Updated At -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Dernière modification</label>
                            <div class="text-sm text-gray-900">{{ $user->updated_at->format('d/m/Y H:i') }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics -->
        <div class="space-y-6">
            <!-- Stats Cards -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="p-6 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">Statistiques</h2>
                </div>
                <div class="p-6 space-y-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <i class="fas fa-file-invoice text-blue-500 mr-2"></i>
                            <span class="text-sm text-gray-600">Factures</span>
                        </div>
                        <span class="text-lg font-bold text-gray-900">{{ $user->invoices_count ?? 0 }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <i class="fas fa-file-alt text-green-500 mr-2"></i>
                            <span class="text-sm text-gray-600">Proformas</span>
                        </div>
                        <span class="text-lg font-bold text-gray-900">{{ $user->proformas_count ?? 0 }}</span>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="p-6 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">Actions</h2>
                </div>
                <div class="p-6 space-y-3">
                    <a href="{{ route('users.edit', $user) }}" class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors inline-flex items-center justify-center">
                        <i class="fas fa-edit mr-2"></i>
                        Modifier l'utilisateur
                    </a>
                    @if($user->id !== auth()->id())
                        <form action="{{ route('users.destroy', $user) }}" method="POST" class="w-full" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg transition-colors inline-flex items-center justify-center">
                                <i class="fas fa-trash mr-2"></i>
                                Supprimer
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity -->
    @if($user->invoices->count() > 0 || $user->proformas->count() > 0)
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="p-6 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">Activité récente</h2>
        </div>
        <div class="p-6">
            <div class="space-y-4">
                @forelse($user->invoices->take(5) as $invoice)
                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                    <div class="flex items-center">
                        <i class="fas fa-file-invoice text-blue-500 mr-3"></i>
                        <div>
                            <div class="text-sm font-medium text-gray-900">Facture #{{ $invoice->number }}</div>
                            <div class="text-xs text-gray-500">{{ $invoice->created_at->format('d/m/Y H:i') }}</div>
                        </div>
                    </div>
                    <div class="text-sm text-gray-900">{{ number_format($invoice->total_amount, 0, ',', ' ') }} FCFA</div>
                </div>
                @empty
                <div class="text-center py-8 text-gray-500">
                    <i class="fas fa-inbox text-4xl mb-4"></i>
                    <p>Aucune activité récente</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>
    @endif
</div>
@endsection
