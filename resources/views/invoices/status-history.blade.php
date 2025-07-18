{{-- views/invoices/status-history.blade.php --}}
@extends('layouts.app')

@section('title', 'Historique des statuts - Facture ' . $invoice->number)
@section('page-title', 'Historique des statuts')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900">Historique des statuts</h1>
            <p class="text-sm text-gray-600">Facture {{ $invoice->number }} - {{ $invoice->client->name }}</p>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('invoices.show', $invoice) }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md flex items-center">
                <i class="fas fa-eye mr-2"></i>
                Voir la facture
            </a>
            <a href="{{ route('invoices.index') }}" class="border border-gray-300 hover:bg-gray-50 text-gray-700 px-4 py-2 rounded-md flex items-center">
                <i class="fas fa-arrow-left mr-2"></i>
                Retour aux factures
            </a>
        </div>
    </div>

    <!-- Info facture -->
    <div class="bg-white p-6 rounded-lg shadow">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">Numéro de facture</label>
                <p class="text-lg font-semibold text-gray-900">{{ $invoice->number }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Client</label>
                <p class="text-lg text-gray-900">{{ $invoice->client->name }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Montant</label>
                <p class="text-lg font-semibold text-gray-900">{{ number_format($invoice->total, 0, ',', ' ') }} FCFA</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Statut actuel</label>
                <div class="flex items-center mt-1">
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
                </div>
            </div>
        </div>
    </div>

    <!-- Timeline de l'historique -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Historique des changements de statut</h3>
            <p class="text-sm text-gray-500">Suivez l'évolution de votre facture</p>
        </div>

        <div class="px-6 py-4">
            @if($history->count() > 0)
                <div class="flow-root">
                    <ul role="list" class="-mb-8">
                        @foreach($history as $index => $log)
                        <li>
                            <div class="relative pb-8">
                                @if(!$loop->last)
                                    <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200" aria-hidden="true"></span>
                                @endif

                                <div class="relative flex space-x-3">
                                    <!-- Icône du statut -->
                                    <div>
                                        @switch($log['new_status'])
                                            @case('draft')
                                                <span class="h-8 w-8 rounded-full bg-gray-100 flex items-center justify-center ring-8 ring-white">
                                                    <i class="fas fa-edit text-gray-600"></i>
                                                </span>
                                                @break
                                            @case('sent')
                                                <span class="h-8 w-8 rounded-full bg-blue-100 flex items-center justify-center ring-8 ring-white">
                                                    <i class="fas fa-paper-plane text-blue-600"></i>
                                                </span>
                                                @break
                                            @case('paid')
                                                <span class="h-8 w-8 rounded-full bg-green-100 flex items-center justify-center ring-8 ring-white">
                                                    <i class="fas fa-check-circle text-green-600"></i>
                                                </span>
                                                @break
                                            @case('cancelled')
                                                <span class="h-8 w-8 rounded-full bg-red-100 flex items-center justify-center ring-8 ring-white">
                                                    <i class="fas fa-times-circle text-red-600"></i>
                                                </span>
                                                @break
                                        @endswitch
                                    </div>

                                    <!-- Contenu du log -->
                                    <div class="min-w-0 flex-1">
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <p class="text-sm font-medium text-gray-900">
                                                    Changement de statut
                                                    @if($log['old_status'])
                                                        de <span class="font-semibold text-gray-600">{{ ucfirst($log['old_status']) }}</span>
                                                    @endif
                                                    vers <span class="font-semibold text-gray-900">{{ ucfirst($log['new_status']) }}</span>
                                                </p>
                                                <p class="text-sm text-gray-500">
                                                    Par {{ $log['user'] }} • {{ $log['changed_at']->diffForHumans() }}
                                                </p>
                                            </div>
                                            <div class="text-right">
                                                <p class="text-xs text-gray-500">
                                                    {{ $log['changed_at']->format('d/m/Y à H:i') }}
                                                </p>
                                            </div>
                                        </div>

                                        <!-- Raison du changement -->
                                        @if($log['reason'])
                                        <div class="mt-2">
                                            <p class="text-sm text-gray-700">
                                                <span class="font-medium">Raison:</span> {{ $log['reason_label'] }}
                                            </p>
                                        </div>
                                        @endif

                                        <!-- Commentaire -->
                                        @if($log['comment'])
                                        <div class="mt-2">
                                            <p class="text-sm text-gray-700">
                                                <span class="font-medium">Commentaire:</span> {{ $log['comment'] }}
                                            </p>
                                        </div>
                                        @endif

                                        <!-- Données supplémentaires -->
                                        @if($log['additional_data'])
                                        <div class="mt-2">
                                            <details class="group">
                                                <summary class="cursor-pointer text-sm text-blue-600 hover:text-blue-800">
                                                    Voir les détails supplémentaires
                                                </summary>
                                                <div class="mt-2 p-3 bg-gray-50 rounded-md">
                                                    @foreach($log['additional_data'] as $key => $value)
                                                        @if($key === 'payment_data' && is_array($value))
                                                            <div class="mb-2">
                                                                <span class="font-medium text-gray-700">Informations de paiement:</span>
                                                                <ul class="mt-1 space-y-1 text-sm text-gray-600">
                                                                    @if(isset($value['payment_method']))
                                                                        <li>• Méthode: {{ $value['payment_method'] }}</li>
                                                                    @endif
                                                                    @if(isset($value['payment_reference']))
                                                                        <li>• Référence: {{ $value['payment_reference'] }}</li>
                                                                    @endif
                                                                    @if(isset($value['amount_paid']))
                                                                        <li>• Montant: {{ number_format($value['amount_paid'], 0, ',', ' ') }} FCFA</li>
                                                                    @endif
                                                                    @if(isset($value['payment_date']))
                                                                        <li>• Date: {{ \Carbon\Carbon::parse($value['payment_date'])->format('d/m/Y') }}</li>
                                                                    @endif
                                                                </ul>
                                                            </div>
                                                        @elseif($key === 'send_email' && $value)
                                                            <p class="text-sm text-gray-600">
                                                                <span class="font-medium">Email envoyé:</span> Oui
                                                            </p>
                                                        @elseif($key === 'email_addresses' && $value)
                                                            <p class="text-sm text-gray-600">
                                                                <span class="font-medium">Adresses email:</span> {{ $value }}
                                                            </p>
                                                        @else
                                                            <p class="text-sm text-gray-600">
                                                                <span class="font-medium">{{ ucfirst(str_replace('_', ' ', $key)) }}:</span>
                                                                @if(is_array($value))
                                                                    {{ json_encode($value) }}
                                                                @else
                                                                    {{ $value }}
                                                                @endif
                                                            </p>
                                                        @endif
                                                    @endforeach
                                                </div>
                                            </details>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </li>
                        @endforeach
                    </ul>
                </div>
            @else
                <div class="text-center py-12">
                    <i class="fas fa-history text-4xl text-gray-300 mb-4"></i>
                    <p class="text-gray-500 text-lg">Aucun historique disponible</p>
                    <p class="text-gray-400 text-sm">Cette facture n'a pas encore subi de changement de statut</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Actions rapides -->
    <div class="bg-white p-6 rounded-lg shadow">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Actions rapides</h3>
        <div class="flex flex-wrap gap-3">
            @if($invoice->status === 'draft')
                <button type="button"
                        onclick="openStatusModal('markAsSentModal', {{ $invoice->id }}, 'mark-as-sent')"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md flex items-center">
                    <i class="fas fa-paper-plane mr-2"></i>
                    Marquer comme envoyée
                </button>
            @elseif($invoice->status === 'sent')
                <button type="button"
                        onclick="openStatusModal('markAsPaidModal', {{ $invoice->id }}, 'mark-as-paid')"
                        class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md flex items-center">
                    <i class="fas fa-check-circle mr-2"></i>
                    Marquer comme payée
                </button>
            @endif

            @if($invoice->status !== 'cancelled')
                <button type="button"
                        onclick="openStatusModal('markAsCancelledModal', {{ $invoice->id }}, 'mark-as-cancelled')"
                        class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-md flex items-center">
                    <i class="fas fa-times-circle mr-2"></i>
                    Annuler la facture
                </button>
            @endif

            <a href="{{ route('invoices.pdf', $invoice) }}"
               class="border border-gray-300 hover:bg-gray-50 text-gray-700 px-4 py-2 rounded-md flex items-center">
                <i class="fas fa-file-pdf mr-2"></i>
                Télécharger PDF
            </a>
        </div>
    </div>
</div>

<!-- Inclusion des modales de changement de statut -->
@include('invoices.partials.status-modals')

<script>
// Réutilisation des fonctions JavaScript de l'index pour les modales
function openStatusModal(modalId, invoiceId, action) {
    const modal = document.getElementById(modalId);
    const form = modal.querySelector('form');

    // Configurer l'action du formulaire
    form.action = `/invoices/${invoiceId}/${action}`;

    // Afficher la modal
    modal.classList.remove('hidden');

    // Focus sur le premier champ
    const firstInput = modal.querySelector('select, input, textarea');
    if (firstInput) {
        firstInput.focus();
    }
}

function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    modal.classList.add('hidden');

    // Réinitialiser le formulaire
    const form = modal.querySelector('form');
    form.reset();
}

// Gérer l'affichage des options email
document.getElementById('send_email').addEventListener('change', function() {
    const emailOptions = document.getElementById('emailOptions');
    if (this.checked) {
        emailOptions.classList.remove('hidden');
    } else {
        emailOptions.classList.add('hidden');
    }
});

// Fermer les modales en cliquant à l'extérieur
['markAsSentModal', 'markAsPaidModal', 'markAsCancelledModal'].forEach(modalId => {
    document.getElementById(modalId).addEventListener('click', function(e) {
        if (e.target === this) {
            closeModal(modalId);
        }
    });
});
</script>

@endsection
