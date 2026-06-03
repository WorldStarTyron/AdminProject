<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recover Password - Security Portal</title>
    <meta name="description" content="Reset your password by entering your registered email address to receive a secure verification code.">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<!-- Page Body: Light gray background matching the design -->
<body class="bg-gray-50 min-h-screen flex flex-col items-center justify-center px-4 py-8">

   
    <!-- Header Section: Lock Icon + Portal Title   -->
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

        <!-- Subtitle -->
        <p class="text-sm text-gray-500 mt-1">Forgot Password</p>
    </div>

   
    <!-- Main Card: Recover Account Form -->
    <div class="w-full max-w-sm bg-white rounded-xl shadow-sm border border-gray-200 p-6">

        <!-- Card Heading -->
        <h2 class="text-lg font-semibold text-gray-900 mb-1">Recover Account</h2>

        <!-- Card Description -->
        <p class="text-sm text-gray-500 mb-6 leading-relaxed">
            Enter your registered email address below. We'll send a secure reset code to your inbox.
        </p>

        <!-- Recovery Form  -->
        @if(auth()->check())
            <div class="mb-5 p-3.5 rounded-xl bg-amber-50 border border-amber-200/80 text-amber-800 text-xs flex flex-col gap-2">
                <div class="font-medium">
                    U bent momenteel ingelogd als <strong>{{ auth()->user()->email }}</strong>. Het e-mailveld is daarom vergrendeld.
                </div>
                <div>
                    <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form-recover').submit();" class="font-bold underline text-amber-900 hover:text-amber-950">
                        Klik hier om uit te loggen
                    </a> en een ander e-mailadres te gebruiken.
                </div>
            </div>
        @endif

        <form method="POST" action="{{route('recover-password.post')}}" class="space-y-5">
            @csrf 

            <!-- Email Address Field -->
            <div>
                <!-- Email Label -->
                <label for="email" class="block text-xs font-semibold text-gray-700 tracking-wide mb-2">
                    EMAIL ADDRESS
                </label>

                <!-- Email Input with Icon -->
                <div class="relative">
                    <input
                        type="email"
                        id="email"
                        name="email"
                        value="{{ auth()->check() ? auth()->user()->email : old('email') }}"
                        placeholder="name@company.com"
                        required
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-gray-900 text-sm placeholder-gray-400 focus:outline-none focus:border-gray-400 focus:ring-1 focus:ring-gray-300 transition pr-10 @error('email') border-red-400 @enderror {{ auth()->check() ? 'bg-gray-100 cursor-not-allowed' : '' }}"
                        @if(auth()->check()) readonly @endif
                    >

                    <!-- Mail Icon (right side of input) -->
                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75" />
                        </svg>
                    </div>
                </div>

                <!-- Validation Error Message -->
                @error('email')
                    <p class="text-red-500 text-xs mt-1.5">{{ $message }}</p>
                @enderror
            </div>

            <!-- Display Session Error (e.g. email not  -->
            <!-- found, rate limiting, etc.) -->
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

            <!-- Submit Button: Send Code -->
            <button
                type="submit"
                class="w-full bg-gray-900 hover:bg-gray-800 text-white font-medium py-2.5 px-4 rounded-lg transition duration-200 text-sm flex items-center justify-center gap-2"
            >
                Send Code
                <!-- Arrow Icon -->
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                </svg>
            </button>
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
     @if(auth()->check())
         <form id="logout-form-recover" method="POST" action="{{ route('logout') }}" class="hidden">
             @csrf
         </form>
     @endif
 </body>
 </html>