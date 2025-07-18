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
        <div class="flex space-x-3">
            <a href="{{ route('users.show', $user) }}" class="bg-green-100 hover:bg-green-200 text-green-700 px-4 py-2 rounded-lg transition-colors inline-flex items-center">
                <i class="fas fa-eye mr-2"></i>
                Voir le profil
            </a>
            <a href="{{ route('users.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg transition-colors inline-flex items-center">
                <i class="fas fa-arrow-left mr-2"></i>
                Retour à la liste
            </a>
        </div>
    </div>

    <!-- Form -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="p-6 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">Informations de l'utilisateur</h2>
        </div>
        <div class="p-6">
            <form action="{{ route('users.update', $user) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf
                @method('PUT')

                <!-- Avatar Section -->
                <div class="bg-gray-50 rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Photo de profil</h3>
                    <div class="flex items-center space-x-6">
                        <div class="flex-shrink-0">
                            @if($user->avatar)
                                <img src="{{ Storage::url($user->avatar) }}"
                                     alt="Avatar de {{ $user->name }}"
                                     class="w-20 h-20 rounded-full object-cover border-4 border-gray-200 shadow-sm"
                                     id="avatar-preview">
                            @else
                                <div class="w-20 h-20 rounded-full bg-gray-200 flex items-center justify-center border-4 border-gray-200 shadow-sm" id="avatar-preview">
                                    <i class="fas fa-user text-gray-400 text-xl"></i>
                                </div>
                            @endif
                        </div>
                        <div class="flex-1">
                            <div class="flex items-center space-x-3">
                                <label class="bg-white hover:bg-gray-50 text-gray-700 px-4 py-2 rounded-lg border border-gray-300 cursor-pointer transition-colors inline-flex items-center">
                                    <i class="fas fa-upload mr-2"></i>
                                    Choisir une nouvelle photo
                                    <input type="file"
                                           name="avatar"
                                           accept="image/*"
                                           class="hidden"
                                           id="avatar-input">
                                </label>
                                @if($user->avatar)
                                    <a href="{{ route('users.delete-avatar', $user) }}"
                                       class="bg-red-100 hover:bg-red-200 text-red-700 px-4 py-2 rounded-lg transition-colors inline-flex items-center"
                                       onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet avatar ?')">
                                        <i class="fas fa-trash mr-2"></i>
                                        Supprimer
                                    </a>
                                @endif
                            </div>
                            <p class="mt-2 text-sm text-gray-500">
                                JPG, PNG, GIF jusqu'à 2MB. Recommandé : 400x400px.
                            </p>
                            @error('avatar')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

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

                    <!-- Phone -->
                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">
                            Téléphone
                        </label>
                        <input type="text"
                               id="phone"
                               name="phone"
                               value="{{ old('phone', $user->phone) }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('phone') border-red-500 @enderror"
                               placeholder="+237 6XX XXX XXX">
                        @error('phone')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
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

                    <!-- Address -->
                    <div class="md:col-span-2">
                        <label for="address" class="block text-sm font-medium text-gray-700 mb-2">
                            Adresse
                        </label>
                        <textarea id="address"
                                  name="address"
                                  rows="3"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('address') border-red-500 @enderror"
                                  placeholder="Adresse complète de l'utilisateur">{{ old('address', $user->address) }}</textarea>
                        @error('address')
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

                    <!-- Status -->
                    <div class="md:col-span-2">
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
                @if(auth()->user()->isAdmin())
                <div class="bg-gray-50 rounded-lg p-4">
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
                            <span>Accès complet à toutes les fonctionnalités incluant la gestion de tous les utilisateurs</span>
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

<!-- JavaScript pour la prévisualisation de l'avatar -->
<script>
document.getElementById('avatar-input').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const preview = document.getElementById('avatar-preview');
            preview.innerHTML = `<img src="${e.target.result}" alt="Prévisualisation" class="w-20 h-20 rounded-full object-cover border-4 border-gray-200 shadow-sm">`;
        };
        reader.readAsDataURL(file);
    }
});
</script>
@endsection
