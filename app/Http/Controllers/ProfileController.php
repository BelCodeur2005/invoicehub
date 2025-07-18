<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\Proforma;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    /**
     * Afficher le profil de l'utilisateur
     */
    public function show()
    {
        $user = auth()->user()->loadCount('proformas');
        return view('profile.show', compact('user'));
    }

    /**
     * Afficher le formulaire d'édition du profil
     */
    public function edit()
    {
        $user = auth()->user();
        return view('profile.edit', compact('user'));
    }

    /**
     * Mettre à jour les informations du profil
     */
    public function update(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                Rule::unique('users')->ignore($user->id),
            ],
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Gestion de l'upload d'avatar
        if ($request->hasFile('avatar')) {
            // Supprimer l'ancien avatar s'il existe
            if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }

            // Sauvegarder le nouvel avatar
            $avatarPath = $request->file('avatar')->store('avatars', 'public');
            $validated['avatar'] = $avatarPath;
        }
        /** @var \App\Models\User $user */

        // Mettre à jour les informations
        $user->update($validated);

        return redirect()->route('profile.show')
                        ->with('success', 'Profil mis à jour avec succès');
    }

    /**
     * Afficher le formulaire de changement de mot de passe
     */
    public function editPassword()
    {
        return view('profile.password');
    }

    /**
     * Mettre à jour le mot de passe
     */
    public function updatePassword(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'current_password' => 'required',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // Vérifier le mot de passe actuel
        if (!Hash::check($validated['current_password'], $user->password)) {
            return back()->withErrors(['current_password' => 'Le mot de passe actuel est incorrect']);
        }
        /** @var \App\Models\User $user */
        // Mettre à jour le mot de passe
        $user->update([
            'password' => Hash::make($validated['password'])
        ]);

        return redirect()->route('profile.show')
                        ->with('success', 'Mot de passe mis à jour avec succès');
    }

    /**
     * Supprimer l'avatar
     */
    public function deleteAvatar()
    {
        $user = auth()->user();
        /** @var \App\Models\User $user */
        if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
            Storage::disk('public')->delete($user->avatar);
            $user->update(['avatar' => null]);
        }

        return redirect()->route('profile.edit')
                        ->with('success', 'Avatar supprimé avec succès');
    }

    /**
     * Afficher les paramètres de l'utilisateur
     */
    public function settings()
    {
        $user = auth()->user();
        return view('profile.settings', compact('user'));
    }

    /**
     * Mettre à jour les paramètres
     */
    public function updateSettings(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'language' => 'required|in:fr,en',
            'timezone' => 'required|string|max:50',
            'email_notifications' => 'boolean',
            'invoice_notifications' => 'boolean',
            'theme' => 'required|in:light,dark,system',
        ]);

        // Convertir les checkboxes en boolean
        $validated['email_notifications'] = $request->has('email_notifications');
        $validated['invoice_notifications'] = $request->has('invoice_notifications');
        /** @var \App\Models\User $user */
        // Mettre à jour les préférences utilisateur
        $user->update($validated);

        return redirect()->route('profile.settings')
                        ->with('success', 'Paramètres mis à jour avec succès');
    }

}
