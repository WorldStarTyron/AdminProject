<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Lid Bewerken | Administratie Panel</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/css/sidebar.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 font-[Inter]">
    <div class="flex min-h-screen">
        @include('layouts.Sidebars.sidebar')

        <div class="flex-1 ml-0 md:ml-64 transition-all duration-300 min-w-0 overflow-x-hidden">
            @include('layouts.header')

            <div class="px-8 py-6">
                {{-- Breadcrumb --}}
                <nav class="text-sm text-gray-400 mb-6 flex items-center gap-1.5">
                    <a href="{{ route('dashboard') }}" class="hover:text-gray-600 transition-colors">Dashboard</a>
                    <span>/</span>
                    <a href="{{ route('ledenpagina') }}" class="hover:text-gray-600 transition-colors">Members</a>
                    <span>/</span>
                    <a href="{{ route('ledenpagina.show', $lid->lid_id) }}" class="hover:text-gray-600 transition-colors">Lid Profiel</a>
                    <span>/</span>
                    <span class="text-gray-700 font-medium">Bewerken</span>
                </nav>

                <div class="flex gap-6 items-start">
                    <div class="flex-1">
                        <div class="bg-white rounded-2xl shadow-sm border border-gray-100">

                            <div class="flex items-center justify-between px-8 py-5 border-b border-gray-100">
                                <div>
                                    <h1 class="text-base font-semibold text-gray-800">Lidmaatschapsgegevens Aanpassen</h1>
                                    <p class="text-sm text-gray-400 mt-0.5">Bewerk de profielinformatie en status van dit lid.</p>
                                </div>
                                <div class="flex items-center gap-3">
                                    <a href="{{ route('ledenpagina.show', $lid->lid_id) }}"
                                       class="px-4 py-2 text-sm font-medium text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">
                                        Annuleren
                                    </a>
                                    <button type="submit" form="edit-form"
                                            class="px-4 py-2 text-sm font-medium text-white bg-slate-800 hover:bg-slate-700 rounded-lg transition-colors flex items-center gap-2">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                            <polyline points="17 21 17 13 7 13 7 21" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                            <polyline points="7 3 7 8 15 8" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                        Opslaan
                                    </button>
                                </div>
                            </div>

                            <form id="edit-form" action="{{ route('ledenpagina.update', $lid->lid_id) }}" method="POST">
                                @csrf
                                @method('PUT')

                                <div class="px-8 py-6 space-y-8">

                                    @if($errors->any())
                                        <div class="bg-red-50 border border-red-200 rounded-xl p-4">
                                            @foreach($errors->all() as $error)
                                                <p class="text-sm text-red-600">• {{ $error }}</p>
                                            @endforeach
                                        </div>
                                    @endif

                                    <div>
                                        <div class="flex items-center gap-2 mb-4">
                                            <div class="w-7 h-7 rounded-full bg-slate-100 flex items-center justify-center">
                                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" stroke="#475569" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                    <circle cx="12" cy="7" r="4" stroke="#475569" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                </svg>
                                            </div>
                                            <h2 class="text-sm font-semibold text-gray-700">Persoonlijke Informatie</h2>
                                        </div>

                                        <div class="mb-4">
                                            <label for="naam" class="block text-xs font-medium text-gray-500 mb-1.5">Volledige Naam</label>
                                            <input type="text" name="naam" id="naam" value="{{ old('naam', $lid->naam) }}" required class="w-full px-3 py-2.5 text-sm text-gray-800 bg-gray-50 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-slate-300 focus:border-slate-400 transition-all placeholder-gray-300">
                                        </div>

                                        <div class="grid grid-cols-2 gap-4">
                                            <div>
                                                <label for="email" class="block text-xs font-medium text-gray-500 mb-1.5">Email Adres</label>
                                                <input type="email" name="email" id="email" value="{{ old('email', $lid->email) }}" required class="w-full px-3 py-2.5 text-sm text-gray-800 bg-gray-50 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-slate-300 focus:border-slate-400 transition-all placeholder-gray-300">
                                            </div>
                                            <div>
                                                <label for="telefoonnummer" class="block text-xs font-medium text-gray-500 mb-1.5">Telefoonnummer</label>
                                                <input type="text" name="telefoonnummer" id="telefoonnummer" value="{{ old('telefoonnummer', $lid->telefoonnummer) }}" required class="w-full px-3 py-2.5 text-sm text-gray-800 bg-gray-50 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-slate-300 focus:border-slate-400 transition-all placeholder-gray-300">
                                            </div>
                                        </div>
                                    </div>

                                    <hr class="border-gray-100">

                                    <div>
                                        <div class="flex items-center gap-2 mb-4">
                                            <div class="w-7 h-7 rounded-full bg-slate-100 flex items-center justify-center">
                                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z" stroke="#475569" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                    <circle cx="12" cy="10" r="3" stroke="#475569" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                </svg>
                                            </div>
                                            <h2 class="text-sm font-semibold text-gray-700">Adresgegevens</h2>
                                        </div>

                                        <div class="grid grid-cols-2 gap-4">
                                            <div>
                                                <label for="adres" class="block text-xs font-medium text-gray-500 mb-1.5">Adres & Huisnummer</label>
                                                <input type="text" name="adres" id="adres" value="{{ old('adres', $lid->adres) }}" required class="w-full px-3 py-2.5 text-sm text-gray-800 bg-gray-50 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-slate-300 focus:border-slate-400 transition-all placeholder-gray-300">
                                            </div>
                                            <div>
                                                <label for="woonplaats" class="block text-xs font-medium text-gray-500 mb-1.5">Woonplaats</label>
                                                <select name="woonplaats" id="woonplaats" class="w-full px-3 py-2.5 text-sm text-gray-800 bg-gray-50 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-slate-300 focus:border-slate-400 transition-all appearance-none cursor-pointer">
                                                    @foreach(['Paramaribo','Wanica','Nickerie','Commewijne','Marowijne','Saramacca','Coronie','Sipaliwini','Brokopondo','Para'] as $district)
                                                        <option value="{{ $district }}" {{ old('woonplaats', $lid->woonplaats) == $district ? 'selected' : '' }}>
                                                            {{ $district }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <hr class="border-gray-100">

                                    <div>
                                        <div class="flex items-center gap-2 mb-4">
                                            <div class="w-7 h-7 rounded-full bg-slate-100 flex items-center justify-center">
                                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <rect x="2" y="3" width="20" height="14" rx="2" stroke="#475569" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                    <line x1="8" y1="21" x2="16" y2="21" stroke="#475569" stroke-width="2" stroke-linecap="round"/>
                                                    <line x1="12" y1="17" x2="12" y2="21" stroke="#475569" stroke-width="2" stroke-linecap="round"/>
                                                </svg>
                                            </div>
                                            <h2 class="text-sm font-semibold text-gray-700">Lidmaatschap Details</h2>
                                        </div>

                                        <div class="grid grid-cols-3 gap-4">
                                            <div>
                                                <label for="geboortedatum" class="block text-xs font-medium text-gray-500 mb-1.5">Geboortedatum</label>
                                                <input type="date" name="geboortedatum" id="geboortedatum"
                                                       value="{{ old('geboortedatum', $lid->geboortedatum) }}"
                                                       required
                                                       class="w-full px-3 py-2.5 text-sm text-gray-800 bg-gray-50 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-slate-300 focus:border-slate-400 transition-all">
                                            </div>
                                            <div>
                                                <label for="lid_type" class="block text-xs font-medium text-gray-500 mb-1.5">Lid Status</label>
                                                <select name="lid_type" id="lid_type"
                                                        required
                                                        class="w-full px-3 py-2.5 text-sm text-gray-800 bg-gray-50 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-slate-300 focus:border-slate-400 transition-all appearance-none cursor-pointer">
                                                    <option value="">Selecteer type</option>
                                                    <option value="Actief"    {{ old('lid_type', $lid->lid_type) == 'Actief'    ? 'selected' : '' }}>Actief</option>
                                                    <option value="Passief"   {{ old('lid_type', $lid->lid_type) == 'Passief'   ? 'selected' : '' }}>Passief</option>
                                                    <option value="Bijzonder" {{ old('lid_type', $lid->lid_type) == 'Bijzonder' ? 'selected' : '' }}>Bijzonder</option>
                                                </select>
                                            </div>
                                            <div>
                                                <label for="lid_sinds" class="block text-xs font-medium text-gray-500 mb-1.5">Lid Sinds</label>
                                                <input type="date" name="lid_sinds" id="lid_since" value="{{ old('lid_sinds', $lid->lid_sinds) }}" required class="w-full px-3 py-2.5 text-sm text-gray-800 bg-gray-50 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-slate-300 focus:border-slate-400 transition-all">
                                            </div>
                                        </div>
                                    </div>

                                    <hr class="border-gray-100">

                                    <div class="grid grid-cols-2 gap-4">
                                        <div class="flex items-start gap-3 bg-amber-50 border border-amber-100 rounded-xl p-4">
                                            <svg class="flex-shrink-0 mt-0.5" width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <circle cx="12" cy="12" r="10" stroke="#d97706" stroke-width="2"/>
                                                <line x1="12" y1="8" x2="12" y2="12" stroke="#d97706" stroke-width="2" stroke-linecap="round"/>
                                                <line x1="12" y1="16" x2="12.01" y2="16" stroke="#d97706" stroke-width="2" stroke-linecap="round"/>
                                            </svg>
                                            <div>
                                                <p class="text-xs font-semibold text-amber-800 mb-1">Informatie Beveiliging</p>
                                                <p class="text-xs text-amber-700 leading-relaxed">
                                                    Wijzigingen in gevoelige velden zoals Geboortedatum worden gelogd in het audit-systeem voor compliance doeleinden.
                                                </p>
                                            </div>
                                        </div>
                                        <div class="flex items-start gap-3 bg-slate-50 border border-slate-100 rounded-xl p-4">
                                            <svg class="flex-shrink-0 mt-0.5" width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <circle cx="12" cy="12" r="10" stroke="#64748b" stroke-width="2"/>
                                                <line x1="12" y1="8" x2="12" y2="12" stroke="#64748b" stroke-width="2" stroke-linecap="round"/>
                                                <line x1="12" y1="16" x2="12.01" y2="16" stroke="#64748b" stroke-width="2" stroke-linecap="round"/>
                                            </svg>
                                            <div>
                                                <p class="text-xs font-semibold text-slate-700 mb-1">Hulp Nodig?</p>
                                                <p class="text-xs text-slate-500 leading-relaxed">
                                                    Neem contact op met de systeembeheerder als u de status van een ereid wilt wijzigen.
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>