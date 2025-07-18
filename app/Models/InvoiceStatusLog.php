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
