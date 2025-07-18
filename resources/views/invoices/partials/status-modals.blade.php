<!-- Modal pour marquer comme envoyée -->
<div id="markAsSentModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50 flex items-center justify-center">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-md mx-4">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Marquer comme envoyée</h3>
        </div>

        <form id="markAsSentForm" method="POST">
            @csrf
            @method('PATCH')
            <div class="px-6 py-4 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Raison du changement *</label>
                    <select name="reason" required class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Sélectionnez une raison</option>
                        <option value="validation_complete">Validation terminée - facture prête à être envoyée</option>
                        <option value="client_approval">Approbation du client obtenue</option>
                        <option value="automatic_send">Envoi automatique programmé</option>
                        <option value="manual_send">Envoi manuel par l'utilisateur</option>
                        <option value="other">Autre raison</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Commentaire</label>
                    <textarea name="comment" rows="3" placeholder="Commentaire optionnel..."
                              class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                </div>

            </div>

            <div class="px-6 py-4 border-t border-gray-200 flex justify-end space-x-3">
                <button type="button" onclick="closeModal('markAsSentModal')"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 rounded-md hover:bg-gray-300">
                    Annuler
                </button>
                <button type="submit"
                        class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700">
                    <i class="fas fa-paper-plane mr-2"></i>Marquer comme envoyée
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal pour marquer comme payée -->
<div id="markAsPaidModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50 flex items-center justify-center">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-md mx-4">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Marquer comme payée</h3>
        </div>

        <form id="markAsPaidForm" method="POST">
            @csrf
            @method('PATCH')
            <div class="px-6 py-4 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Raison du paiement *</label>
                    <select name="reason" required class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Sélectionnez une raison</option>
                        <option value="payment_received">Paiement reçu et confirmé</option>
                        <option value="bank_transfer">Virement bancaire confirmé</option>
                        <option value="cash_payment">Paiement en espèces</option>
                        <option value="check_cleared">Chèque encaissé</option>
                        <option value="partial_payment">Paiement partiel (ajustement)</option>
                        <option value="other">Autre mode de paiement</option>
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Méthode de paiement</label>
                        <select name="payment_method" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Sélectionnez</option>
                            <option value="cash">Espèces</option>
                            <option value="bank_transfer">Virement</option>
                            <option value="check">Chèque</option>
                            <option value="card">Carte</option>
                            <option value="mobile_money">Mobile Money</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Date de paiement</label>
                        <input type="date" name="payment_date" value="{{ date('Y-m-d') }}"
                               class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Référence de paiement</label>
                    <input type="text" name="payment_reference" placeholder="Numéro de transaction, référence..."
                           class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Montant payé (FCFA)</label>
                    <input type="number" name="amount_paid" step="0.01" placeholder="Montant exact payé"
                           class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Commentaire</label>
                    <textarea name="comment" rows="3" placeholder="Commentaire optionnel..."
                              class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                </div>
            </div>

            <div class="px-6 py-4 border-t border-gray-200 flex justify-end space-x-3">
                <button type="button" onclick="closeModal('markAsPaidModal')"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 rounded-md hover:bg-gray-300">
                    Annuler
                </button>
                <button type="submit"
                        class="px-4 py-2 text-sm font-medium text-white bg-green-600 rounded-md hover:bg-green-700">
                    <i class="fas fa-check-circle mr-2"></i>Marquer comme payée
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal pour annuler -->
<div id="markAsCancelledModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50 flex items-center justify-center">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-md mx-4">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Annuler la facture</h3>
        </div>

        <form id="markAsCancelledForm" method="POST">
            @csrf
            @method('PATCH')
            <div class="px-6 py-4 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Raison de l'annulation *</label>
                    <select name="reason" required class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Sélectionnez une raison</option>
                        <option value="client_request">Demande du client</option>
                        <option value="billing_error">Erreur de facturation</option>
                        <option value="duplicate_invoice">Facture en double</option>
                        <option value="service_not_delivered">Service non livré</option>
                        <option value="business_closure">Fermeture de l'entreprise cliente</option>
                        <option value="other">Autre raison</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Explication détaillée *</label>
                    <textarea name="comment" rows="4" required placeholder="Expliquez en détail la raison de l'annulation..."
                              class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                    <p class="text-xs text-gray-500 mt-1">Cette information est obligatoire pour les annulations</p>
                </div>

                <div class="bg-red-50 border border-red-200 rounded-md p-3">
                    <div class="flex">
                        <i class="fas fa-exclamation-triangle text-red-400 mr-2 mt-1"></i>
                        <div class="text-sm text-red-700">
                            <strong>Attention:</strong> Cette action changera le statut de la facture en "Annulée".
                            Assurez-vous que cette décision est justifiée.
                        </div>
                    </div>
                </div>
            </div>

            <div class="px-6 py-4 border-t border-gray-200 flex justify-end space-x-3">
                <button type="button" onclick="closeModal('markAsCancelledModal')"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 rounded-md hover:bg-gray-300">
                    Annuler
                </button>
                <button type="submit"
                        class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-md hover:bg-red-700">
                    <i class="fas fa-times-circle mr-2"></i>Annuler la facture
                </button>
            </div>
        </form>
    </div>
</div>

<script>
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
