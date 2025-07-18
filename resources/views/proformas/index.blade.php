@extends('layouts.app')
@section('title', 'Proformas')
@section('page-title', 'Gestion des Proformas')
@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Proformas</h1>
            <p class="mt-2 text-sm text-gray-600">Gérez vos proformas et devis</p>
        </div>
        <a href="{{ route('proformas.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors inline-flex items-center">
            <i class="fas fa-plus mr-2"></i>
            Nouveau Proforma
        </a>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
        <form method="GET" action="{{ route('proformas.index') }}">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label for="search" class="block text-sm font-medium text-gray-700 mb-2">Rechercher</label>
                    <input type="text"
                        id="search"
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="Numéro, client..."
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Statut</label>
                    <select id="status"
                            name="status"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">Tous les statuts</option>
                        <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Brouillon</option>
                        <option value="sent" {{ request('status') == 'sent' ? 'selected' : '' }}>Envoyé</option>
                        <option value="accepted" {{ request('status') == 'accepted' ? 'selected' : '' }}>Accepté</option>
                        <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejeté</option>
                        <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>Expiré</option>
                    </select>
                </div>
                <div>
                    <label for="date_from" class="block text-sm font-medium text-gray-700 mb-2">Date de</label>
                    <input type="date"
                        id="date_from"
                        name="date_from"
                        value="{{ request('date_from') }}"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
                <div>
                    <label for="date_to" class="block text-sm font-medium text-gray-700 mb-2">Date à</label>
                    <input type="date"
                        id="date_to"
                        name="date_to"
                        value="{{ request('date_to') }}"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
            </div>
            <div class="mt-4 flex space-x-2">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center">
                    <i class="fas fa-search mr-2"></i>
                    Rechercher
                </button>
                <a href="{{ route('proformas.index') }}" class="border border-gray-300 hover:bg-gray-100 text-gray-700 px-4 py-2 rounded-lg flex items-center">
                    <i class="fas fa-undo mr-2"></i>
                    Réinitialiser
                </a>
            </div>
        </form>
    </div>

    <!-- Proformas List -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="p-6 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">Liste des Proformas</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Numéro
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Client
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Date
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Valide jusqu'au
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Montant
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Statut
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($proformas as $proforma)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="text-sm font-medium text-gray-900">
                                    {{ $proforma->number }}
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $proforma->client->name }}</div>
                            <div class="text-sm text-gray-500">{{ $proforma->client->email }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ \Carbon\Carbon::parse($proforma->date)->format('d/m/Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ \Carbon\Carbon::parse($proforma->valid_until)->format('d/m/Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ number_format($proforma->total, 0, ',', ' ') }} FCFA
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @php
                                $statusClasses = [
                                    'draft' => 'bg-gray-100 text-gray-800',
                                    'sent' => 'bg-blue-100 text-blue-800',
                                    'accepted' => 'bg-green-100 text-green-800',
                                    'rejected' => 'bg-red-100 text-red-800',
                                ];
                                $statusLabels = [
                                    'draft' => 'Non Envoyée',
                                    'sent' => 'Envoyé',
                                    'accepted' => 'Accepté',
                                    'rejected' => 'Rejeté',
                                ];
                            @endphp
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusClasses[$proforma->status] }}">
                                {{ $statusLabels[$proforma->status] }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex items-center space-x-2">
                                <a href="{{ route('proformas.show', $proforma) }}" class="text-blue-600 hover:text-blue-900" title="Voir">
                                    <i class="fas fa-eye"></i>
                                </a>
                                {{-- @if($proforma->status === 'draft') --}}
                                <a href="{{ route('proformas.edit', $proforma) }}" class="text-indigo-600 hover:text-indigo-900" title="Modifier">
                                    <i class="fas fa-edit"></i>
                                </a>
                                {{-- @endif --}}
                                <!-- PDF -->
                                <button id="pdfButton"
                                        class="text-red-600 hover:text-red-900"
                                        title="Générer PDF">
                                    <i class="fas fa-file-pdf"></i>
                                </button>
                                <!-- Email -->
                                <button type="button"
                                        onclick="openEmailModal({{ $proforma->id }}, '{{ $proforma->client->email }}', '{{ $proforma->number }}')"
                                        class="text-purple-600 hover:text-purple-900"
                                        title="Envoyer par email">
                                    <i class="fas fa-envelope"></i>
                                </button>
                                {{-- @if($proforma->status === 'accepted' && !$proforma->invoice) --}}
                                <form action="{{ route('proformas.convert', $proforma) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="text-green-600 hover:text-green-900" title="Convertir en facture">
                                        <i class="fas fa-file-invoice"></i>
                                    </button>
                                </form>
                                {{-- @endif --}}
                                {{-- @if($proforma->status === 'draft') --}}
                                <form action="{{ route('proformas.destroy', $proforma) }}" method="POST" class="inline" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce proforma ?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900" title="Supprimer">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                                {{-- @endif --}}
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                            <div class="flex flex-col items-center justify-center py-8">
                                <i class="fas fa-file-invoice text-4xl text-gray-300 mb-4"></i>
                                <p class="text-lg font-medium text-gray-900 mb-2">Aucun proforma trouvé</p>
                                <p class="text-gray-500 mb-4">Commencez par créer votre premier proforma</p>
                                <a href="{{ route('proformas.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors inline-flex items-center">
                                    <i class="fas fa-plus mr-2"></i>
                                    Créer un proforma
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($proformas->hasPages())
        <div class="bg-white px-4 py-3 border border-gray-200 rounded-md">
            {{ $proformas->appends(request()->query())->links() }}
        </div>
        @endif
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-file-alt text-gray-600"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total Proformas</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $proformas->total() }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-paper-plane text-blue-600"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Envoyés</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $proformas->where('status', 'sent')->count() }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-check text-green-600"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Acceptés</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $proformas->where('status', 'accepted')->count() }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-yellow-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-clock text-yellow-600"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">En attente</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $proformas->where('status', 'draft')->count() }}</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal pour l'envoi d'email -->
<div id="emailModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50 flex items-center justify-center">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-md mx-4">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Envoyer la proforma par email</h3>
        </div>

        <form id="emailForm" method="POST">
            @csrf
            <div class="px-6 py-4 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Destinataire principal</label>
                    <input type="email" id="mainEmail" name="to" readonly
                           class="w-full border border-gray-300 rounded-md px-3 py-2 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">CC (optionnel)</label>
                    <input type="email" name="cc" placeholder="email@example.com"
                           class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <p class="text-xs text-gray-500 mt-1">Séparez plusieurs emails par des virgules</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">BCC (optionnel)</label>
                    <input type="email" name="bcc" placeholder="email@example.com"
                           class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <p class="text-xs text-gray-500 mt-1">Séparez plusieurs emails par des virgules</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Sujet</label>
                    <input type="text" name="subject" id="emailSubject"
                           class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Message (optionnel)</label>
                    <textarea name="message" rows="3" placeholder="Message personnalisé..."
                              class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                </div>
            </div>

            <div class="px-6 py-4 border-t border-gray-200 flex justify-end space-x-3">
                <button type="button" onclick="closeEmailModal()"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 rounded-md hover:bg-gray-300">
                    Annuler
                </button>
                <button type="submit" id="sendEmailBtn"
                        class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700">
                    <i class="fas fa-envelope mr-2"></i>Envoyer
                </button>
            </div>
        </form>
    </div>
