<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Lid profiel pagina - Bekijk lid gegevens">
    <title>Lidpagina | Administratie Panel</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/css/sidebar.css', 'resources/js/Sidebar.js'])
</head>
<body>
    <div class="flex justify-center min-h-screen">
        @include('layouts.Sidebars.sidebar')

        <div class="flex-1 ml-0 md:ml-64 transition-all duration-300 min-w-0 overflow-x-hidden">
            @include('layouts.header')

            <section class="px-8 py-6">

                {{-- Succes / Fout melding --}}
                @if(session('success'))
                    <div id="successToast"
                         class="mb-6 bg-emerald-50 border border-emerald-200 text-emerald-800 px-5 py-4 rounded-2xl flex items-center gap-3 text-sm font-medium shadow-sm"
                         style="animation: slideDown 0.4s ease-out;">
                        <div class="w-8 h-8 rounded-full bg-emerald-100 flex items-center justify-center flex-shrink-0">
                            <i class="fa-solid fa-circle-check text-emerald-600"></i>
                        </div>
                        <div class="flex-1">
                            <p class="font-bold text-emerald-900 text-sm">Gelukt!</p>
                            <p class="text-emerald-700 text-xs mt-0.5">{{ session('success') }}</p>
                        </div>
                        <button onclick="document.getElementById('successToast').remove()" class="text-emerald-400 hover:text-emerald-600 transition-colors">
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                    </div>
                @endif

                @if(session('error'))
                    <div id="errorToast"
                         class="mb-6 bg-rose-50 border border-rose-200 text-rose-800 px-5 py-4 rounded-2xl flex items-center gap-3 text-sm font-medium shadow-sm"
                         style="animation: slideDown 0.4s ease-out;">
                        <div class="w-8 h-8 rounded-full bg-rose-100 flex items-center justify-center flex-shrink-0">
                            <i class="fa-solid fa-triangle-exclamation text-rose-600"></i>
                        </div>
                        <div class="flex-1">
                            <p class="font-bold text-rose-900 text-sm">Fout</p>
                            <p class="text-rose-700 text-xs mt-0.5">{{ session('error') }}</p>
                        </div>
                        <button onclick="document.getElementById('errorToast').remove()" class="text-rose-400 hover:text-rose-600 transition-colors">
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                    </div>
                @endif

                <style>
                    @keyframes slideDown {
                        from { opacity: 0; transform: translateY(-12px); }
                        to   { opacity: 1; transform: translateY(0); }
                    }
                </style>

                <!-- Profile Header Section: Elegant top banner containing user avatar, user name, profile actions, and active status indicators -->
                <div class="flex flex-col md:flex-row items-center md:rounded-2xl md:border md:border-slate-800 md:px-10 md:py-5 bg-white justify-between pb-6 mb-8 border-b border-slate-200/60">
                    <div class="flex items-center gap-4">
                        <div class="w-14 h-14 rounded-2xl overflow-hidden border border-slate-200 bg-slate-100 flex items-center justify-center">
                            <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->naam) }}&size=56&background=e2e8f0&color=475569&bold=true&font-size=0.4" alt="Profiel foto" class="w-full h-full object-cover" id="profilePhoto">
                        </div>
                        <div>
                            <div class="flex items-center gap-2  mb-0.5">
                                <h1 class="text-xl font-bold text-slate-900 m-0">{{ Auth::user()->naam }}</h1>
                                <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-700 text-[10px] font-bold">
                                    <span class="relative flex h-1.5 w-1.5">
                                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                                        <span class="relative inline-flex rounded-full h-1.5 w-1.5 bg-emerald-500"></span>
                                    </span>
                                    Actief
                                </span>
                            </div>
                            <p class="text-xs text-slate-500 font-semibold">Lid ID: {{ $lid->lid_id }} • {{ $lid->lid_type }}</p>
                        </div>
                    </div>
                    
                    <!-- Password reset link button -->
                    <a href="{{ route('recover-password') }}" class="mt-4 md:mt-0">
                        <button class="inline-flex items-center gap-2 px-4 py-2 bg-white hover:bg-slate-50 text-slate-700 text-sm font-semibold rounded-xl border border-slate-200 transition-all duration-200 shadow-sm cursor-pointer hover:-translate-y-0.5 active:translate-y-0">
                            <i class="fa-solid fa-lock text-xs text-slate-400"></i>
                            Wachtwoord resetten
                        </button>
                    </a>
                </div>

                <!-- Three Column Dashboard Cards: Informatie, Lidmaatschapskosten, and Contactgegevens -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">

                    <!-- Card 1: Informatie -->
                    <div class="bg-white rounded-3xl border border-slate-100 shadow-[0_4px_20px_rgba(0,0,0,0.02)] p-7 flex flex-col justify-between hover:shadow-[0_8px_30px_rgba(0,0,0,0.04)] transition-all duration-300">
                        <div>
                            <div class="flex items-center gap-3 mb-6">
                                <div class="w-10 h-10 rounded-full bg-slate-100 flex items-center justify-center text-slate-500">
                                    <i class="fa-solid fa-circle-info text-lg"></i>
                                </div>
                                <h2 class="text-lg font-bold text-slate-900 m-0">Informatie</h2>
                            </div>

                            <div class="space-y-1">
                                <div class="flex justify-between items-center py-3.5 border-b border-slate-100">
                                    <span class="text-[11px] font-bold tracking-wider text-slate-400 uppercase">Email</span>
                                    <span class="text-sm font-bold text-slate-800">{{ Auth::user()->email }}</span>
                                </div>
                                <div class="flex justify-between items-center py-3.5 border-b border-slate-100">
                                    <span class="text-[11px] font-bold tracking-wider text-slate-400 uppercase">Leeftijd</span>
                                    <span class="text-sm font-bold text-slate-800">@if($lid->geboortedatum) {{ \Carbon\Carbon::parse($lid->geboortedatum)->age }} jaar @else Onbekend @endif</span>
                                </div>
                                <div class="flex justify-between items-center py-3.5 border-b border-slate-100">
                                    <span class="text-[11px] font-bold tracking-wider text-slate-400 uppercase">Geboortedatum</span>
                                    <span class="text-sm font-bold text-slate-800">{{ $lid->geboortedatum ? \Carbon\Carbon::parse($lid->geboortedatum)->format('d-m-Y') : 'Onbekend' }}</span>
                                </div>
                                <div class="flex justify-between items-center py-3.5">
                                    <span class="text-[11px] font-bold tracking-wider text-slate-400 uppercase">Lid sinds</span>
                                    <span class="text-sm font-bold text-slate-800">{{ $lid->lid_sinds ? \Carbon\Carbon::parse($lid->lid_sinds)->format('d-m-Y') : 'Onbekend' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Card 2: Lidmaatschapskosten -->
                    <div class="bg-slate-900 text-white rounded-3xl p-7 flex flex-col justify-between relative overflow-hidden shadow-[0_8px_30px_rgba(0,0,0,0.15)] min-h-[360px] border border-slate-800">
                        <!-- Subtle Credit Card Watermark in background -->
                        <div class="absolute -right-8 -top-8 opacity-10 pointer-events-none transform translate-x-4 -translate-y-4">
                            <i class="fa-solid fa-credit-card text-white text-9xl"></i>
                        </div>

                        <div>
                            <div class="flex items-center gap-3 mb-6 relative z-10">
                                <div class="w-10 h-10 rounded-full bg-white/10 flex items-center justify-center text-white">
                                    <i class="fa-solid fa-wallet text-base"></i>
                                </div>
                                <h2 class="text-lg font-bold text-white m-0">Lidmaatschapskosten</h2>
                            </div>

                            <div class="text-center py-6 relative z-10">
                                <p class="text-[10px] font-bold tracking-widest text-slate-400 uppercase mb-3">Openstaand Saldo</p>
                                <p class="text-4xl md:text-5xl font-extrabold tracking-tight text-white mb-6 font-mono leading-none">
                                    SRD {{ number_format($openstaandeBalans, 2, ',', '.') }}
                                </p>
                                
                                <div class="inline-flex items-center gap-1.5 px-4 py-1.5 rounded-full bg-rose-500/10 border border-rose-500/20 text-rose-300 text-[11px] font-bold uppercase tracking-wider">
                                    <i class="fa-regular fa-clock text-xs"></i>
                                    <span>Deadline: {{ $deadline ? $deadline->translatedFormat('d M Y') : 'Geen' }}</span>
                                </div>
                            </div>
                        </div>
  
                        <!--Upload Button-->
                      <form action="{{route('UploadBewijs')}}" method="POST" enctype="multipart/form-data" id="bewijsForm">
                          @csrf
                          <input type="file" name="betaling_bewijs" id="bewijsInput" accept="application/pdf" class="hidden"
                          onchange="document.getElementById('bewijsForm').submit()">
                          <button type="button" onclick="document.getElementById('bewijsInput').click()" class="inline-flex items-center gap-2 px-6 py-3 rounded-full bg-white text-slate-900 hover:bg-slate-100 font-bold text-sm shadow-lg hover:shadow-xl transition-all duration-200 w-full transform hover:scale-[1.02]">
                              <i class="fa-solid fa-upload"></i>
                              <span>Upload Betaalbewijs</span>
                          </button>

                          @error('betaling_bewijs')
                              <p class="text-red-400 text-xs mt-2">{{ $message }}</p>
                          @enderror
                          
                      </form>
                    </div>

                    <!-- Card 3: Contactgegevens -->
                    <div class="bg-white rounded-3xl border border-slate-100 shadow-[0_4px_20px_rgba(0,0,0,0.02)] p-7 flex flex-col justify-between hover:shadow-[0_8px_30px_rgba(0,0,0,0.04)] transition-all duration-300">
                        <div>
                            <div class="flex items-center gap-3 mb-6">
                                <div class="w-10 h-10 rounded-full bg-slate-100 flex items-center justify-center text-slate-500">
                                    <i class="fa-solid fa-address-book text-lg"></i>
                                </div>
                                <h2 class="text-lg font-bold text-slate-900 m-0">Contactgegevens</h2>
                            </div>

                            <div class="space-y-4">
                                <!-- Email address block -->
                                <div class="flex items-start gap-4">
                                    <div class="w-10 h-10 rounded-xl bg-slate-50 text-slate-400 flex items-center justify-center flex-shrink-0">
                                        <i class="fa-regular fa-envelope text-base"></i>
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <p class="text-[10px] font-bold tracking-wider uppercase text-slate-400 mb-0.5">Email adres</p>
                                        <p class="text-sm font-semibold text-slate-800 truncate select-all">{{ Auth::user()->email }}</p>
                                    </div>
                                </div>

                                <!-- Phone number block -->
                                <div class="flex items-start gap-4">
                                    <div class="w-10 h-10 rounded-xl bg-slate-50 text-slate-400 flex items-center justify-center flex-shrink-0">
                                        <i class="fa-solid fa-phone text-sm"></i>
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <p class="text-[10px] font-bold tracking-wider uppercase text-slate-400 mb-0.5">Telefoonnummer</p>
                                        <p class="text-sm font-semibold text-slate-800">{{ $lid->telefoonnummer ?? 'Onbekend' }}</p>
                                    </div>
                                </div>

                                <!-- Home Address block -->
                                <div class="flex items-start gap-4">
                                    <div class="w-10 h-10 rounded-xl bg-slate-50 text-slate-400 flex items-center justify-center flex-shrink-0">
                                        <i class="fa-solid fa-location-dot text-sm"></i>
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <p class="text-[10px] font-bold tracking-wider uppercase text-slate-400 mb-0.5">Adres</p>
                                        <p class="text-sm font-semibold text-slate-800 whitespace-normal leading-relaxed">
                                            {{ $lid->adres ?? 'Onbekend' }}@if($lid->woonplaats), {{ $lid->woonplaats }}@endif
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

               <!-- Geschiedenis Betaling tabel -->
<div class="bg-white rounded-2xl border border-slate-200/60 shadow-sm">
    <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
        <h2 class="text-base font-bold text-slate-900 m-0">Geschiedenis Betaling</h2>

        <form method="GET" action="{{ route('GegevensPagina') }}" class="flex items-center gap-2">
            <!-- Maand dropdown -->
            <select name="maand" onchange="this.form.submit()"
                class="text-sm px-3 py-1.5 rounded-xl border border-slate-200 bg-white text-slate-700 cursor-pointer focus:outline-none">
                <option value="">Alle maanden</option>
                @foreach(['01'=>'Januari','02'=>'Februari','03'=>'Maart','04'=>'April','05'=>'Mei','06'=>'Juni','07'=>'Juli','08'=>'Augustus','09'=>'September','10'=>'Oktober','11'=>'November','12'=>'December'] as $num => $naam)
                    <option value="{{ $num }}" {{ request('maand') == $num ? 'selected' : '' }}>
                        {{ $naam }}
                    </option>
                @endforeach
            </select>

            <!-- Jaar dropdown -->
            <select name="jaar" onchange="this.form.submit()"
                class="text-sm px-3 py-1.5 rounded-xl border border-slate-200 bg-white text-slate-700 cursor-pointer focus:outline-none">
                <option value="">Alle jaren</option>
                @foreach($beschikbareJaren as $jaar)
                    <option value="{{ $jaar }}" {{ request('jaar') == $jaar ? 'selected' : '' }}>
                        {{ $jaar }}
                    </option>
                @endforeach
            </select>

            <!-- Wis filter knop -->
            @if(request('maand') || request('jaar'))
                <a href="{{ route('GegevensPagina') }}"
                   class="w-8 h-8 flex items-center justify-center rounded-lg border border-slate-200 hover:bg-slate-50 transition-colors cursor-pointer" title="Wis filter">
                    <i class="fa-solid fa-xmark text-slate-400 text-sm"></i>
                </a>
            @else
                <div class="w-8 h-8 flex items-center justify-center rounded-lg border border-slate-200">
                    <i class="fa-solid fa-filter text-slate-400 text-sm"></i>
                </div>
            @endif
        </form>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm" id="paymentHistoryTable">
            <thead>
                <tr class="border-b border-slate-100">
                    <th class="text-left text-[11px] font-semibold tracking-widest uppercase text-slate-400 px-6 py-3">Date</th>
                    <th class="text-right text-[11px] font-semibold tracking-widest uppercase text-slate-400 px-6 py-3">Amount</th>
                    <th class="text-left text-[11px] font-semibold tracking-widest uppercase text-slate-400 px-6 py-3">Status</th>
                    <th class="text-left text-[11px] font-semibold tracking-widest uppercase text-slate-400 px-6 py-3">Bon nummer</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                @forelse($betalingen as $betaling)
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="px-6 py-4 text-slate-600">{{ \Carbon\Carbon::parse($betaling->ingediend_op)->translatedFormat('d M Y') }}</td>
                        <td class="px-6 py-4 text-slate-800 font-semibold text-right">SRD {{ number_format($betaling->bedrag, 2, ',', '.') }}</td>
                        <td class="px-6 py-4">
                            @if($betaling->status === 'betaald')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-50 text-emerald-600">Betaald</span>
                            @elseif($betaling->status === 'niet_betaald')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-red-50 text-red-600">Niet betaald</span>
                            @elseif($betaling->status === 'Openstaand')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-cyan-50 text-cyan-600">Openstaand</span>
                            @elseif($betaling->status === 'in_behandeling')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-50 text-amber-600">In behandeling</span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-slate-100 text-slate-500">Afgewezen</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-slate-500">{{ $betaling->bon ? $betaling->bon->bon_nummer : '—' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-6 py-10 text-center text-slate-400">
                            Geen betalingsgeschiedenis gevonden.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($betalingen->hasPages())
    <div class="flex items-center justify-between px-6 py-4 border-t border-slate-100">
        <p class="text-sm text-slate-400">
            Weergeven van {{ $betalingen->firstItem() }} tot {{ $betalingen->lastItem() }} van {{ $betalingen->total() }} betalingen
        </p>
        <div class="flex items-center gap-1.5">
            {{ $betalingen->links('pagination::tailwind') }}
        </div>
    </div>
    @endif
</div>

            </section>
        </div>
    </div>
    @vite('resources/js/BewijsMessage.js')
</body>
</html>