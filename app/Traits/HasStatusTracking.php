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
