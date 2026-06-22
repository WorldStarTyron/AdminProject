<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Password - Security Portal</title>
    <meta name="description" content="Create a new secure password for your account.">

    {{-- Font Awesome 6 --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    @vite(['resources/css/app.css', 'resources/js/UI/Sidebar.js', 'resources/js/Auth/ShowPassword.js'])
</head>

<!-- Pagina achtergrond: lichtgrijs -->
<body class="bg-gray-50 min-h-screen flex flex-col items-center justify-center px-4 py-8">

    <!-- Header: Slot-icoon + paginatitel -->
    <div class="flex flex-col items-center mb-8">

        <!-- Donker vierkant met slot-icoon -->
        <div class="w-16 h-16 bg-gray-900 rounded-2xl flex items-center justify-center mb-4 shadow-lg">
            <i class="fa-solid fa-lock text-white text-2xl"></i>
        </div>

        <!-- Portaalnaam -->
        <h1 class="text-xl font-bold text-gray-900 tracking-tight">Security Portal</h1>

        <!-- Ondertitel -->
        <p class="text-sm text-gray-500 mt-1">Set New Password</p>
    </div>

    <!-- Formulierkaart -->
    <div class="w-full max-w-sm bg-white rounded-xl shadow-sm border border-gray-200 p-6">

        <!-- Kaartkopping -->
        <h2 class="text-lg font-semibold text-gray-900 mb-1">Create New Password</h2>

        <!-- Beschrijving -->
        <p class="text-sm text-gray-500 mb-6 leading-relaxed">
            Enter a strong new password for your account. Make sure it's at least 8 characters long.
        </p>

        <!-- Formulier -->
        <form method="POST" action="{{ route('new-password.post') }}" class="space-y-5">
            @csrf

            <!-- Nieuw wachtwoord veld -->
            <div>
                <label for="password" class="block text-xs font-semibold text-gray-700 tracking-wide mb-2">
                    NEW PASSWORD
                </label>

                <div class="relative">
                    <input
                        type="password"
                        id="password"
                        name="password"
                        placeholder="Enter new password"
                        required
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-gray-900 text-sm placeholder-gray-400 focus:outline-none focus:border-gray-400 focus:ring-1 focus:ring-gray-300 transition pr-10 @error('password') border-red-400 @enderror">

                    <!-- Oog-icoon: wisselt tussen zichtbaar/verborgen wachtwoord -->
                     <button
                        type="button"
                        id="togglePassword"
                        class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-gray-600 transition"
                        aria-label="Wachtwoord tonen of verbergen">
                        <i id="openEyes" class="fa-regular fa-eye text-base"></i>
                        <i id="closeEyes" class="fa-regular fa-eye-slash text-base" style="display: none;"></i>
                     </button>
                </div>

                @error('password')
                    <p class="text-red-500 text-xs mt-1.5">{{ $message }}</p>
                @enderror
            </div>

            <!-- Wachtwoord bevestigen veld -->
            <div>
                <label for="password_confirmation" class="block text-xs font-semibold text-gray-700 tracking-wide mb-2">
                    CONFIRM PASSWORD
                </label>

                <div class="relative">
                    <input
                        type="password"
                        id="password_confirmation"
                        name="password_confirmation"
                        placeholder="Confirm new password"
                        required
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-gray-900 text-sm placeholder-gray-400 focus:outline-none focus:border-gray-400 focus:ring-1 focus:ring-gray-300 transition pr-10">

                    <!-- Oog-icoon voor bevestigingsveld -->
                   <button
        type="button"
        id="togglePassword"
        class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-gray-600 transition"
        aria-label="Wachtwoord tonen of verbergen">
        <i id="openEyes" class="fa-regular fa-eye text-base"></i>
        <i id="closeEyes" class="fa-regular fa-eye-slash text-base" style="display: none;"></i>
    </button>
                </div>
            </div>

            <!-- Sessie-foutmelding (bijv. wachtwoorden komen niet overeen) -->
            @if(session('error'))
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm">
                    <i class="fa-solid fa-circle-exclamation mr-1.5"></i>
                    {{ session('error') }}
                </div>
            @endif

            <!-- Sessie-succesmelding -->
            @if(session('success'))
                <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg text-sm">
                    <i class="fa-solid fa-circle-check mr-1.5"></i>
                    {{ session('success') }}
                </div>
            @endif

            <!-- Verzendknop -->
            <button
                type="submit"
                class="w-full bg-gray-900 hover:bg-gray-800 text-white font-medium py-2.5 px-4 rounded-lg transition duration-200 text-sm flex items-center justify-center gap-2"
            >
                Reset Password
                <i class="fa-solid fa-arrow-right text-sm"></i>
            </button>
        </form>
    </div>

    <!-- Terug naar inloggen -->
    <div class="mt-6">
        <a href="{{ route('login') }}" class="text-sm text-gray-600 hover:text-gray-900 font-medium transition flex items-center gap-1.5">
            <i class="fa-solid fa-arrow-left text-sm"></i>
            Back to Login
        </a>
    </div>

    <!-- Footer -->
    <footer class="mt-10 text-center">
        <p class="text-xs text-gray-400">
            &copy; {{ date('Y') }} SecureAdmin Systems. All rights reserved.
        </p>
        <div class="flex items-center justify-center gap-4 mt-2">
            <a href="#" class="text-xs text-gray-500 hover:text-gray-700 font-medium transition">Help Center</a>
            <a href="#" class="text-xs text-gray-500 hover:text-gray-700 font-medium transition">Privacy Policy</a>
        </div>
    </footer>



</body>
</html>