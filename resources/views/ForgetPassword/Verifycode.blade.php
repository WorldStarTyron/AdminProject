<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Code - Security Portal</title>
    <meta name="description" content="Enter the 6-digit verification code sent to your email to reset your password.">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<!-- Page Body: Light gray background -->
<body class="bg-gray-50 min-h-screen flex flex-col items-center justify-center px-4 py-8">

    <!-- Header Section: Lock Icon + Portal Title -->
    <div class="flex flex-col items-center mb-8">

        <!-- Lock Icon: Dark rounded square with lock SVG -->
        <div class="w-16 h-16 bg-gray-900 rounded-2xl flex items-center justify-center mb-4 shadow-lg">
            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" />
            </svg>
        </div>

        <!-- Portal Title -->
        <h1 class="text-xl font-bold text-gray-900 tracking-tight">Security Portal</h1>

        <!-- Subtitle / Instructions -->
        <p class="text-sm text-gray-500 mt-2 text-center max-w-xs leading-relaxed">
            We've sent a 6-digit verification code to your registered email address. Please enter it below.
        </p>
        @if(session('reset_attempts') && session('reset_attempts') > 0)
            <p class="text-xs text-amber-600 mt-2 font-medium">
                ⚠️ {{ 5 - session('reset_attempts') }} poging(en) resterend
            </p>
        @endif
    </div>

    <!-- Main Card: Verification Code Form -->
    <div class="w-full max-w-sm bg-white rounded-xl shadow-sm border border-gray-200 p-6">

        <!-- Verification Form -->
        <form method="POST" action="{{route('verify-code.post')}}" class="space-y-5">
            @csrf

            <!-- 6-Digit Code Input Fields -->
            <div class="flex items-center justify-center gap-3">

                <!-- Digit 1 -->
                <input
                    type="text"
                    name="code[]"
                    maxlength="1"
                    inputmode="numeric"
                    pattern="[0-9]"
                    required
                    autofocus
                    class="w-12 h-14 text-center text-lg font-semibold text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:outline-none focus:border-gray-500 focus:ring-1 focus:ring-gray-400 transition"
                    data-code-input
                >

                <!-- Digit 2 -->
                <input
                    type="text"
                    name="code[]"
                    maxlength="1"
                    inputmode="numeric"
                    pattern="[0-9]"
                    required
                    class="w-12 h-14 text-center text-lg font-semibold text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:outline-none focus:border-gray-500 focus:ring-1 focus:ring-gray-400 transition"
                    data-code-input
                >

                <!-- Digit 3 -->
                <input
                    type="text"
                    name="code[]"
                    maxlength="1"
                    inputmode="numeric"
                    pattern="[0-9]"
                    required
                    class="w-12 h-14 text-center text-lg font-semibold text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:outline-none focus:border-gray-500 focus:ring-1 focus:ring-gray-400 transition"
                    data-code-input
                >

                <!-- Digit 4 -->
                <input
                    type="text"
                    name="code[]"
                    maxlength="1"
                    inputmode="numeric"
                    pattern="[0-9]"
                    required
                    class="w-12 h-14 text-center text-lg font-semibold text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:outline-none focus:border-gray-500 focus:ring-1 focus:ring-gray-400 transition"
                    data-code-input
                >

                <!-- Digit 5 -->
                <input
                    type="text"
                    name="code[]"
                    maxlength="1"
                    inputmode="numeric"
                    pattern="[0-9]"
                    required
                    class="w-12 h-14 text-center text-lg font-semibold text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:outline-none focus:border-gray-500 focus:ring-1 focus:ring-gray-400 transition"
                    data-code-input
                >

                <!-- Digit 6 -->
                <input
                    type="text"
                    name="code[]"
                    maxlength="1"
                    inputmode="numeric"
                    pattern="[0-9]"
                    required
                    class="w-12 h-14 text-center text-lg font-semibold text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:outline-none focus:border-gray-500 focus:ring-1 focus:ring-gray-400 transition"
                    data-code-input
                >
            </div>

            <!-- Validation Error Message -->
            @error('code')
                <p class="text-red-500 text-xs text-center mt-1">{{ $message }}</p>
            @enderror

            <!-- Display Session Error -->
            @if(session('error'))
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm">
                    {{ session('error') }}
                </div>
            @endif

            <!-- Display Success Message -->
            @if(session('success'))
                <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg text-sm">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Submit Button: Verify -->
            <button
                type="submit"
                class="w-full bg-gray-900 hover:bg-gray-800 text-white font-medium py-2.5 px-4 rounded-lg transition duration-200 text-sm"
            >
                Verify
            </button>

             <!-- Resend Code Form & Link -->
             <div class="text-center">
                 <a href="#" onclick="event.preventDefault(); document.getElementById('resend-form').submit();" class="text-sm text-gray-600 hover:text-gray-900 font-medium transition inline-flex items-center gap-1.5">
                     <!-- Refresh Icon -->
                     <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                         <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.992 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182" />
                     </svg>
                     Resend Code
                 </a>
             </div>
         </form>

         <!-- Hidden Form for Resending Code -->
         <form id="resend-form" method="POST" action="{{ route('recover-password.post') }}" class="hidden">
             @csrf
             <input type="hidden" name="email" value="{{ session('reset_email') }}">
         </form>
     </div>

    <!-- ========================================== -->
    <!-- Back to Login Link                         -->
    <!-- ========================================== -->
    <div class="mt-6">
        <a href="{{ route('login') }}" class="text-sm text-gray-600 hover:text-gray-900 font-medium transition flex items-center gap-1.5">
            <!-- Left Arrow Icon -->
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
            </svg>
            Back to Login
        </a>
    </div>

    <!-- ========================================== -->
    <!-- Footer: Copyright + Links                  -->
    <!-- ========================================== -->
    <footer class="mt-10 text-center">
        <!-- Copyright Notice -->
        <p class="text-xs text-gray-400">
            &copy; {{ date('Y') }} SecureAdmin Systems. All rights reserved.
        </p>

        <!-- Footer Links -->
        <div class="flex items-center justify-center gap-4 mt-2">
            <a href="#" class="text-xs text-gray-500 hover:text-gray-700 font-medium transition">Help Center</a>
            <a href="#" class="text-xs text-gray-500 hover:text-gray-700 font-medium transition">Privacy Policy</a>
        </div>
    </footer>

 <!-- Auto-Focus Script: Move cursor to next    -->
    @vite('resources/js/verifycode.js')
 
</body>
</html>