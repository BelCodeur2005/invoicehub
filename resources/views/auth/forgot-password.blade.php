{{-- resources/views/auth/forgot-password.blade.php --}}
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Mot de passe oublié - Invoice Hub</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100">
    <div class="min-h-screen flex items-center justify-center px-4">
        <div class="bg-white/80 backdrop-blur-sm rounded-3xl shadow-2xl px-12 py-7 w-full max-w-xl">
            <div class="text-center mb-8">
                <h1 class="text-3xl font-bold text-gray-800 mb-2">Mot de passe oublié</h1>
                <p class="text-gray-600">Entrez votre email pour recevoir un code de récupération</p>
            </div>

            @if(session('status'))
                <div class="mb-6 text-green-600 font-medium text-center bg-green-50 p-3 rounded-xl">
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('password.email') }}" class="space-y-6">
                @csrf

                <div>
                    <input id="email" name="email" type="email" value="{{ old('email') }}" required
                        placeholder="Votre adresse email..."
                        class="w-full px-6 py-4 bg-gray-100 border-0 rounded-2xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition-all duration-200 text-gray-700 placeholder-gray-400" />
                    @error('email')
                        <p class="text-red-500 text-sm mt-2 ml-2">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit"
                    class="w-full bg-gradient-to-r from-orange-400 to-orange-500 hover:from-orange-500 hover:to-orange-600 text-white font-semibold py-4 rounded-2xl transition-all duration-200 transform hover:scale-105 shadow-lg hover:shadow-xl">
                    Envoyer le code
                </button>

                <div class="text-center">
                    <a href="{{ route('login') }}" class="text-gray-400 hover:text-gray-600 text-sm transition-colors duration-200">
                        Retour à la connexion
                    </a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
