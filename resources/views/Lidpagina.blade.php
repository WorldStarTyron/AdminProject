<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Lid profiel pagina">
    <title>Mijn Gegevens | Administratie Panel</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/css/sidebar.css', 'resources/js/UI/Sidebar.js'])
</head>
<body class="bg-slate-50 font-['Inter']">
    <div class="flex min-h-screen">
        @include('Layouts.Sidebars.sidebar')

        <div class="flex-1 ml-0 md:ml-64 transition-all duration-300 min-w-0 overflow-x-hidden">
            @include('Layouts.Headers.LidHeader')

            <section class="px-6 lg:px-8 py-6 space-y-6">

                {{-- Succes / Fout meldingen --}}
                @if(session('success'))
                    <div class="mb-2 bg-emerald-50 border border-emerald-200 text-emerald-800 px-5 py-4 rounded-2xl flex items-center gap-3 text-sm font-medium">
                        <i class="fa-solid fa-circle-check text-emerald-600"></i>
                        <span>{{ session('success') }}</span>
                    </div>
                @endif
                @if(session('error'))
                    <div class="mb-2 bg-rose-50 border border-rose-200 text-rose-800 px-5 py-4 rounded-2xl flex items-center gap-3 text-sm font-medium">
                        <i class="fa-solid fa-triangle-exclamation text-rose-600"></i>
                        <span>{{ session('error') }}</span>
                    </div>
                @endif

                <!-- Profiel kaart bovenaan -->
                <div class="bg-white rounded-2xl border border-slate-200/60 shadow-sm px-6 py-5 flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
                    <div class="flex items-center gap-4">
                        <!-- Initialen avatar -->
                        @php
                            $initials = collect(explode(' ', Auth::user()->naam))->take(2)->map(fn($n) => strtoupper(substr($n, 0, 1)))->implode('');
                        @endphp
                        <div class="w-14 h-14 rounded-2xl bg-slate-100 text-slate-700 flex items-center justify-center font-extrabold text-lg border border-slate-200">
                            {{ $initials }}
                        </div>

                        <div>
                            <div class="flex items-center gap-2 flex-wrap">
                                <h1 class="text-xl font-bold text-slate-900 m-0">{{ Auth::user()->naam }}</h1>
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-700 text-[10px] font-bold uppercase">
                                    Actief
                                </span>
                            </div>
                            <p class="text-xs text-slate-500 mt-0.5">
                                Lid ID: {{ $lid->lid_id }} • Lid sinds {{ $lid->lid_sinds ? \Carbon\Carbon::parse($lid->lid_sinds)->format('d-m-Y') : 'Onbekend' }}
                            </p>
                            <p class="text-xs text-slate-500">
                                Status: {{ $lid->lid_type }} Lidmaatschap
                            </p>
                        </div>
                    </div>

                    <!-- Wachtwoord resetten knop -->
                    <a href="{{ route('recover-password') }}"
                       class="inline-flex items-center gap-2 px-4 py-2 bg-white hover:bg-slate-50 text-slate-700 text-sm font-semibold rounded-xl border border-slate-200 transition shadow-sm">
                        <i class="fa-solid fa-rotate-left text-xs text-slate-400"></i>
                        Wachtwoord Resetten
                    </a>
                </div>

                <!-- Hoofdgrid: Openstaande Maanden (2/3) + rechter kolom (1/3) -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                    <!-- Linker kolom: Openstaande Maanden tabel -->
                    <div class="lg:col-span-2">
                        @include('Layouts.Tables.openstaande-maanden-tabel')
                    </div>

                    <!-- Rechter kolom: Contributie kaart + Contactgegevens (gestapeld) -->
                    <div class="space-y-6">

                        <!-- Donkere contributie kaart -->
                        <div class="bg-slate-900 text-white rounded-2xl p-6 shadow-lg relative overflow-hidden">
                            <p class="text-[10px] font-bold tracking-widest text-slate-400 uppercase mb-1">Contributie Betalingen</p>
                            <p class="text-[10px] font-bold tracking-widest text-slate-400 uppercase mb-3">Openstaand Saldo</p>

                            <p class="text-3xl font-extrabold tracking-tight mb-5">
                                SRD <span id="openstaandSaldo">{{ number_format($openstaandeBalans, 2, ',', '.') }}</span>
                            </p>

                            <!-- Selectie info -->
                            <div class="flex items-center justify-between text-xs text-slate-300 mb-2">
                                <span>Geselecteerd voor betaling:</span>
                                <span class="font-bold text-white" id="selectieCount">0 maanden</span>
                            </div>

                            <p class="text-[10px] font-bold tracking-widest text-slate-400 uppercase mb-1 mt-4">Subtotaal te betalen</p>
                            <p class="text-2xl font-extrabold tracking-tight mb-5">
                                SRD <span id="subtotaal">0,00</span>
                            </p>

                            <!-- Upload knop -->
                            <form action="{{ route('UploadBewijs') }}" method="POST" enctype="multipart/form-data" id="bewijsForm">
                                @csrf
                                {{-- Gekozen maanden worden hier als hidden inputs ingevuld door submitBewijs() --}}
                                <input type="file" name="betaling_bewijs" id="bewijsInput" accept="application/pdf,image/jpeg,image/png,image/webp" class="hidden"
                                       onchange="submitBewijs()">
                                <button type="button" onclick="kiesBestand()"
                                        class="w-full inline-flex items-center justify-center gap-2 px-4 py-3 bg-white text-slate-900 hover:bg-slate-100 font-bold text-sm rounded-xl transition">
                                    <i class="fa-solid fa-upload"></i>
                                    Upload Betaalbewijs
                                </button>
                            </form>

                            @error('betaling_bewijs')
                                <p class="text-[11px] text-rose-300 text-center mt-3 font-medium">{{ $message }}</p>
                            @enderror

                            <p class="text-[10px] text-slate-400 text-center mt-3 leading-relaxed">
                                U kunt een PDF of een foto (JPG, PNG, WEBP) van uw betaalbewijs uploaden, max. 5 MB.
                            </p>
                        </div>

                        <!-- Contactgegevens kaart -->
                        <div class="bg-white rounded-2xl border border-slate-200/60 shadow-sm p-6">
                            <div class="flex items-center gap-2 mb-4">
                                <i class="fa-solid fa-address-book text-slate-400"></i>
                                <h3 class="text-sm font-bold text-slate-900 m-0">Contactgegevens</h3>
                            </div>

                            <div class="space-y-4">
                                <div class="flex items-start gap-3">
                                    <i class="fa-regular fa-envelope text-slate-400 text-sm mt-0.5"></i>
                                    <div class="min-w-0 flex-1">
                                        <p class="text-[10px] font-bold tracking-wider uppercase text-slate-400">Email adres</p>
                                        <p class="text-xs font-semibold text-slate-800 truncate">{{ Auth::user()->email }}</p>
                                    </div>
                                </div>
                                <div class="flex items-start gap-3">
                                    <i class="fa-solid fa-phone text-slate-400 text-sm mt-0.5"></i>
                                    <div class="min-w-0 flex-1">
                                        <p class="text-[10px] font-bold tracking-wider uppercase text-slate-400">Telefoonnummer</p>
                                        <p class="text-xs font-semibold text-slate-800">{{ $lid->telefoonnummer ?? '—' }}</p>
                                    </div>
                                </div>
                                <div class="flex items-start gap-3">
                                    <i class="fa-solid fa-location-dot text-slate-400 text-sm mt-0.5"></i>
                                    <div class="min-w-0 flex-1">
                                        <p class="text-[10px] font-bold tracking-wider uppercase text-slate-400">Adres</p>
                                        <p class="text-xs font-semibold text-slate-800">
                                            {{ $lid->adres ?? '—' }}@if($lid->woonplaats), {{ $lid->woonplaats }}@endif
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                <!-- Geschiedenis Betaling tabel (volle breedte onderaan) -->
                @include('Layouts.Tables.geschiedenis-betaling-tabel')

            </section>
        </div>
    </div> 

   
    {{-- Selectie + upload logica inline (gewone functies, zodat de onclick/onchange in de HTML ze kan vinden) --}}
    <script>
        function updateSelectie() {
            const checkboxes = document.querySelectorAll('.openstaand-checkbox:checked');
            let totaal = 0;
            checkboxes.forEach(c => totaal += parseFloat(c.dataset.bedrag || 0));

            document.getElementById('selectieCount').textContent = checkboxes.length + ' maand' + (checkboxes.length === 1 ? '' : 'en');
            document.getElementById('subtotaal').textContent = totaal.toLocaleString('nl-NL', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        }

        // Open de bestandskiezer, maar vraag eerst om een maandselectie als er openstaande maanden zijn
        function kiesBestand() {
            const heeftOpenstaand = document.querySelectorAll('.openstaand-checkbox').length > 0;
            const selectie = document.querySelectorAll('.openstaand-checkbox:checked').length;
            if (heeftOpenstaand && selectie === 0) {
                alert('Selecteer eerst de maand(en) die u wilt betalen.');
                return;
            }
            document.getElementById('bewijsInput').click();
        }

        // Zet de gekozen maanden als hidden inputs in het formulier en verstuur
        function submitBewijs() {
            const form = document.getElementById('bewijsForm');
            form.querySelectorAll('input[name="betaling_ids[]"]').forEach(e => e.remove());
            document.querySelectorAll('.openstaand-checkbox:checked').forEach(c => {
                const h = document.createElement('input');
                h.type = 'hidden';
                h.name = 'betaling_ids[]';
                h.value = c.dataset.id;
                form.appendChild(h);
            });
            form.submit();
        }
    </script>

    @vite('resources/js/Pages/BewijsMessage.js')
</body>
</html>
