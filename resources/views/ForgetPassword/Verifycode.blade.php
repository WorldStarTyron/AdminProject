<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Code - Security Portal</title>
    <meta name="description" content="Enter the 6-digit verification code sent to your email to reset your password.">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    @vite(['resources/css/app.css', 'resources/js/UI/Sidebar.js'])
</head>

<body class="bg-gray-50 min-h-screen flex flex-col items-center justify-center px-4 py-8">

    <!-- Header Section -->
    <div class="flex flex-col items-center mb-8">

        <!-- Lock Icon -->
        <div class="w-16 h-16 bg-gray-900 rounded-2xl flex items-center justify-center mb-4 shadow-lg">
            <i class="fa-solid fa-lock text-white text-2xl"></i>
        </div>

        <!-- Portal Title -->
        <h1 class="text-xl font-bold text-gray-900 tracking-tight">Security Portal</h1>

        <!-- Subtitle -->
        <p class="text-sm text-gray-500 mt-2 text-center max-w-xs leading-relaxed">
            We've sent a 6-digit verification code to your registered email address. Please enter it below.
        </p>

        @if(session('reset_attempts') && session('reset_attempts') > 0)
            <p class="text-xs text-amber-600 mt-2 font-medium">
                {{ 5 - session('reset_attempts') }} poging(en) resterend
            </p>
        @endif
    </div>

    <!-- Main Card -->
    <div class="w-full max-w-sm bg-white rounded-xl shadow-sm border border-gray-200 p-6">

        <form method="POST" action="{{ route('verify-code.post') }}" class="space-y-5">
            @csrf

            <!-- Validation Error -->
            @error('code')
                <p class="text-red-500 text-xs text-center mt-1">{{ $message }}</p>
            @enderror

            <!-- 6-Digit Code Input -->
            <div class="flex items-center justify-center gap-3 mb-5">
                <input
                    type="tel"
                    inputmode="numeric"
                    autocomplete="one-time-code"
                    required
                    maxlength="6"
                    name="code"
                    class="w-full h-14 text-center text-lg font-semibold text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:outline-none focus:border-gray-500 focus:ring-1 focus:ring-gray-400 transition"
                    data-code-input
                    placeholder="Enter a 6 digit code">
            </div>

            <!-- Session Error -->
            @if(session('error'))
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm">
                    {{ session('error') }}
                </div>
            @endif

            <!-- Session Success -->
            @if(session('success'))
                <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg text-sm">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Submit Button -->
            <button type="submit" class="w-full bg-gray-900 hover:bg-gray-800 text-white font-medium py-2.5 px-4 rounded-lg transition duration-200 text-sm">
                Verify
            </button>

            <!-- Resend Code -->
            <div class="text-center">
                <a href="#" onclick="event.preventDefault(); document.getElementById('resend-form').submit();"
                   class="text-sm text-gray-600 hover:text-gray-900 font-medium transition inline-flex items-center gap-1.5">
                    <i class="fa-solid fa-rotate-right text-sm"></i>
                    Resend Code
                </a>
            </div>
        </form>

        <!-- Hidden Resend Form -->
        <form id="resend-form" method="POST" action="{{ route('recover-password.post') }}" class="hidden">
            @csrf
            <input type="hidden" name="email" value="{{ session('reset_email') }}">
        </form>
    </div>

    <!-- Back to Login -->
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

    @vite('resources/js/Auth/verifycode.js')

</body>
</html>