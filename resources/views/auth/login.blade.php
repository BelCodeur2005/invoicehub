<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Connexion - Invoice Hub</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(180deg); }
        }

        @keyframes float-reverse {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(20px) rotate(-180deg); }
        }

        .floating-element {
            animation: float 6s ease-in-out infinite;
        }

        .floating-element-reverse {
            animation: float-reverse 8s ease-in-out infinite;
        }

        .floating-element:nth-child(2) {
            animation-delay: 2s;
        }

        .floating-element:nth-child(3) {
            animation-delay: 4s;
        }

        .blur-bg {
            backdrop-filter: blur(10px);
            background: rgba(255, 255, 255, 0.1);
        }
    </style>
</head>
<body class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 overflow-hidden relative">
    <!-- Floating Elements -->
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
        <!-- Orange sphere top left -->
        <div class="floating-element absolute top-32 left-32 w-24 h-24 bg-gradient-to-br from-orange-400 to-orange-500 rounded-full shadow-2xl opacity-80"></div>

        <!-- Blue sphere bottom left -->
        <div class="floating-element-reverse absolute bottom-16 left-16 w-32 h-32 bg-gradient-to-br from-blue-400 to-blue-500 rounded-full shadow-2xl opacity-80"></div>

        <!-- Orange sphere bottom right -->
        <div class="floating-element absolute bottom-32 right-32 w-28 h-28 bg-gradient-to-br from-orange-300 to-orange-400 rounded-full shadow-2xl opacity-80"></div>

        <!-- Small dark sphere top right -->
        <div class="floating-element-reverse absolute top-20 right-20 w-16 h-16 bg-gradient-to-br from-gray-600 to-gray-700 rounded-full shadow-2xl opacity-70"></div>

        <!-- Small dark sphere bottom right -->
        <div class="floating-element absolute bottom-20 right-16 w-12 h-12 bg-gradient-to-br from-gray-500 to-gray-600 rounded-full shadow-xl opacity-60"></div>
    </div>

    <!-- Main Content -->
    <div class="min-h-screen flex items-center justify-center relative z-10 px-4">
        <div class="bg-white/80 backdrop-blur-sm rounded-3xl shadow-2xl px-12 py-7 w-full max-w-xl border border-white/20">
            <!-- Logo and Title -->
            <div class="text-center mb-8">
                <div class="flex items-center justify-center mb-4">
                    <div class="rounded-lg p-2 mr-3">
                        <img src="{{ asset('storage/images/Logoinvoice.png') }}" alt="Invoice Hub Logo" class="w-[180px] h-[180px] object-contain" />
                    </div>
                </div>
                <h1 class="text-3xl font-bold text-gray-800 mb-2">Connexion</h1>
            </div>

            @if(session('status'))
                <div class="mb-6 text-green-600 font-medium text-center bg-green-50 p-3 rounded-xl">
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" novalidate class="space-y-6">
                @csrf

                <!-- Email Field -->
                <div>
                    <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus
                        placeholder="Email..."
                        class="w-full px-6 py-4 bg-gray-100 border-0 rounded-2xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition-all duration-200 text-gray-700 placeholder-gray-400" />
                    @error('email')
                        <p class="text-red-500 text-sm mt-2 ml-2">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password Field -->
                <div>
                    <input id="password" name="password" type="password" required
                        placeholder="Mot de passe..."
                        class="w-full px-6 py-4 bg-gray-100 border-0 rounded-2xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition-all duration-200 text-gray-700 placeholder-gray-400" />
                    @error('password')
                        <p class="text-red-500 text-sm mt-2 ml-2">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Forgot Password Link -->
                <div class="text-center">
                    <a href="{{ route('password.request') }}" class="text-gray-400 hover:text-gray-600 text-sm transition-colors duration-200">
                        Mot de passe oublié ?
                    </a>
                </div>
                <!-- Submit Button -->
                <button type="submit"
                    class="w-full bg-gradient-to-r from-orange-400 to-orange-500 hover:from-orange-500 hover:to-orange-600 text-white font-semibold py-4 rounded-2xl transition-all duration-200 transform hover:scale-105 shadow-lg hover:shadow-xl">
                    connecte toi
                </button>
            </form>
        </div>
    </div>
</body>
</html>
