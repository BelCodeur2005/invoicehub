<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('invoice_status_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('old_status')->nullable();
            $table->string('new_status');
            $table->text('reason')->nullable();
            $table->text('comment')->nullable();
            $table->json('additional_data')->nullable(); // Pour stocker des données supplémentaires
            $table->timestamp('changed_at');
            $table->timestamps();

            $table->index(['invoice_id', 'changed_at']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('invoice_status_logs');
    }
};
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceStatusLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_id',
        'user_id',
        'old_status',
        'new_status',
        'reason',
        'comment',
        'additional_data',
        'changed_at'
    ];

    protected $casts = [
        'additional_data' => 'array',
        'changed_at' => 'datetime'
    ];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Méthodes utiles pour les raisons
    public static function getReasons()
    {
        return [
            'draft_to_sent' => [
                'validation_complete' => 'Validation terminée - facture prête à être envoyée',
                'client_approval' => 'Approbation du client obtenue',
                'automatic_send' => 'Envoi automatique programmé',
                'manual_send' => 'Envoi manuel par l\'utilisateur',
                'other' => 'Autre raison'
            ],
            'sent_to_paid' => [
                'payment_received' => 'Paiement reçu et confirmé',
                'bank_transfer' => 'Virement bancaire confirmé',
                'cash_payment' => 'Paiement en espèces',
                'check_cleared' => 'Chèque encaissé',
                'partial_payment' => 'Paiement partiel (ajustement)',
                'other' => 'Autre mode de paiement'
            ],
            'any_to_cancelled' => [
                'client_request' => 'Demande du client',
                'billing_error' => 'Erreur de facturation',
                'duplicate_invoice' => 'Facture en double',
                'service_not_delivered' => 'Service non livré',
                'business_closure' => 'Fermeture de l\'entreprise cliente',
                'other' => 'Autre raison'
            ],
            'paid_to_sent' => [
                'payment_cancelled' => 'Paiement annulé',
                'chargeback' => 'Rétrofacturation',
                'accounting_error' => 'Erreur comptable',
                'other' => 'Autre raison'
            ]
        ];
    }

    public static function getReasonLabel($fromStatus, $toStatus, $reason)
    {
        $reasons = self::getReasons();
        $key = $fromStatus . '_to_' . $toStatus;

        if (!isset($reasons[$key])) {
            $key = 'any_to_' . $toStatus;
        }

        return $reasons[$key][$reason] ?? $reason;
    }
}
<?php

namespace App\Traits;

use App\Models\InvoiceStatusLog;
use Illuminate\Support\Facades\Auth;

trait HasStatusTracking
{
    public function statusLogs()
    {
        return $this->hasMany(InvoiceStatusLog::class);
    }

    public function changeStatus($newStatus, $reason = null, $comment = null, $additionalData = null)
    {
        $oldStatus = $this->status;

        // Validation des transitions autorisées
        if (!$this->isValidStatusTransition($oldStatus, $newStatus)) {
            throw new \InvalidArgumentException("Transition de statut non autorisée : {$oldStatus} -> {$newStatus}");
        }

        // Enregistrer le changement dans les logs
        $this->statusLogs()->create([
            'user_id' => Auth::id(),
            'old_status' => $oldStatus,
            'new_status' => $newStatus,
            'reason' => $reason,
            'comment' => $comment,
            'additional_data' => $additionalData,
            'changed_at' => now()
        ]);

        // Mettre à jour le statut
        $this->update(['status' => $newStatus]);

        return $this;
    }

    protected function isValidStatusTransition($from, $to)
    {
        $validTransitions = [
            'draft' => ['sent', 'cancelled'],
            'sent' => ['paid', 'cancelled'],
            'paid' => ['sent'], // En cas d'annulation de paiement
            'cancelled' => ['draft'] // Réactivation possible
        ];

        return in_array($to, $validTransitions[$from] ?? []);
    }

