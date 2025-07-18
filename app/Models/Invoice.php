<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasStatusTracking; // <-- Vérifiez cette ligne

class Invoice extends Model
{
    use HasFactory, HasStatusTracking;

    protected $fillable = [
        'client_id',
        'user_id',
        'proforma_id',
        'number',
        'date',
        'due_date',
        'subtotal',
        'tax_amount',
        'total',
        'status',
        'notes',
    ];

    protected $casts = [
        'date' => 'date:Y-m-d',
        'due_date' => 'date:Y-m-d',
        'subtotal' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'total' => 'decimal:2',
    ];




    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function proforma()
    {
        return $this->belongsTo(Proforma::class);
    }

    public function items()
    {
        return $this->hasMany(\App\Models\Invoice_item::class);
    }


    public function calculateTotals()
    {
        $this->subtotal = $this->items->sum('subtotal');
        $this->tax_amount = $this->items->sum('tax_amount');
        $this->total = $this->subtotal + $this->tax_amount;
        $this->save();
    }

    public static function generateNumber()
    {
        $year = date('Y');

        // Chercher la derniere facture de l'année au format BTS/DC/{year}/pfmXXX
        $lastInvoice = self::where('number', 'like', "BTS/DC/{$year}/fac%")
                            ->latest()
                            ->first();

        if ($lastInvoice) {
            // Extraire les 3 derniers chiffres du numéro
            $lastNumber = (int) substr($lastInvoice->number, -3);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        // Formater le numéro avec leading zeros sur 3 chiffres
        $formattedNumber = str_pad($newNumber, 3, '0', STR_PAD_LEFT);

        return "BTS/DC/{$year}/fac{$formattedNumber}";
    }


    public function isOverdue()
    {
        return $this->due_date < now() && $this->status !== 'paid';
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
