<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Admin Channel</title>
    @vite(['resources/css/app.css', 'resources/js/UI/Sidebar.js', 'resources/css/login.css', 'resources/js/Auth/Login.js', 'resources/js/UI/ShowPassword.js'])
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body class="bg-white overflow-x-hidden font-family-Plus Jakarta Sans !important ' ">
    <div class="min-h-screen flex">
        <!-- Left Side - Image Section -->
        <div class="relative hidden lg:flex w-1/2 min-h-screen overflow-hidden bg-slate-100 items-center justify-center border-r border-slate-200/60">
            <img src="images/Moskee_Build.jpg" alt="Login Image" class="absolute inset-0 w-full h-full object-cover opacity-85 scale-105" style="animation: zoomPan 25s ease-in-out infinite alternate;">
            <div class="absolute inset-0 bg-gradient-to-tr from-slate-950/70 via-slate-900/40 to-transparent"></div>
            <div class="absolute inset-0 opacity-15 bg-[linear-gradient(to_right,#e2e8f0_1px,transparent_1px),linear-gradient(to_bottom,#e2e8f0_1px,transparent_1px)] bg-[size:24px_24px]"></div>

            <div class="relative z-10 max-w-lg px-12 text-left space-y-6 animate-fade-slide-up">
                <div class="inline-flex items-center gap-3 px-3.5 py-1.5 rounded-full bg-white/10 backdrop-blur-md border border-white/20">
                    <span class="flex h-2 w-2 relative">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-450 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                    </span>
                    <span class="text-xs font-semibold text-white tracking-wider uppercase">Portal Active</span>
                </div>

                <div class="space-y-3">
                    <h2 class="text-4xl font-extrabold text-white tracking-tight leading-none">
                        Khilafat<span class="bg-clip-text text-transparent bg-gradient-to-r from-indigo-300 to-blue-300">Anjuman</span>
                    </h2>
                    <p class="text-slate-100 text-lg leading-relaxed font-light">
                        Managing operations, communication channels, and community administration with precision and elegance.
                    </p>
                </div>
            </div>
        </div>

        <!-- Right Side - Login Form Section -->
        <div class="relative w-full lg:w-1/2 flex items-center justify-center p-6 sm:p-12 overflow-hidden bg-slate-50 min-h-screen">

            <!-- Floating Animated Blobs -->
            <div class="absolute inset-0 pointer-events-none overflow-hidden">
                <div class="absolute -top-40 -left-40 w-96 h-96 rounded-full bg-indigo-400/15 blur-[120px]" style="animation: floatBlob1 25s infinite alternate;"></div>
                <div class="absolute -bottom-40 -right-40 w-96 h-96 rounded-full bg-blue-300/15 blur-[120px]" style="animation: floatBlob2 20s infinite alternate 2s;"></div>
                <div class="absolute top-1/2 left-1/3 w-80 h-80 rounded-full bg-violet-300/10 blur-[120px]" style="animation: floatBlob3 30s infinite alternate 4s;"></div>
            </div>

            <!-- Glassmorphic Login Card -->
            <div class="relative w-full max-w-md backdrop-blur-xl bg-white/75 border border-white shadow-[0_20px_50px_rgba(0,0,0,0.06)] rounded-3xl p-8 sm:p-10 transition-all duration-300 hover:border-white/95 animate-fade-slide-up">

                <div class="space-y-8">
                    <!-- Title -->
                    <div>
                        <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">Welcome back</h1>
                        <p class="text-slate-500 mt-2 font-medium">Log in to your  account</p>
                    </div>

                    <!-- Error Message -->
                    @if(session('error'))
                        <div class="bg-red-50 border border-red-200/60 text-red-750 px-4 py-3.5 rounded-xl text-sm flex items-center gap-3 animate-[bounce_0.5s_ease-in-out]">
                            <i class="fa-solid fa-circle-exclamation text-red-500 text-base flex-shrink-0"></i>
                            <span class="font-medium">{{ session('error') }}</span>
                        </div>
                    @endif

                    <!-- Login Form -->
                    <form id="loginForm" method="POST" action="{{ route('login.post') }}" class="space-y-6">
                        @csrf

                        <!-- Email Input -->
                        <div class="space-y-2">
                            <label for="email" class="block text-sm font-semibold text-slate-700">
                                Email address
                            </label>
                            <div class="relative group">
                                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 transition-colors duration-200 group-focus-within:text-indigo-650">
                                    <i class="fa-solid fa-envelope text-base"></i>
                                </span>
                                <input
                                    type="email"
                                    id="email"
                                    name="email"
                                    value="{{ old('email') }}"
                                    placeholder="name@example.com"
                                    required
                                    class="w-full pl-11 pr-4 py-3 bg-white/80 border border-slate-200 rounded-xl text-slate-900 placeholder-slate-400 focus:outline-none focus:border-indigo-600 focus:ring-1 focus:ring-indigo-600/10 transition-all duration-200 @error('email') border-red-400/50 @enderror"
                                >
                            </div>
                            @error('email')
                                <p class="text-red-600 text-xs mt-1.5 flex items-center gap-1.5 font-medium">
                                    <i class="fa-solid fa-triangle-exclamation text-xs"></i>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <!-- Password Input -->
                        <div class="space-y-2">
                            <label for="password" class="block text-sm font-semibold text-slate-700">
                                Password
                            </label>
                            <div class="relative group">
                                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 transition-colors duration-200 group-focus-within:text-indigo-650">
                                    <i class="fa-solid fa-lock text-base"></i>
                                </span>
                                <input
                                    type="password"
                                    id="password"
                                    name="password"
                                    placeholder="••••••••"
                                    required
                                    class="w-full pl-11 pr-12 py-3 bg-white/80 border border-slate-200 rounded-xl text-slate-900 placeholder-slate-400 focus:outline-none focus:border-indigo-600 focus:ring-1 focus:ring-indigo-600/10 transition-all duration-200 @error('password') border-red-400/50 @enderror">
                                <button
                                    type="button"
                                    id="togglePassword"
                                    class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 hover:text-indigo-600 focus:outline-none transition-colors"
                                    title="Toggle Password Visibility">
                                    <!--Open eyes-->
                                    <i class="fa-solid fa-eye text-base"></i>
                                    <!--Close eyes-->
                                    <i class="fa-solid fa-eye-slash text-base" style="display: none;"></i>
                                </button>
                            </div>
                            @error('password')
                                <p class="text-red-600 text-xs mt-1.5 flex items-center gap-1.5 font-medium">
                                    <i class="fa-solid fa-triangle-exclamation text-xs"></i>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <!-- Forgot Password Link -->
                        <div class="flex justify-end pt-1">
                            <a href="{{ route('recover-password') }}" class="text-sm text-gray-700 hover:text-black font-semibold transition-colors duration-150">
                                Forgot password?
                            </a>
                        </div>

                        <!-- Login Button -->
                        <button
                            type="submit"
                            id="submitBtn"
                            class="w-full bg-gradient-to-r from-gray-900 to-black hover:from-gray-800 hover:to-gray-900 text-white font-semibold py-3 px-4 rounded-xl shadow-lg shadow-black/20 hover:shadow-black/40 active:scale-[0.98] transition-all duration-200 flex items-center justify-center gap-2">
                            <span id="btnText">Login</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>