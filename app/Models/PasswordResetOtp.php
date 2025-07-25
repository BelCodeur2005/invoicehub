<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class PasswordResetOtp extends Model
{
    use HasFactory;
    protected $fillable = [
        'email',
        'otp',
        'expires_at',
        'is_used'
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'is_used' => 'boolean'
    ];

    public function isExpired()
    {
        return Carbon::now()->isAfter($this->expires_at);
    }

    public function isValid()
    {
        return !$this->is_used && !$this->isExpired();
    }
}
