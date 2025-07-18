@extends('layouts.app')
@section('title', 'Modifier Utilisateur')
@section('page-title', 'Modifier l\'Utilisateur')
@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Modifier l'Utilisateur</h1>
            <p class="mt-2 text-sm text-gray-600">Modifiez les informations de {{ $user->name }}</p>
        </div>
        <a href="{{ route('users.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg transition-colors inline-flex items-center">
            <i class="fas fa-arrow-left mr-2"></i>
            Retour à la liste
        </a>
    </div>

    <!-- Form -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="p-6 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">Informations de l'utilisateur</h2>
        </div>
        <div class="p-6">
            <form action="{{ route('users.update', $user) }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Name -->
                    <div class="md:col-span-2">
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                            Nom complet <span class="text-red-500">*</span>
                        </label>
                        <input type="text"
                               id="name"
                               name="name"
                               value="{{ old('name', $user->name) }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('name') border-red-500 @enderror"
                               placeholder="Nom complet de l'utilisateur">
                        @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div class="md:col-span-2">
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                            Adresse email <span class="text-red-500">*</span>
                        </label>
                        <input type="email"
                               id="email"
                               name="email"
                               value="{{ old('email', $user->email) }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('email') border-red-500 @enderror"
                               placeholder="adresse@example.com">
                        @error('email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Password -->
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                            Nouveau mot de passe
                        </label>
                        <input type="password"
                               id="password"
                               name="password"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('password') border-red-500 @enderror"
                               placeholder="Laissez vide pour garder l'ancien">
                        @error('password')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-sm text-gray-500">Laissez vide si vous ne souhaitez pas changer le mot de passe</p>
                    </div>

                    <!-- Password Confirmation -->
                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                            Confirmer le nouveau mot de passe
                        </label>
                        <input type="password"
                               id="password_confirmation"
                               name="password_confirmation"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               placeholder="Confirmer le nouveau mot de passe">
                    </div>

                    <!-- Role (seulement visible pour les admins) -->
                    @if(auth()->user()->isAdmin())
                    <div>
                        <label for="role" class="block text-sm font-medium text-gray-700 mb-2">
                            Rôle <span class="text-red-500">*</span>
                        </label>
                        <select id="role"
                                name="role"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('role') border-red-500 @enderror">
                            @foreach($roles as $value => $label)
                                <option value="{{ $value }}" {{ old('role', $user->role) == $value ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('role')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    @else
                        <!-- Champ caché pour les managers (forcé à 'user') -->
                        <input type="hidden" name="role" value="{{ $user->role }}">
                    @endif

                    <!-- Status -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Statut</label>
                        <div class="flex items-center">
                            <input type="checkbox"
                                   id="is_active"
                                   name="is_active"
                                   value="1"
                                   {{ old('is_active', $user->is_active) ? 'checked' : '' }}
                                   class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            <label for="is_active" class="ml-2 text-sm text-gray-700">
                                Compte actif
                            </label>
                        </div>
                        <p class="mt-1 text-sm text-gray-500">Un compte actif permet à l'utilisateur de se connecter</p>
                    </div>
                </div>

                <!-- Role Descriptions (seulement visible pour les admins) -->
                @if(auth()->user()->isAdmin())                <div class="bg-gray-50 rounded-lg p-4">
                    <h3 class="text-sm font-medium text-gray-700 mb-3">Description des rôles</h3>
                    <div class="space-y-2 text-sm text-gray-600">
                        <div class="flex items-start">
                            <span class="bg-green-100 text-green-800 px-2 py-1 rounded-full text-xs font-medium mr-3 mt-0.5">Utilisateur</span>
                            <span>Accès de base aux fonctionnalités de l'application</span>
                        </div>
                        <div class="flex items-start">
                            <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded-full text-xs font-medium mr-3 mt-0.5">Manager</span>
                            <span>Peut gérer les utilisateurs, factures, clients et produits</span>
                        </div>
                        <div class="flex items-start">
                            <span class="bg-red-100 text-red-800 px-2 py-1 rounded-full text-xs font-medium mr-3 mt-0.5">Admin</span>
                            <span>Accès complet à toutes les fonctionnalités incluant la gestion de tout les utilisateurs</span>
                        </div>
                    </div>
                </div>
                @endif

                <!-- User Stats -->
                <div class="bg-blue-50 rounded-lg p-4">
                    <h3 class="text-sm font-medium text-gray-700 mb-3">Statistiques de l'utilisateur</h3>
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div class="text-center">
                            <div class="text-2xl font-bold text-blue-600">{{ $user->invoices_count ?? 0 }}</div>
                            <div class="text-gray-600">Factures créées</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-green-600">{{ $user->proformas_count ?? 0 }}</div>
                            <div class="text-gray-600">Proformas créées</div>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200">
                    <a href="{{ route('users.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg transition-colors">
                        Annuler
                    </a>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors inline-flex items-center">
                        <i class="fas fa-save mr-2"></i>
                        Mettre à jour
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
