<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\OtpService;

class CleanupExpiredOtps extends Command
{
    protected $signature = 'otp:cleanup';
    protected $description = 'Nettoyer les OTP expirés';

    public function handle(OtpService $otpService)
    {
        $otpService->cleanupExpiredOtps();
        $this->info('OTP expirés supprimés avec succès.');
    }
}
