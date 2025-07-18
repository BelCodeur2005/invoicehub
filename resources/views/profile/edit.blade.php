@extends('layouts.app')

@section('title', 'Modifier le profil')
@section('page-title', 'Modifier le profil')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-lg shadow-sm p-6">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold text-gray-900">Modifier le profil</h1>
            <a href="{{ route('profile.show') }}" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-arrow-left mr-2"></i>
                Retour au profil
            </a>
        </div>

        <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PATCH')

            <!-- Avatar -->
            <div class="flex items-center space-x-6">
                <div class="shrink-0">
                    @if($user->avatar)
                        <img class="h-16 w-16 rounded-full object-cover"
                             src="{{ asset('storage/' . $user->avatar) }}"
                             alt="{{ $user->name }}" id="avatar-preview">
                    @else
                        <div class="h-16 w-16 rounded-full bg-blue-500 flex items-center justify-center" id="avatar-preview">
                            <span class="text-xl font-bold text-white">{{ substr($user->name, 0, 1) }}</span>
                        </div>
                    @endif
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Photo de profil</label>
                    <div class="flex items-center space-x-4">
                        <label class="cursor-pointer bg-white px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">
                            <i class="fas fa-upload mr-2"></i>
                            Choisir une photo
                            <input type="file" name="avatar" accept="image/*" class="hidden" id="avatar-input">
                        </label>
                        @if($user->avatar)
                            <button type="button" onclick="deleteAvatar()" class="text-red-600 hover:text-red-800 text-sm">
                                <i class="fas fa-trash mr-1"></i>
                                Supprimer
                            </button>
                        @endif
                    </div>
                    <p class="text-xs text-gray-500 mt-1">JPG, PNG, GIF jusqu'à 2MB</p>
                    @error('avatar')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Informations de base -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Nom complet *</label>
                    <input type="text"
                           name="name"
                           id="name"
                           value="{{ old('name', $user->name) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           required>
                    @error('name')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email *</label>
                    <input type="email"
                           name="email"
                           id="email"
                           value="{{ old('email', $user->email) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           required>
                    @error('email')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">Téléphone</label>
                    <input type="tel"
                           name="phone"
                           id="phone"
                           value="{{ old('phone', $user->phone) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           placeholder="+237 6XX XX XX XX">
                    @error('phone')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Adresse -->
            <div>
                <label for="address" class="block text-sm font-medium text-gray-700 mb-2">Adresse</label>
                <textarea name="address"
                          id="address"
                          rows="3"
                          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                          placeholder="Votre adresse complète">{{ old('address', $user->address) }}</textarea>
                @error('address')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Boutons d'action -->
            <div class="flex items-center justify-end space-x-4 pt-6 border-t">
                <a href="{{ route('profile.show') }}" class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">
                    Annuler
                </a>
                <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 transition-colors">
                    <i class="fas fa-save mr-2"></i>
                    Sauvegarder
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    // Prévisualisation de l'avatar
    document.getElementById('avatar-input').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('avatar-preview').innerHTML = `
                    <img class="h-16 w-16 rounded-full object-cover" src="${e.target.result}" alt="Aperçu">
                `;
            }
            reader.readAsDataURL(file);
        }
    });

    // Supprimer l'avatar
    function deleteAvatar() {
        if (confirm('Êtes-vous sûr de vouloir supprimer votre photo de profil ?')) {
            fetch('{{ route("profile.avatar.delete") }}', {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json',
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert('Erreur lors de la suppression de l\'avatar');
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                alert('Erreur lors de la suppression de l\'avatar');
            });
        }
    }
</script>
@endsection
