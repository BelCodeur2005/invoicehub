{{-- views/invoices/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Factures')
@section('page-title', 'Factures')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900">Factures</h1>
            <p class="text-sm text-gray-600">Gérez vos factures et leur statut</p>
        </div>
        <a href="{{ route('invoices.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md flex items-center">
            <i class="fas fa-plus mr-2"></i>
            Nouvelle facture
        </a>
    </div>

    <!-- Filters -->
    <div class="bg-white p-4 rounded-lg shadow">
        <form method="GET" action="{{ route('invoices.index') }}">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Rechercher</label>
                    <input type="text"
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="Numéro, client..."
                        class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Statut</label>
                    <select name="status" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Tous les statuts</option>
                        <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Brouillon</option>
                        <option value="sent" {{ request('status') == 'sent' ? 'selected' : '' }}>Envoyée</option>
                        <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Payée</option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Annulée</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Date de début</label>
                    <input type="date"
                        name="start_date"
                        value="{{ request('start_date') }}"
                        class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Date de fin</label>
                    <input type="date"
                        name="end_date"
                        value="{{ request('end_date') }}"
                        class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
            </div>
            <div class="mt-4 flex space-x-2">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm">
                    <i class="fas fa-search mr-2"></i>Rechercher
                </button>
                <a href="{{ route('invoices.index') }}" class="border border-gray-300 hover:bg-gray-50 text-gray-700 px-4 py-2 rounded-md text-sm flex items-center">
                    <i class="fas fa-undo mr-2"></i>Réinitialiser
                </a>
            </div>
        </form>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
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
                            Échéance
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Montant
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Statut
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($invoices as $invoice)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="text-sm font-medium text-gray-900">{{ $invoice->number }}</div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $invoice->client->name }}</div>
                            <div class="text-sm text-gray-500">{{ $invoice->client->email }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ \Carbon\Carbon::parse($invoice->date)->format('d/m/Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ \Carbon\Carbon::parse($invoice->due_date)->format('d/m/Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ number_format($invoice->total, 0, ',', ' ') }} FCFA
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $invoice->getStatusBadgeClass() }}">
                                <i class="{{ $invoice->getStatusIcon() }} mr-1"></i>
                                @switch($invoice->status)
                                    @case('draft')
                                        Brouillon
                                        @break
                                    @case('sent')
                                        Envoyée
                                        @break
                                    @case('paid')
                                        Payée
                                        @break
                                    @case('cancelled')
                                        Annulée
                                        @break
                                @endswitch
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex justify-end space-x-2">
                                <!-- View -->
                                <a href="{{ route('invoices.show', $invoice) }}" class="text-blue-600 hover:text-blue-900" title="Voir">
                                    <i class="fas fa-eye"></i>
                                </a>

                                <!-- Edit (seulement si draft) -->
                                @if($invoice->status != 'cancelled')
                                <a href="{{ route('invoices.edit', $invoice) }}" class="text-green-600 hover:text-green-900" title="Modifier">
                                    <i class="fas fa-edit"></i>
                                </a>
                                @endif

                                <!-- PDF -->
                                <a href="{{ route('invoices.pdf', $invoice) }}" class="text-red-600 hover:text-red-900" title="Générer PDF">
                                    <i class="fas fa-file-pdf"></i>
                                </a>

                                <!-- Email -->
                                <button type="button"
                                        onclick="openEmailModal({{ $invoice->id }}, '{{ $invoice->client->email }}', '{{ $invoice->number }}')"
                                        class="text-purple-600 hover:text-purple-900"
                                        title="Envoyer par email">
                                    <i class="fas fa-envelope"></i>
                                </button>

                                <!-- Status History -->
                                <a href="{{ route('invoices.status-history', $invoice) }}" class="text-indigo-600 hover:text-indigo-900" title="Historique des statuts">
                                    <i class="fas fa-history"></i>
                                </a>

                                <!-- Status Actions avec modales -->
                                @if($invoice->status === 'draft')
                                <button type="button"
                                        onclick="openStatusModal('markAsSentModal', {{ $invoice->id }}, 'mark-as-sent')"
                                        class="text-blue-600 hover:text-blue-900"
                                        title="Marquer comme envoyée">
                                    <i class="fas fa-paper-plane"></i>
                                </button>
                                @elseif($invoice->status === 'sent')
                                <button type="button"
                                        onclick="openStatusModal('markAsPaidModal', {{ $invoice->id }}, 'mark-as-paid')"
                                        class="text-green-600 hover:text-green-900"
                                        title="Marquer comme payée">
                                    <i class="fas fa-check-circle"></i>
                                </button>
                                @endif

                                <!-- Cancel action (pour tous les statuts sauf cancelled) -->
                                @if($invoice->status !== 'cancelled')
                                <button type="button"
                                        onclick="openStatusModal('markAsCancelledModal', {{ $invoice->id }}, 'mark-as-cancelled')"
                                        class="text-red-600 hover:text-red-900"
                                        title="Annuler la facture">
                                    <i class="fas fa-times-circle"></i>
                                </button>
                                @endif

                                <!-- Delete (seulement si draft) -->
                                @if($invoice->status === 'draft')
                                <form method="POST" action="{{ route('invoices.destroy', $invoice) }}" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" onclick="confirmDelete(this.form)" class="text-red-600 hover:text-red-900" title="Supprimer">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center">
                                <i class="fas fa-file-invoice text-4xl text-gray-300 mb-4"></i>
                                <p class="text-gray-500 text-lg">Aucune facture trouvée</p>
                                <p class="text-gray-400 text-sm">Commencez par créer votre première facture</p>
                                <a href="{{ route('invoices.create') }}" class="mt-4 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md">
                                    <i class="fas fa-plus mr-2"></i>Créer une facture
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    @if($invoices->hasPages())
    <div class="bg-white px-4 py-3 border border-gray-200 rounded-md">
        {{ $invoices->appends(request()->query())->links() }}
    </div>
    @endif
</div>

<!-- Inclusion des modales de changement de statut -->
@include('invoices.partials.status-modals')

<!-- Modal pour l'envoi d'email -->
<div id="emailModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50 flex items-center justify-center">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-md mx-4">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Envoyer la facture par email</h3>
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
function openEmailModal(invoiceId, clientEmail, invoiceNumber) {
    const modal = document.getElementById('emailModal');
    const form = document.getElementById('emailForm');
    const mainEmail = document.getElementById('mainEmail');
    const subject = document.getElementById('emailSubject');

    // Configurer le formulaire
    form.action = `/invoices/${invoiceId}/send`;

    // Remplir les champs
    mainEmail.value = clientEmail;
    subject.value = `Facture ${invoiceNumber}`;

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

// Fonction pour confirmer la suppression
function confirmDelete(form) {
    if (confirm('Êtes-vous sûr de vouloir supprimer cette facture ?')) {
        form.submit();
    }
}
</script>

@endsection
