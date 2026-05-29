<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Admin Channel</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-white">
    <div class="min-h-screen flex">
        <!-- Left Side - Image Section -->
        <div class="hidden object-cover h-[35rem] lg:flex w-1/2 bg-gray-50 items-center justify-center border-r border-gray-200">
            <div class="text-center">
                <!-- Placeholder for image -->
               <img src="images/Moskee_Build.jpg" alt="Login Image">
            </div>
        </div>

        <!-- Right Side - Login Form Section -->
        <div class="w-full lg:w-1/2 flex items-center justify-center p-6 sm:p-8">
            <div class="w-full max-w-md">
                <div class="space-y-8">
                    <!-- Title -->
                    <div>
                        <h1 class="text-3xl font-semibold text-gray-900">Welcome back</h1>
                        <p class="text-gray-600 mt-2">Log in to your account</p>
                    </div>

                    <!-- Display generic error messages (wrong credentials / inactive account) -->
                    @if(session('error'))
                        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm">
                            {{ session('error') }}
                        </div>
                    @endif

                    <!-- Login Form -->
                    <form method="POST" action="{{ route('login.post') }}" class="space-y-5">
                        @csrf

                        <!-- Email Input -->
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-900 mb-1.5">
                                Email address
                            </label>
                            <input
                                type="email"
                                id="email"
                                name="email"
                                value="{{ old('email') }}"
                                placeholder="you@example.com"
                                required
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-gray-900 placeholder-gray-400 focus:outline-none focus:border-gray-400 focus:ring-1 focus:ring-gray-300 transition @error('email') border-gray-400 @enderror"
                            >
                            @error('email')
                                <p class="text-gray-600 text-sm mt-1.5">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Password Input -->
                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-900 mb-1.5">
                                Password
                            </label>
                            <input
                                type="password"
                                id="password"
                                name="password"
                                placeholder="••••••••"
                                required
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-gray-900 placeholder-gray-400 focus:outline-none focus:border-gray-400 focus:ring-1 focus:ring-gray-300 transition @error('password') border-gray-400 @enderror"
                            >
                            @error('password')
                                <p class="text-gray-600 text-sm mt-1.5">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Forgot Password Link -->
                        <div class="flex justify-end mr-5">
                           <a href="{{ route('recover-password') }}" class="text-sm text-gray-700 hover:text-blue-900 font-medium transition">
                                Forgot password?
                            </a>
                        </div>

                        <!-- Login Button -->
                        <button
                            type="submit"
                            class="w-full bg-gray-900 hover:bg-gray-800 text-white font-medium py-2.5 px-4 rounded-lg transition duration-200">
                           Login
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>