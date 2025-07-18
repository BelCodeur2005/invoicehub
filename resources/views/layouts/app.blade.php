<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Système de Facturation')</title>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Alpine.js pour les interactions -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Font Awesome pour les icônes -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <style>
        [x-cloak] { display: none !important; }

        /* Animations pour les éléments flottants */
        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(180deg); }
        }

        @keyframes float-reverse {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(20px) rotate(-180deg); }
        }

        @keyframes float-slow {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-30px) rotate(90deg); }
        }

        .floating-element {
            animation: float 8s ease-in-out infinite;
        }

        .floating-element-reverse {
            animation: float-reverse 10s ease-in-out infinite;
        }

        .floating-element-slow {
            animation: float-slow 12s ease-in-out infinite;
        }

        .floating-element:nth-child(2) {
            animation-delay: 3s;
        }

        .floating-element:nth-child(3) {
            animation-delay: 6s;
        }

        .floating-element:nth-child(4) {
            animation-delay: 9s;
        }

        /* Assurer que les éléments flottants ne gênent pas l'interaction */
        .floating-background {
            pointer-events: none;
            z-index: 0;
        }

        .content-layer {
            position: relative;
            z-index: 10;
        }

        /* Correction pour le scroll */
        body {
            overflow-x: hidden;
            overflow-y: auto;
        }

        .main-container {
            min-height: 100vh;
            display: flex;
            position: relative;
        }

        .sidebar {
            width: 256px;
            flex-shrink: 0;
            height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            overflow-y: auto;
        }

        .main-content {
            flex: 1;
            margin-left: 256px;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .content-area {
            flex: 1;
            overflow-y: auto;
        }
    </style>
</head>
<body class="bg-gray-50 relative">
    <!-- Éléments flottants en arrière-plan -->
    <div class="floating-background fixed inset-0 overflow-hidden">
        <!-- Sphère orange grande - coin supérieur gauche -->
        <div class="floating-element absolute top-10 left-10 w-20 h-20 bg-gradient-to-br from-orange-400 to-orange-500 rounded-full shadow-lg opacity-20"></div>

        <!-- Sphère bleue - coin inférieur gauche -->
        <div class="floating-element-reverse absolute bottom-20 left-20 w-24 h-24 bg-gradient-to-br from-blue-400 to-blue-500 rounded-full shadow-lg opacity-25"></div>

        <!-- Sphère orange moyenne - coin supérieur droit -->
        <div class="floating-element-slow absolute top-32 right-24 w-16 h-16 bg-gradient-to-br from-orange-300 to-orange-400 rounded-full shadow-lg opacity-20"></div>

        <!-- Sphère grise - coin inférieur droit -->
        <div class="floating-element absolute bottom-32 right-16 w-14 h-14 bg-gradient-to-br from-gray-400 to-gray-500 rounded-full shadow-lg opacity-15"></div>

        <!-- Sphère bleue petite - milieu droit -->
        <div class="floating-element-reverse absolute top-1/2 right-8 w-12 h-12 bg-gradient-to-br from-blue-300 to-blue-400 rounded-full shadow-lg opacity-20"></div>

        <!-- Sphère orange petite - milieu gauche -->
        <div class="floating-element-slow absolute top-1/3 left-8 w-10 h-10 bg-gradient-to-br from-orange-400 to-orange-500 rounded-full shadow-lg opacity-15"></div>

        <!-- Sphère grise petite - centre haut -->
        <div class="floating-element absolute top-16 left-1/2 w-8 h-8 bg-gradient-to-br from-gray-500 to-gray-600 rounded-full shadow-lg opacity-10"></div>
    </div>

    <!-- Container principal -->
    <div class="main-container">
        <!-- Sidebar -->
        <div class="sidebar bg-white/95 backdrop-blur-sm shadow-lg">
            <div class="p-6">
                <div class="flex items-center justify-center">
                    <div class="rounded-lg p-2">
                        <img src="{{asset('storage/images/Logoinvoice.png')}}" alt="Invoice Hub Logo" class="w-24 h-24 object-contain" />
                    </div>
                </div>
            </div>

            <nav class="mt-1">
                <div class="px-6 py-2">
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Menu Principal</p>
                </div>

                <a href="{{ route('dashboard') }}" class="flex items-center px-6 py-3 text-gray-700 hover:bg-blue-50 hover:text-blue-600 {{ request()->routeIs('dashboard') ? 'bg-blue-50 text-blue-600 border-r-2 border-blue-600' : '' }}">
                    <i class="fas fa-tachometer-alt mr-3"></i>
                    Dashboard
                </a>

                <a href="{{ route('invoices.index') }}" class="flex items-center px-6 py-3 text-gray-700 hover:bg-blue-50 hover:text-blue-600 {{ request()->routeIs('invoices.*') ? 'bg-blue-50 text-blue-600 border-r-2 border-blue-600' : '' }}">
                    <i class="fas fa-file-invoice mr-3"></i>
                    Factures
                </a>

                <a href="{{ route('proformas.index') }}" class="flex items-center px-6 py-3 text-gray-700 hover:bg-blue-50 hover:text-blue-600 {{ request()->routeIs('proformas.*') ? 'bg-blue-50 text-blue-600 border-r-2 border-blue-600' : '' }}">
                    <i class="fas fa-file-alt mr-3"></i>
                    Proformas
                </a>

                <a href="{{ route('clients.index') }}" class="flex items-center px-6 py-3 text-gray-700 hover:bg-blue-50 hover:text-blue-600 {{ request()->routeIs('clients.*') ? 'bg-blue-50 text-blue-600 border-r-2 border-blue-600' : '' }}">
                    <i class="fas fa-users mr-3"></i>
                    Clients
                </a>

                <a href="{{ route('products.index') }}" class="flex items-center px-6 py-3 text-gray-700 hover:bg-blue-50 hover:text-blue-600 {{ request()->routeIs('products.*') ? 'bg-blue-50 text-blue-600 border-r-2 border-blue-600' : '' }}">
                    <i class="fas fa-box mr-3"></i>
                    Produits
                </a>

                @if(auth()->user()->canManageUsers())
                <div class="px-6 py-2 mt-6">
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Administration</p>
                </div>

                <a href="{{ route('users.index') }}" class="flex items-center px-6 py-3 text-gray-700 hover:bg-blue-50 hover:text-blue-600 {{ request()->routeIs('users.*') ? 'bg-blue-50 text-blue-600 border-r-2 border-blue-600' : '' }}">
                    <i class="fas fa-user-cog mr-3"></i>
                    Utilisateurs
                </a>
                @endif
            </nav>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <!-- Top Bar -->
            <header class="bg-white/95 backdrop-blur-sm shadow-sm border-b border-gray-200">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between items-center h-16">
                        <div class="flex items-center">
                            <h2 class="text-xl font-semibold text-gray-800">@yield('page-title', 'Dashboard')</h2>
                        </div>

                        <div class="flex items-center space-x-4">
                            <!-- Notifications -->
                            <div class="relative" x-data="{ open: false }">
                                <button @click="open = !open" class="p-2 text-gray-400 hover:text-gray-600">
                                    <i class="fas fa-bell text-lg"></i>
                                    <span class="absolute -top-1 -right-1 h-4 w-4 bg-red-500 text-white text-xs rounded-full flex items-center justify-center">3</span>
                                </button>

                                <div x-show="open" @click.away="open = false" x-cloak class="absolute right-0 mt-2 w-80 bg-white/95 backdrop-blur-sm rounded-md shadow-lg z-50">
                                    <div class="py-1">
                                        <div class="px-4 py-2 text-sm text-gray-700 border-b">
                                            <strong>Notifications</strong>
                                        </div>
                                        <a href="#" class="flex items-center px-4 py-3 hover:bg-gray-50">
                                            <div class="flex-shrink-0">
                                                <i class="fas fa-exclamation-circle text-red-500"></i>
                                            </div>
                                            <div class="ml-3">
                                                <p class="text-sm text-gray-900">Factures en retard</p>
                                                <p class="text-xs text-gray-500">3 factures nécessitent votre attention</p>
                                            </div>
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <!-- User Menu -->
                            <div class="relative" x-data="{ open: false }">
                                <button @click="open = !open" class="flex items-center text-sm rounded-full focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    <div class="h-8 w-8 rounded-full bg-blue-500 flex items-center justify-center">
                                        <span class="text-white font-medium">{{ substr(auth()->user()->name, 0, 1) }}</span>
                                    </div>
                                    <span class="ml-3 text-gray-700">{{ auth()->user()->name }}</span>
                                    <i class="fas fa-chevron-down ml-2 text-gray-400"></i>
                                </button>

                                <div x-show="open" @click.away="open = false" x-cloak class="absolute right-0 mt-2 w-48 bg-white/95 backdrop-blur-sm rounded-md shadow-lg z-50">
                                    <div class="py-1">
                                        <a href="{{ route('profile.show')}}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                            <i class="fas fa-user mr-2"></i>Profil
                                        </a>
                                        <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                            <i class="fas fa-cog mr-2"></i>Paramètres
                                        </a>
                                        <div class="border-t border-gray-100"></div>
                                        <form method="POST" action="{{ route('logout') }}">
                                            @csrf
                                            <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                <i class="fas fa-sign-out-alt mr-2"></i>Déconnexion
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main class="content-area">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                    <!-- Flash Messages -->
                    @if(session('success'))
                    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" class="mb-6 bg-green-50/95 backdrop-blur-sm border border-green-200 rounded-md p-4">
                        <div class="flex">
                            <i class="fas fa-check-circle text-green-400 mr-3 mt-0.5"></i>
                            <div>
                                <p class="text-sm text-green-800">{{ session('success') }}</p>
                            </div>
                            <button @click="show = false" class="ml-auto">
                                <i class="fas fa-times text-green-400 hover:text-green-600"></i>
                            </button>
                        </div>
                    </div>
                    @endif

                    @if(session('error'))
                    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" class="mb-6 bg-red-50/95 backdrop-blur-sm border border-red-200 rounded-md p-4">
                        <div class="flex">
                            <i class="fas fa-exclamation-circle text-red-400 mr-3 mt-0.5"></i>
                            <div>
                                <p class="text-sm text-red-800">{{ session('error') }}</p>
                            </div>
                            <button @click="show = false" class="ml-auto">
                                <i class="fas fa-times text-red-400 hover:text-red-600"></i>
                            </button>
                        </div>
                    </div>
                    @endif

                    @if(session('info'))
                    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" class="mb-6 bg-blue-50/95 backdrop-blur-sm border border-blue-200 rounded-md p-4">
                        <div class="flex">
                            <i class="fas fa-info-circle text-blue-400 mr-3 mt-0.5"></i>
                            <div>
                                <p class="text-sm text-blue-800">{{ session('info') }}</p>
                            </div>
                            <button @click="show = false" class="ml-auto">
                                <i class="fas fa-times text-blue-400 hover:text-blue-600"></i>
                            </button>
                        </div>
                    </div>
                    @endif

                    @yield('content')
                </div>
            </main>
        </div>
    </div>

    <!-- Modal Container -->
    <div id="modal-container"></div>

    <script>
        // Fonction pour générer PDF
        function generatePDF(type, id) {
            const url = type === 'invoice' ? `/invoices/${id}/pdf` : `/proformas/${id}/pdf`;
            window.open(url, '_blank');
        }

        // Fonction pour envoyer par email
        function sendEmail(type, id) {
            const url = type === 'invoice' ? `/invoices/${id}/email` : `/proformas/${id}/email`;

            if (confirm('Êtes-vous sûr de vouloir envoyer ce document par email ?')) {
                fetch(url, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Email envoyé avec succès !');
                    } else {
                        alert('Erreur lors de l\'envoi de l\'email');
                    }
                })
                .catch(error => {
                    console.error('Erreur:', error);
                    alert('Erreur lors de l\'envoi de l\'email');
                });
            }
        }

        // Fonction pour confirmer la suppression
        function confirmDelete(form) {
            if (confirm('Êtes-vous sûr de vouloir supprimer cet élément ?')) {
                form.submit();
            }
        }
    </script>
</body>
</html>
