<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
        'niu',
        'rccm',
        'bp',
        'account_number',
        'bank',
        'country',
        'street',
        'city',
    ];

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    public function proformas()
    {
        return $this->hasMany(Proforma::class);
    }
}
