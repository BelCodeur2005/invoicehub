<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company_settings extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_name',
        'company_address',
        'company_phone',
        'company_email',
        'tax_number',
        'logo_path',
        'default_tax_rate',
    ];

    protected $casts = [
        'default_tax_rate' => 'decimal:2',
    ];

    public static function getSettings()
    {
        return self::first() ?? new self([
            'company_name' => 'Ma Société',
            'default_tax_rate' => 19.25,
        ]);
    }
}
