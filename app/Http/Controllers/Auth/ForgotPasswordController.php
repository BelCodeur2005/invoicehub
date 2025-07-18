<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\OtpService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class ForgotPasswordController extends Controller
{
    protected $otpService;

    public function __construct(OtpService $otpService)
    {
        $this->otpService = $otpService;
    }

    public function showForgotForm()
    {
        return view('auth.forgot-password');
    }

    public function sendOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email'
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $email = $request->email;
        $otp = $this->otpService->generateOtp($email);

        // Envoyer l'OTP par email
        Mail::send('emails.otp', ['otp' => $otp], function ($message) use ($email) {
            $message->to($email)
                    ->subject('Code de récupération de mot de passe - Invoice Hub');
        });

        // Stocker l'email dans la session persistante
        session(['email' => $email]);

        return redirect()->route('password.verify-otp')
                        ->with('status', 'Un code de vérification a été envoyé à votre adresse email.');
    }

    public function showVerifyOtpForm()
    {
        if (!session('email')) {
            return redirect()->route('password.request');
        }

        return view('auth.verify-otp');
    }

    public function verifyOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'otp' => 'required|string|size:6'
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        if (!$this->otpService->verifyOtp($request->email, $request->otp)) {
            return back()->withErrors(['otp' => 'Code de vérification invalide ou expiré.'])
                        ->withInput();
        }

        // SOLUTION 1 : Stocker dans la session persistante
        session(['email' => $request->email]);
        session(['otp_verified' => true]);

        return redirect()->route('password.reset');
    }

    public function showResetForm()
    {
        if (!session('email') || !session('otp_verified')) {
            return redirect()->route('password.request');
        }

        return view('auth.reset-password');
    }

    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
            'password' => 'required|confirmed|min:8',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Vérifier que l'OTP a été vérifié
        if (!session('otp_verified')) {
            return redirect()->route('password.request');
        }

        // Vérifier que l'email correspond à celui de la session
        if (session('email') !== $request->email) {
            return redirect()->route('password.request');
        }

        $user = User::where('email', $request->email)->first();
        $user->update([
            'password' => Hash::make($request->password)
        ]);

        // Nettoyer la session
        session()->forget(['email', 'otp_verified']);

        return redirect()->route('login')
                        ->with('status', 'Votre mot de passe a été réinitialisé avec succès.');
    }
}
