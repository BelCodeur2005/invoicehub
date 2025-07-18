<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (!auth()->user()->canManageUsers()) {
                abort(403, 'Accès non autorisé');
            }
            return $next($request);
        });
    }

    public function index()
    {
        // $this->authorize('viewAny', $user);
        // Filtrage selon le rôle
        $query = User::withCount(['invoices', 'proformas']);

        if (auth()->user()->isManager()) {
            $query->where('role', 'user');
        }

        $users = $query->paginate(7);

        return view('users.index', compact('users'));
    }

    public function create()
    {
        $roles = auth()->user()->isAdmin()
            ? ['admin' => 'Admin', 'manager' => 'Manager', 'user' => 'User']
            : ['user' => 'User'];

        return view('users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $validated = $this->validateRequest($request);

        // Gestion de l'upload d'avatar
        if ($request->hasFile('avatar')) {
            $avatarPath = $request->file('avatar')->store('avatars', 'public');
            $validated['avatar'] = $avatarPath;
        }

        $validated['password'] = Hash::make($validated['password']);
        $validated['is_active'] = $request->has('is_active');

        User::create($validated);

        return redirect()->route('users.index')
                        ->with('success', 'Utilisateur créé avec succès');
    }

    public function show(User $user)
    {
        $this->authorize('view', $user);
        $user->loadCount(['invoices', 'proformas'])->load(['invoices', 'proformas']);
        return view('users.show', compact('user'));
    }

    public function edit(User $user)
    {
        $this->authorize('update', $user);
        $roles = auth()->user()->isAdmin()
            ? ['admin' => 'Admin', 'manager' => 'Manager', 'user' => 'User']
            : ['user' => 'User'];
        return view('users.edit', compact('user','roles'));
    }

    public function update(Request $request, User $user)
    {
        $this->authorize('update', $user);
        $validated = $this->validateRequest($request, $user);

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

        if ($validated['password']) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $validated['is_active'] = $request->has('is_active');

        $user->update($validated);

        return redirect()->route('users.index')
                        ->with('success', 'Utilisateur mis à jour avec succès');
    }

    public function destroy(User $user)
    {
        $this->authorize('delete', $user);
        if ($user->id === auth()->id()) {
            return redirect()->route('users.index')
                            ->with('error', 'Vous ne pouvez pas supprimer votre propre compte');
        }

        if ($user->invoices()->count() > 0 || $user->proformas()->count() > 0) {
            return redirect()->route('users.index')
                            ->with('error', 'Impossible de supprimer cet utilisateur car il a des factures ou proformas associées');
        }

        // Supprimer l'avatar s'il existe
        if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
            Storage::disk('public')->delete($user->avatar);
        }

        $user->delete();

        return redirect()->route('users.index')
                        ->with('success', 'Utilisateur supprimé avec succès');
    }

    protected function validateRequest(Request $request, ?User $user = null): array
    {
        $rules = [
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users')->ignore($user ? $user->id : null)],
            'password' => 'nullable|string|min:8|confirmed',
            'role' => 'required|in:admin,manager,user',
            'is_active' => 'boolean',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ];

        // Pour les managers, forcer le rôle 'user'
        if (auth()->user()->isManager()) {
            $request->merge(['role' => 'user']);
        }

        return $request->validate($rules);
    }

    /**
     * Supprimer l'avatar d'un utilisateur
     */
        public function deleteAvatar(User $user)
        {
            $this->authorize('update', $user);

            if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
                $user->update(['avatar' => null]);
            }

            return redirect()->route('users.edit', $user)
                            ->with('success', 'Avatar supprimé avec succès');
        }
}
