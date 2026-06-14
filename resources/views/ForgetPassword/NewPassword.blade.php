<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Password - Security Portal</title>
    <meta name="description" content="Create a new secure password for your account.">
    @vite(['resources/css/app.css', 'resources/js/UI/Sidebar.js'])
</head>

<!-- Page Body: Light gray background matching the design -->
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

        <!-- Subtitle -->
        <p class="text-sm text-gray-500 mt-1">Set New Password</p>
    </div>

    <!-- Main Card: New Password Form -->
    <div class="w-full max-w-sm bg-white rounded-xl shadow-sm border border-gray-200 p-6">

        <!-- Card Heading -->
        <h2 class="text-lg font-semibold text-gray-900 mb-1">Create New Password</h2>

        <!-- Card Description -->
        <p class="text-sm text-gray-500 mb-6 leading-relaxed">
            Enter a strong new password for your account. Make sure it's at least 8 characters long.
        </p>

        <!-- New Password Form -->
        <form method="POST" action=" {{route('new-password.post')}}" class="space-y-5">
            @csrf

            <!-- New Password Field -->
            <div>
                <!-- Password Label -->
                <label for="password" class="block text-xs font-semibold text-gray-700 tracking-wide mb-2">
                    NEW PASSWORD
                </label>

                <!-- Password Input with Icon -->
                <div class="relative">
                    <input
                        type="password"
                        id="password"
                        name="password"
                        placeholder="Enter new password"
                        required
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-gray-900 text-sm placeholder-gray-400 focus:outline-none focus:border-gray-400 focus:ring-1 focus:ring-gray-300 transition pr-10 @error('password') border-red-400 @enderror"
                    >

                    <!-- Lock Icon (right side of input) -->
                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" />
                        </svg>
                    </div>
                </div>

                <!-- Validation Error Message -->
                @error('password')
                    <p class="text-red-500 text-xs mt-1.5">{{ $message }}</p>
                @enderror
            </div>

            <!-- Confirm Password Field -->
            <div>
                <!-- Confirm Password Label -->
                <label for="password_confirmation" class="block text-xs font-semibold text-gray-700 tracking-wide mb-2">
                    CONFIRM PASSWORD
                </label>

                <!-- Confirm Password Input with Icon -->
                <div class="relative">
                    <input
                        type="password"
                        id="password_confirmation"
                        name="password_confirmation"
                        placeholder="Confirm new password"
                        required
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-gray-900 text-sm placeholder-gray-400 focus:outline-none focus:border-gray-400 focus:ring-1 focus:ring-gray-300 transition pr-10"
                    >

                    <!-- Shield Check Icon (right side of input) -->
                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z" />
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Display Session Error (e.g. password -->
            <!-- mismatch, validation failure, etc.)  -->
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

            <!-- Submit Button: Reset Password -->
            <button
                type="submit"
                class="w-full bg-gray-900 hover:bg-gray-800 text-white font-medium py-2.5 px-4 rounded-lg transition duration-200 text-sm flex items-center justify-center gap-2"
            >
                Reset Password
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
    </footer>

</body>
</html>
