@extends('layouts.app')

@section('title', 'Mon Profil')
@section('page-title', 'Mon Profil')

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
    <!-- En-tête du profil amélioré -->
    <div class="bg-white rounded-xl shadow-sm overflow-hidden mb-8 border border-gray-100">
        <div class="bg-gradient-to-r from-indigo-500 to-purple-600 h-40 relative">
            <div class="absolute inset-0 bg-gradient-to-t from-black/10 to-transparent"></div>
            <!-- Déplacé à l'intérieur du conteneur flex pour un meilleur contrôle -->
        </div>

        <div class="px-6 py-4 relative">
            <div class="flex flex-col sm:flex-row items-start sm:items-end">
                <!-- Image de profil avec marge ajustée -->
                <div class="flex-shrink-0 -mt-12 mb-4 sm:mb-0 sm:-mt-16">
                    @if($user->avatar)
                        <img class="h-24 w-24 sm:h-28 sm:w-28 rounded-full border-4 border-white shadow-lg object-cover"
                             src="{{ asset('storage/' . $user->avatar) }}"
                             alt="{{ $user->name }}">
                    @else
                        <div class="h-24 w-24 sm:h-28 sm:w-28 rounded-full border-4 border-white shadow-lg bg-indigo-500 flex items-center justify-center">
                            <span class="text-3xl font-bold text-white">{{ substr($user->name, 0, 1) }}</span>
                        </div>
                    @endif
                </div>

                <!-- Conteneur des informations avec espacement ajusté -->
                <div class="sm:ml-6 flex-1">
                    <!-- Nom et email maintenant dans ce bloc -->
                    <div class="mb-3">
                        <h1 class="text-2xl font-bold text-gray-900">{{ $user->name }}</h1>
                        <p class="text-gray-600">{{ $user->email }}</p>
                    </div>

                    <!-- Badges -->
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-indigo-100 text-indigo-800">
                            <i class="fas fa-user-tag mr-1.5 text-xs"></i>
                            {{ ucfirst($user->role) }}
                        </span>
                        @if($user->is_active)
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-emerald-100 text-emerald-800">
                                <i class="fas fa-check-circle mr-1.5"></i>
                                Compte actif
                            </span>
                        @endif
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                            <i class="fas fa-calendar-alt mr-1.5"></i>
                            Membre depuis {{ $user->created_at->format('M Y') }}
                        </span>
                    </div>
                </div>

                <!-- Bouton déplacé vers la droite -->
                <div class="mt-4 sm:mt-0 sm:ml-auto">
                    <a href="{{ route('profile.edit') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition-all shadow-sm">
                        <i class="fas fa-user-edit mr-2"></i>
                        Modifier le profil
                    </a>
                </div>
            </div>
        </div>
    </div>


    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Section principale (2/3) -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Carte Informations personnelles -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50">
                    <h2 class="text-lg font-semibold text-gray-800 flex items-center">
                        <i class="fas fa-user-circle text-indigo-500 mr-2"></i>
                        Informations personnelles
                    </h2>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-1">
                            <label class="block text-sm font-medium text-gray-500">Nom complet</label>
                            <p class="text-gray-900 font-medium">{{ $user->name }}</p>
                        </div>

                        <div class="space-y-1">
                            <label class="block text-sm font-medium text-gray-500">Email</label>
                            <p class="text-gray-900 font-medium">{{ $user->email }}</p>
                        </div>

                        @if($user->phone)
                        <div class="space-y-1">
                            <label class="block text-sm font-medium text-gray-500">Téléphone</label>
                            <p class="text-gray-900 font-medium">{{ $user->phone }}</p>
                        </div>
                        @endif

                        @if($user->address)
                        <div class="space-y-1 md:col-span-2">
                            <label class="block text-sm font-medium text-gray-500">Adresse</label>
                            <p class="text-gray-900 font-medium">{{ $user->address }}</p>
                        </div>
                        @endif

                        @if($user->bio)
                        <div class="space-y-1 md:col-span-2">
                            <label class="block text-sm font-medium text-gray-500">À propos</label>
                            <p class="text-gray-900">{{ $user->bio }}</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

        </div>

        <!-- Sidebar (1/3) -->
        <div class="space-y-6">
            <!-- Carte Statistiques -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50">
                    <h2 class="text-lg font-semibold text-gray-800 flex items-center">
                        <i class="fas fa-chart-bar text-indigo-500 mr-2"></i>
                        Statistiques
                    </h2>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        <div class="flex items-center justify-between p-3 bg-indigo-50 rounded-lg">
                            <div class="flex items-center">
                                <div class="bg-indigo-100 p-2 rounded-lg text-indigo-600 mr-3">
                                    <i class="fas fa-file-invoice"></i>
                                </div>
                                <span class="text-sm font-medium text-gray-700">Factures</span>
                            </div>
                            <span class="text-lg font-bold text-indigo-600">{{ $user->invoices_count ?? 0 }}</span>
                        </div>

                        <div class="flex items-center justify-between p-3 bg-green-50 rounded-lg">
                            <div class="flex items-center">
                                <div class="bg-green-100 p-2 rounded-lg text-green-600 mr-3">
                                    <i class="fas fa-file-alt"></i>
                                </div>
                                <span class="text-sm font-medium text-gray-700">Proformas</span>
                            </div>
                            <span class="text-lg font-bold text-green-600">{{ $user->proformas_count ?? 0 }}</span>
                        </div>

                    </div>
                </div>
            </div>

            <!-- Carte Actions rapides -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50">
                    <h2 class="text-lg font-semibold text-gray-800 flex items-center">
                        <i class="fas fa-bolt text-indigo-500 mr-2"></i>
                        Actions rapides
                    </h2>
                </div>
                <div class="p-6">
                    <div class="space-y-2">
                        <a href="{{ route('profile.edit') }}" class="flex items-center w-full text-left p-3 rounded-lg hover:bg-indigo-50 transition-colors group">
                            <div class="bg-indigo-100 p-2 rounded-lg text-indigo-600 mr-3 group-hover:bg-indigo-200">
                                <i class="fas fa-user-edit"></i>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900">Modifier le profil</p>
                                <p class="text-xs text-gray-500">Mettre à jour vos informations</p>
                            </div>
                        </a>

                        <a href="{{ route('profile.password') }}" class="flex items-center w-full text-left p-3 rounded-lg hover:bg-yellow-50 transition-colors group">
                            <div class="bg-yellow-100 p-2 rounded-lg text-yellow-600 mr-3 group-hover:bg-yellow-200">
                                <i class="fas fa-lock"></i>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900">Changer le mot de passe</p>
                                <p class="text-xs text-gray-500">Mettre à jour votre sécurité</p>
                            </div>
                        </a>

                        <a href="{{ route('profile.settings') }}" class="flex items-center w-full text-left p-3 rounded-lg hover:bg-gray-50 transition-colors group">
                            <div class="bg-gray-100 p-2 rounded-lg text-gray-600 mr-3 group-hover:bg-gray-200">
                                <i class="fas fa-cog"></i>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900">Paramètres</p>
                                <p class="text-xs text-gray-500">Personnaliser votre expérience</p>
                            </div>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Carte Préférences -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50">
                    <h2 class="text-lg font-semibold text-gray-800 flex items-center">
                        <i class="fas fa-sliders-h text-indigo-500 mr-2"></i>
                        Préférences
                    </h2>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div class="flex items-center">
                                <i class="fas fa-language text-gray-500 mr-3"></i>
                                <span class="text-sm font-medium text-gray-700">Langue</span>
                            </div>
                            <span class="text-sm font-medium text-gray-900">{{ $user->language === 'fr' ? 'Français' : 'English' }}</span>
                        </div>

                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div class="flex items-center">
                                <i class="fas fa-globe text-gray-500 mr-3"></i>
                                <span class="text-sm font-medium text-gray-700">Fuseau horaire</span>
                            </div>
                            <span class="text-sm font-medium text-gray-900">{{ $user->timezone }}</span>
                        </div>

                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div class="flex items-center">
                                <i class="fas fa-palette text-gray-500 mr-3"></i>
                                <span class="text-sm font-medium text-gray-700">Thème</span>
                            </div>
                            <span class="text-sm font-medium text-gray-900">{{ ucfirst($user->theme) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
