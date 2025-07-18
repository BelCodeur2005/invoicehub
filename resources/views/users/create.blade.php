@extends('layouts.app')
@section('title', 'Nouvel Utilisateur')
@section('page-title', 'Créer un Nouvel Utilisateur')
@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Nouvel Utilisateur</h1>
            <p class="mt-2 text-sm text-gray-600">Créez un nouveau compte utilisateur</p>
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
            <form action="{{ route('users.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf

                <!-- Avatar Section -->
                <div class="flex items-center space-x-6">
                    <div class="shrink-0">
                        <div class="relative">
                            <img id="avatar-preview"
                                 src="{{ asset('images/default-avatar.png') }}"
                                 alt="Avatar"
                                 class="h-20 w-20 rounded-full object-cover border-2 border-gray-300">
                            <label for="avatar" class="absolute bottom-0 right-0 bg-blue-600 hover:bg-blue-700 text-white rounded-full p-1 cursor-pointer transition-colors">
                                <i class="fas fa-camera text-xs"></i>
                                <input type="file"
                                       id="avatar"
                                       name="avatar"
                                       accept="image/*"
                                       class="hidden"
                                       onchange="previewAvatar(event)">
                            </label>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Photo de profil
                        </label>
                        <p class="text-sm text-gray-500">
                            Formats acceptés : JPG, PNG, GIF (max 2MB)
                        </p>
                        @error('avatar')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
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
                               value="{{ old('name') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('name') border-red-500 @enderror"
                               placeholder="Nom complet de l'utilisateur">
                        @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                            Adresse email <span class="text-red-500">*</span>
                        </label>
                        <input type="email"
                               id="email"
                               name="email"
                               value="{{ old('email') }}"
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
                        <input type="tel"
                               id="phone"
                               name="phone"
                               value="{{ old('phone') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('phone') border-red-500 @enderror"
                               placeholder="+237 6XX XX XX XX">
                        @error('phone')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Address -->
                    <div class="md:col-span-2">
                        <label for="address" class="block text-sm font-medium text-gray-700 mb-2">
                            Adresse
                        </label>
                        <textarea id="address"
                                  name="address"
                                  rows="3"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('address') border-red-500 @enderror"
                                  placeholder="Adresse complète">{{ old('address') }}</textarea>
                        @error('address')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Password -->
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                            Mot de passe <span class="text-red-500">*</span>
                        </label>
                        <input type="password"
                               id="password"
                               name="password"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('password') border-red-500 @enderror"
                               placeholder="Mot de passe">
                        @error('password')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Password Confirmation -->
                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                            Confirmer le mot de passe <span class="text-red-500">*</span>
                        </label>
                        <input type="password"
                               id="password_confirmation"
                               name="password_confirmation"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               placeholder="Confirmer le mot de passe">
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
                            <option value="">Sélectionnez un rôle</option>
                            @foreach($roles as $value => $label)
                                <option value="{{ $value }}" {{ old('role') == $value ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('role')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    @else
                        <!-- Champ caché pour les managers (forcé à 'user') -->
                        <input type="hidden" name="role" value="user">
                    @endif

                    <!-- Status -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Statut</label>
                        <div class="flex items-center">
                            <input type="checkbox"
                                   id="is_active"
                                   name="is_active"
                                   value="1"
                                   {{ old('is_active', true) ? 'checked' : '' }}
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
                            <span>Accès complet à toutes les fonctionnalités incluant la gestion de tout les utilisateurs</span>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Actions -->
                <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200">
                    <a href="{{ route('users.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg transition-colors">
                        Annuler
                    </a>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors inline-flex items-center">
                        <i class="fas fa-save mr-2"></i>
                        Créer l'utilisateur
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function previewAvatar(event) {
    const file = event.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('avatar-preview').src = e.target.result;
        }
        reader.readAsDataURL(file);
    }
}
</script>
@endsection