    public function getStatusHistory()
    {
        return $this->statusLogs()
            ->with('user')
            ->orderBy('changed_at', 'desc')
            ->get()
            ->map(function ($log) {
                return [
                    'old_status' => $log->old_status,
                    'new_status' => $log->new_status,
                    'reason' => $log->reason,
                    'reason_label' => InvoiceStatusLog::getReasonLabel($log->old_status, $log->new_status, $log->reason),
                    'comment' => $log->comment,
                    'user' => $log->user->name,
                    'changed_at' => $log->changed_at,
                    'additional_data' => $log->additional_data
                ];
            });
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasStatusTracking;

class Invoice extends Model
{
    use HasFactory, HasStatusTracking;

    protected $fillable = [
        'number',
        'date',
        'due_date',
        'status',
        'total',
        'client_id',
        // autres champs...
    ];

    protected $casts = [
        'date' => 'date',
        'due_date' => 'date',
        'total' => 'decimal:2'
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    // Méthodes spécifiques pour chaque transition
    public function markAsSent($reason = 'manual_send', $comment = null)
    {
        return $this->changeStatus('sent', $reason, $comment);
    }

    public function markAsPaid($reason = 'payment_received', $comment = null, $paymentData = null)
    {
        return $this->changeStatus('paid', $reason, $comment, [
            'payment_data' => $paymentData,
            'payment_date' => now()
        ]);
    }

    public function markAsCancelled($reason = 'client_request', $comment = null)
    {
        return $this->changeStatus('cancelled', $reason, $comment);
    }

    // Méthodes utiles
    public function getStatusBadgeClass()
    {
        return match($this->status) {
            'draft' => 'bg-gray-100 text-gray-800',
            'sent' => 'bg-blue-100 text-blue-800',
            'paid' => 'bg-green-100 text-green-800',
            'cancelled' => 'bg-red-100 text-red-800',
            default => 'bg-gray-100 text-gray-800'
        };
    }

    public function getStatusIcon()
    {
        return match($this->status) {
            'draft' => 'fas fa-edit',
            'sent' => 'fas fa-paper-plane',
            'paid' => 'fas fa-check-circle',
            'cancelled' => 'fas fa-times-circle',
            default => 'fas fa-question-circle'
        };
    }
}
<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\InvoiceStatusLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class InvoiceController extends Controller
{
    public function markAsSent(Request $request, Invoice $invoice)
    {
        $validator = Validator::make($request->all(), [
            'reason' => 'required|string|in:' . implode(',', array_keys(InvoiceStatusLog::getReasons()['draft_to_sent'])),
            'comment' => 'nullable|string|max:500',
            'send_email' => 'boolean',
            'email_addresses' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            $invoice->markAsSent(
                $request->reason,
                $request->comment,
                [
                    'send_email' => $request->send_email,
                    'email_addresses' => $request->email_addresses
                ]
            );

            $reasonLabel = InvoiceStatusLog::getReasonLabel('draft', 'sent', $request->reason);

            return redirect()->route('invoices.index')
                ->with('success', "Facture marquée comme envoyée. Raison: {$reasonLabel}");

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Erreur lors du changement de statut: ' . $e->getMessage());
        }
    }

    public function markAsPaid(Request $request, Invoice $invoice)
    {
        $validator = Validator::make($request->all(), [
            'reason' => 'required|string|in:' . implode(',', array_keys(InvoiceStatusLog::getReasons()['sent_to_paid'])),
            'comment' => 'nullable|string|max:500',
            'payment_method' => 'nullable|string|max:100',
            'payment_reference' => 'nullable|string|max:100',
            'payment_date' => 'nullable|date',
            'amount_paid' => 'nullable|numeric|min:0'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            $paymentData = [
                'payment_method' => $request->payment_method,
                'payment_reference' => $request->payment_reference,
                'payment_date' => $request->payment_date ?? now(),
                'amount_paid' => $request->amount_paid ?? $invoice->total
            ];

            $invoice->markAsPaid($request->reason, $request->comment, $paymentData);

            $reasonLabel = InvoiceStatusLog::getReasonLabel('sent', 'paid', $request->reason);

            return redirect()->route('invoices.index')
                ->with('success', "Facture marquée comme payée. Raison: {$reasonLabel}");

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Erreur lors du changement de statut: ' . $e->getMessage());
        }
    }

    public function markAsCancelled(Request $request, Invoice $invoice)
    {
        $validator = Validator::make($request->all(), [
            'reason' => 'required|string|in:' . implode(',', array_keys(InvoiceStatusLog::getReasons()['any_to_cancelled'])),
            'comment' => 'required|string|max:500' // Obligatoire pour les annulations
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            $invoice->markAsCancelled($request->reason, $request->comment);

            $reasonLabel = InvoiceStatusLog::getReasonLabel($invoice->status, 'cancelled', $request->reason);

            return redirect()->route('invoices.index')
                ->with('success', "Facture annulée. Raison: {$reasonLabel}");

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Erreur lors du changement de statut: ' . $e->getMessage());
        }
    }

    public function showStatusHistory(Invoice $invoice)
    {
        $history = $invoice->getStatusHistory();

        return view('invoices.status-history', compact('invoice', 'history'));
    }
}
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

                <div class="flex items-center">
                    <input type="checkbox" name="send_email" id="send_email" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                    <label for="send_email" class="ml-2 text-sm text-gray-700">Envoyer par email automatiquement</label>
                </div>

                <div id="emailOptions" class="hidden">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Adresses email supplémentaires</label>
                    <input type="email" name="email_addresses" placeholder="Séparez par des virgules"
                           class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
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
