{{-- resources/views/auth/verify-otp.blade.php --}}
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Vérification du code - Invoice Hub</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100">
    <div class="min-h-screen flex items-center justify-center px-4">
        <div class="bg-white/80 backdrop-blur-sm rounded-3xl shadow-2xl px-12 py-7 w-full max-w-xl">
            <div class="text-center mb-8">
                <h1 class="text-3xl font-bold text-gray-800 mb-2">Vérification du code</h1>
                <p class="text-gray-600">Entrez le code de 6 chiffres envoyé à {{ session('email') }}</p>
            </div>

            <form method="POST" action="{{ route('password.verify-otp.submit') }}" class="space-y-6">
                @csrf
                <input type="hidden" name="email" value="{{ session('email') }}">

                <div>
                    <input id="otp" name="otp" type="text" maxlength="6" required
                        placeholder="Code de vérification (6 chiffres)"
                        class="w-full px-6 py-4 bg-gray-100 border-0 rounded-2xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition-all duration-200 text-gray-700 placeholder-gray-400 text-center text-2xl tracking-widest" />
                    @error('otp')
                        <p class="text-red-500 text-sm mt-2 ml-2">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit"
                    class="w-full bg-gradient-to-r from-orange-400 to-orange-500 hover:from-orange-500 hover:to-orange-600 text-white font-semibold py-4 rounded-2xl transition-all duration-200 transform hover:scale-105 shadow-lg hover:shadow-xl">
                    Vérifier le code
                </button>
            </form>
        </div>
    </div>
</body>
</html>
