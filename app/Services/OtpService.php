<?php


namespace App\Services;

use App\Models\PasswordResetOtp;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class OtpService
{
    public function generateOtp(string $email): string
    {
        // Supprimer les anciens OTP non utilisés
        PasswordResetOtp::where('email', $email)
            ->where('is_used', false)
            ->delete();

        // Générer un OTP de 6 chiffres
        $otp = str_pad(random_int(100000, 999999), 6, '0', STR_PAD_LEFT);

        // Sauvegarder l'OTP
        PasswordResetOtp::create([
            'email' => $email,
            'otp' => $otp,
            'expires_at' => Carbon::now()->addMinutes(7), // Expire dans 15 minutes
            'is_used' => false
        ]);

        return $otp;
    }

    public function verifyOtp(string $email, string $otp): bool
    {
        $otpRecord = PasswordResetOtp::where('email', $email)
            ->where('otp', $otp)
            ->where('is_used', false)
            ->first();

        if (!$otpRecord || $otpRecord->isExpired()) {
            return false;
        }

        // Marquer l'OTP comme utilisé
        $otpRecord->update(['is_used' => true]);

        return true;
    }

    public function cleanupExpiredOtps(): void
    {
        PasswordResetOtp::where('expires_at', '<', Carbon::now())
            ->orWhere('is_used', true)
            ->delete();
    }
}
