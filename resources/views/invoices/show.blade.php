@extends('layouts.app')

@section('title', 'Facture ' . $invoice->number)
@section('page-title', 'Facture ' . $invoice->number)

@section('content')
<div class="space-y-6">
    <!-- Header avec actions -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900">Facture {{ $invoice->number }}</h1>
            <p class="text-sm text-gray-600">Créée le {{ \Carbon\Carbon::parse($invoice->created_at)->format('d/m/Y à H:i') }}</p>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('invoices.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-md">
                <i class="fas fa-arrow-left mr-2"></i>Retour
            </a>

            {{-- @if($invoice->status === 'draft') --}}
            <a href="{{ route('invoices.edit', $invoice) }}" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md">
                <i class="fas fa-edit mr-2"></i>Modifier
            </a>
            {{-- @endif --}}

            <button onclick="generatePDF('invoice', {{ $invoice->id }})" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-md">
                <i class="fas fa-file-pdf mr-2"></i>PDF
            </button>

            <button onclick="openEmailModal({{ $invoice->id }}, '{{ $invoice->client->email }}', '{{ $invoice->number }}')" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-md">
                <i class="fas fa-envelope mr-2"></i>Email
            </button>

            @if($invoice->status === 'draft')
            <form method="POST" action="{{ route('invoices.mark-as-sent', $invoice) }}" class="inline">
                @csrf
                @method('PATCH')
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md">
                    <i class="fas fa-paper-plane mr-2"></i>Marquer envoyée
                </button>
            </form>
            @elseif($invoice->status === 'sent')
            <form method="POST" action="{{ route('invoices.mark-as-paid', $invoice) }}" class="inline">
                @csrf
                @method('PATCH')
                <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-md">
                    <i class="fas fa-check-circle mr-2"></i>Marquer payée
                </button>
            </form>
            @endif
        </div>
    </div>

    <!-- Statut et informations générales -->
    <div class="bg-white rounded-lg shadow p-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div>
                <h3 class="text-lg font-medium text-gray-900 mb-3">Statut</h3>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                    {{ $invoice->status === 'paid' ? 'bg-green-100 text-green-800' :
                       ($invoice->status === 'sent' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800') }}">
                    @if($invoice->status === 'draft')
                        <i class="fas fa-edit mr-2"></i>Brouillon
                    @elseif($invoice->status === 'sent')
                        <i class="fas fa-paper-plane mr-2"></i>Envoyée
                    @elseif($invoice->status === 'paid')
                        <i class="fas fa-check-circle mr-2"></i>Payée
                    @endif
                </span>
            </div>
            <div>
                <h3 class="text-lg font-medium text-gray-900 mb-3">Dates</h3>
                <p class="text-sm text-gray-600">Date: {{ \Carbon\Carbon::parse($invoice->date)->format('d/m/Y') }}</p>
                <p class="text-sm text-gray-600">Échéance: {{ \Carbon\Carbon::parse($invoice->due_date)->format('d/m/Y') }}</p>
            </div>
            <div>
                <h3 class="text-lg font-medium text-gray-900 mb-3">Montant total</h3>
                <p class="text-2xl font-bold text-gray-900">{{ number_format($invoice->total, 0, ',', ' ') }} FCFA</p>
            </div>
        </div>
    </div>

    <!-- Informations client et entreprise -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Client -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Client</h3>
            <div class="space-y-2">
                <p class="font-medium text-gray-900">{{ $invoice->client->name }}</p>
                <p class="text-sm text-gray-600">{{ $invoice->client->email }}</p>
                @if($invoice->client->phone)
                <p class="text-sm text-gray-600">{{ $invoice->client->phone }}</p>
                @endif
                @if($invoice->client->address)
                <p class="text-sm text-gray-600">{{ $invoice->client->address }}</p>
                @endif
                @if($invoice->client->city)
                <p class="text-sm text-gray-600">{{ $invoice->client->city }}, {{ $invoice->client->country }}</p>
                @endif
            </div>
        </div>

        <!-- Entreprise -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Facturé par</h3>
            <div class="space-y-2">
                <p class="font-medium text-gray-900">Votre Entreprise</p>
                <p class="text-sm text-gray-600">contact@entreprise.com</p>
                <p class="text-sm text-gray-600">+237 XXX XXX XXX</p>
                <p class="text-sm text-gray-600">Douala, Cameroun</p>
            </div>
        </div>
    </div>

    <!-- Articles -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Articles</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Produit/Service
                        </th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Quantité
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Prix unitaire
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            TVA
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Total
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($invoice->items as $item)
                    <tr>
                        <td class="px-6 py-4">
                            <div class="text-sm font-medium text-gray-900">{{ $item->product->name }}</div>
                            @if($item->product->description)
                            <div class="text-sm text-gray-500">{{ $item->product->description }}</div>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-center text-sm text-gray-900">
                            {{ $item->quantity }}
                        </td>
                        <td class="px-6 py-4 text-right text-sm text-gray-900">
                            {{ number_format($item->price, 0, ',', ' ') }} FCFA
                        </td>
                        <td class="px-6 py-4 text-right text-sm text-gray-900">
                            {{ $item->tax_rate }}%
                        </td>
                        <td class="px-6 py-4 text-right text-sm font-medium text-gray-900">
                            {{ number_format($item->total, 0, ',', ' ') }} FCFA
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Totaux -->
        <div class="bg-gray-50 px-6 py-4">
            <div class="flex justify-end">
                <div class="w-64">
                    <div class="flex justify-between py-2">
                        <span class="text-sm text-gray-600">Sous-total:</span>
                        <span class="text-sm font-medium text-gray-900">{{ number_format($invoice->subtotal, 0, ',', ' ') }} FCFA</span>
                    </div>
                    <div class="flex justify-between py-2">
                        <span class="text-sm text-gray-600">TVA:</span>
                        <span class="text-sm font-medium text-gray-900">{{ number_format($invoice->tax_amount, 0, ',', ' ') }} FCFA</span>
                    </div>
                    <div class="flex justify-between py-2 border-t border-gray-200">
                        <span class="text-base font-medium text-gray-900">Total:</span>
                        <span class="text-base font-bold text-gray-900">{{ number_format($invoice->total, 0, ',', ' ') }} FCFA</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Notes -->
    @if($invoice->notes)
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Notes</h3>
        <div class="prose max-w-none">
            <p class="text-gray-600">{{ $invoice->notes }}</p>
        </div>
    </div>
    @endif

    <!-- Proforma source -->
    @if($invoice->proforma)
    <div class="bg-blue-50 rounded-lg p-6">
        <h3 class="text-lg font-medium text-blue-900 mb-2">Proforma source</h3>
        <p class="text-blue-700">Cette facture a été générée à partir du proforma
            <a href="{{ route('proformas.show', $invoice->proforma) }}" class="font-medium underline">{{ $invoice->proforma->number }}</a>
        </p>
    </div>
    @endif

    <!-- Actions de suppression -->
    @if($invoice->status === 'draft')
    <div class="bg-red-50 rounded-lg p-6">
        <h3 class="text-lg font-medium text-red-900 mb-2">Zone de danger</h3>
        <p class="text-red-700 mb-4">Une fois supprimée, cette facture ne pourra pas être récupérée.</p>
        <form method="POST" action="{{ route('invoices.destroy', $invoice) }}" class="inline">
            @csrf
            @method('DELETE')
            <button type="button" onclick="confirmDelete(this.form)" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-md">
                <i class="fas fa-trash mr-2"></i>Supprimer la facture
            </button>
        </form>
    </div>
    @endif
</div>
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

</script>
@endsection

