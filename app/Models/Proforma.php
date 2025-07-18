<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Proforma extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'user_id',
        'number',
        'date',
        'valid_until',
        'subtotal',
        'tax_amount',
        'total',
        'status',
        'notes',
        'conditionPaiement',
        'delaiDeploiment',
        'garantieMateriel'
    ];

    protected $casts = [
        'date' => 'date',
        'valid_until' => 'date',
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

    public function items()
    {
        return $this->hasMany(Proforma_item::class);
    }

    public function invoice()
    {
        return $this->hasOne(Invoice::class);
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

        // Chercher le dernier proforma de l'année au format BTS/DC/{year}/pfmXXX
        $lastProforma = self::where('number', 'like', "BTS/DC/{$year}/pfm%")
                            ->latest()
                            ->first();

        if ($lastProforma) {
            // Extraire les 3 derniers chiffres du numéro
            $lastNumber = (int) substr($lastProforma->number, -3);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        // Formater le numéro avec leading zeros sur 3 chiffres
        $formattedNumber = str_pad($newNumber, 3, '0', STR_PAD_LEFT);

        return "BTS/DC/{$year}/pfm{$formattedNumber}";
    }

}