</div>
<script>
    // Fonction pour ouvrir la modal d'email
    function openEmailModal(proformaId, clientEmail, proformaNumber) {
        const modal = document.getElementById('emailModal');
        const form = document.getElementById('emailForm');
        const mainEmail = document.getElementById('mainEmail');
        const subject = document.getElementById('emailSubject');

        // Configurer le formulaire
        form.action = `/proformas/${proformaId}/send`;

        // Remplir les champs
        mainEmail.value = clientEmail;
        subject.value = `Proforma ${proformaNumber}`;

        // Afficher la modal
        modal.classList.remove('hidden');

        // Focus sur le champ CC
        document.querySelector('input[name="cc"]').focus();
    }

    // Fonction pour fermer la modal
    function closeEmailModal() {
        const modal = document.getElementById('emailModal');
        modal.classList.add('hidden');

        // Réinitialiser le formulaire
        document.getElementById('emailForm').reset();
    }

    // Fermer la modal en cliquant à l'extérieur
    document.getElementById('emailModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeEmailModal();
        }
    });

    // Gérer l'envoi du formulaire
    document.getElementById('emailForm').addEventListener('submit', function(e) {
        const sendBtn = document.getElementById('sendEmailBtn');
        sendBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Envoi...';
        sendBtn.disabled = true;
    });
    // Script existant pour le PDF
    document.getElementById('pdfButton').addEventListener('click', function() {
        this.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
        window.location.href = '{{ route('proformas.pdf', $proforma) }}';
    });
</script>
@endsection
