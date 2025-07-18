@extends('layouts.app')
@section('title', 'Proforma #' . $proforma->number)
@section('page-title', 'Détails du Proforma #' . $proforma->number)
@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Proforma #{{ $proforma->number }}</h1>
            <p class="mt-2 text-sm text-gray-600">
                Créé le {{ $proforma->created_at->format('d/m/Y') }} par {{ $proforma->user->name }}
            </p>
        </div>
        <div class="flex items-center space-x-3">
            <a href="{{ route('proformas.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg transition-colors inline-flex items-center">
                <i class="fas fa-arrow-left mr-2"></i>
                Retour à la liste
            </a>
            @if($proforma->status === 'draft')
                <a href="{{ route('proformas.edit', $proforma) }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors inline-flex items-center">
                    <i class="fas fa-edit mr-2"></i>
                    Modifier
                </a>
            @endif
            @if(!$proforma->invoice)
                <form action="{{ route('proformas.convert', $proforma) }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition-colors inline-flex items-center">
                        <i class="fas fa-file-invoice mr-2"></i>
                        Convertir en facture
                    </button>
                </form>
            @endif
        </div>
    </div>

    <!-- Status Badge -->
    <div class="flex items-center space-x-2">
        <span class="px-3 py-1 text-xs font-medium rounded-full
            @if($proforma->status === 'draft') bg-gray-100 text-gray-800
            @elseif($proforma->status === 'sent') bg-blue-100 text-blue-800
            @elseif($proforma->status === 'accepted') bg-green-100 text-green-800
            @elseif($proforma->status === 'rejected') bg-red-100 text-red-800
            @endif">
            @if($proforma->status === 'draft') Brouillon
            @elseif($proforma->status === 'sent') Envoyé
            @elseif($proforma->status === 'accepted') Accepté
            @elseif($proforma->status === 'rejected') Rejeté
            @endif
        </span>
        @if($proforma->invoice)
            <span class="px-3 py-1 text-xs font-medium rounded-full bg-purple-100 text-purple-800">
                Converti en facture
            </span>
        @endif
    </div>

    <!-- Main Content -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Proforma Details -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Basic Information -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="p-6 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">Informations générales</h2>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Date du proforma</label>
                            <p class="mt-1 text-sm text-gray-900">{{ \Carbon\Carbon::parse($proforma->date)->format('d/m/Y') }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Valide jusqu'au</label>
                            <p class="mt-1 text-sm text-gray-900">{{ \Carbon\Carbon::parse($proforma->valid_until)->format('d/m/Y') }}</p>
                        </div>
                        @if($proforma->conditionPaiement)
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Condition de paiement</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $proforma->conditionPaiement }}</p>
                        </div>
                        @endif
                        @if($proforma->delaiDeploiment)
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Délai de déploiement</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $proforma->delaiDeploiment }}</p>
                        </div>
                        @endif
                        @if($proforma->garantieMateriel)
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Garantie matériel</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $proforma->garantieMateriel }}</p>
                        </div>
                        @endif
                        @if($proforma->notes)
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700">Notes</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $proforma->notes }}</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Items -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="p-6 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">Articles</h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Produit</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantité</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Prix unitaire</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">TVA</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($proforma->items as $item)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $item->product->name }}</div>
                                    @if($item->product->description)
                                    <div class="text-sm text-gray-500">{{ $item->product->description }}</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $item->quantity }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ number_format($item->price, 0, ',', ' ') }} FCFA
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $item->tax_rate }}%
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    {{ number_format($item->total, 0, ',', ' ') }} FCFA
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Totals -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="p-6">
                    <div class="flex justify-end">
                        <div class="w-full max-w-md space-y-2">
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600">Sous-total HT:</span>
                                <span class="text-sm font-medium text-gray-900">{{ number_format($proforma->subtotal, 0, ',', ' ') }} FCFA</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600">TVA:</span>
                                <span class="text-sm font-medium text-gray-900">{{ number_format($proforma->tax_amount, 0, ',', ' ') }} FCFA</span>
                            </div>
                            <div class="flex justify-between text-lg font-bold border-t border-gray-300 pt-2">
                                <span>Total TTC:</span>
                                <span class="text-blue-600">{{ number_format($proforma->total, 0, ',', ' ') }} FCFA</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Client Information -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="p-6 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">Client</h2>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Nom</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $proforma->client->name }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Email</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $proforma->client->email }}</p>
                        </div>
                        @if($proforma->client->phone)
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Téléphone</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $proforma->client->phone }}</p>
                        </div>
                        @endif
                        @if($proforma->client->address)
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Adresse</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $proforma->client->address }}</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="p-6 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">Actions</h2>
                </div>
                <div class="p-6 space-y-3">
                    <button onclick="generatePDF('proforma', {{ $proforma->id }})" class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors inline-flex items-center justify-center">
                        <i class="fas fa-download mr-2"></i>
                        Télécharger PDF
                    </button>
                    <button onclick="openEmailModal({{ $proforma->id }}, '{{ $proforma->client->email }}', '{{ $proforma->number }}')" class="w-full bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition-colors inline-flex items-center justify-center">
                        <i class="fas fa-envelope mr-2"></i>
                        Envoyer par email
                    </button>
                    @if($proforma->status === 'draft')
                    <form action="{{ route('proformas.destroy', $proforma) }}" method="POST" class="w-full" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce proforma ?')">
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
</script>
@endsection
