<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Verwijderde Betalingen</title>
    @vite(['resources/css/app.css', 'resources/css/sidebar.css', 'resources/js/UI/Sidebar.js'])
</head>
<body class="m-0 bg-[#f0f4f8] text-slate-700 font-['Inter',sans-serif] antialiased">
<div class="flex min-h-screen">

    {{-- Sidebar --}}
    @include('Layouts.Sidebars.sidebar')

    {{-- Hoofdinhoud --}}
    <div class="flex-1 ml-0 md:ml-64 pt-20 transition-all duration-300 min-w-0 overflow-x-hidden">

        {{-- Header --}}
        @include('Layouts.Headers.header')

        <div class="p-6">

            {{-- Paginatitel --}}
            <div class="mb-6">
                <h1 class="text-2xl font-bold text-slate-800">Verwijderde Betalingen</h1>
                <p class="text-sm text-slate-500 mt-1">Overzicht van alle soft-deleted betalingen. Je kan ze hier herstellen.</p>
            </div>

            {{-- Tabel kaart --}}
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 overflow-hidden">

                {{-- Tabel header balk --}}
                <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100">
                    <p class="text-sm text-slate-500">
                        {{-- Laat zien hoeveel records er zijn --}}
                        <span class="font-semibold text-slate-700">{{ $verwijderdeBetalingen->count() }}</span> verwijderde betalingen gevonden
                    </p>

                    {{-- Knop terug naar betalingen --}}
                    <a href="{{ route('betalingPagina') }}"
                       class="inline-flex items-center gap-1.5 bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-semibold px-3.5 py-1.5 rounded-lg transition-all duration-150">
                        <i class="fa-solid fa-arrow-left text-[0.625rem]"></i>
                        Terug naar betalingen
                    </a>
                </div>

                {{-- Tabel --}}
                <div class="overflow-x-auto">
                    
                    <table class="w-full text-sm">

                        {{-- Kolomhoofden --}}
                        <thead>
                            <tr class="bg-slate-50 text-slate-500 text-xs uppercase tracking-wide">
                                <th class="px-5 py-3 text-left font-semibold">Naam</th>
                                <th class="px-5 py-3 text-left font-semibold">Bedrag</th>
                                <th class="px-5 py-3 text-left font-semibold">Methode</th>
                                <th class="px-5 py-3 text-left font-semibold">Status</th> 
                                <th class="px-5 py-3 text-left font-semibold">Maand / Jaar</th>
                                <th class="px-5 py-3 text-left font-semibold">Verwijderd op</th>
                                <th class="px-5 py-3 text-left font-semibold">Acties</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-slate-100">

                            {{-- Als er geen verwijderde betalingen zijn --}}
                            @forelse($verwijderdeBetalingen as $betaling)
                            <tr class="hover:bg-slate-50/60 transition-colors duration-100">

                                {{-- Naam van het lid --}}
                                <td class="px-5 py-3.5">
                                    <div class="flex items-center gap-3">
                                        {{-- Avatar cirkel met initialen --}}
                                        <div class="w-8 h-8 rounded-full bg-slate-200 flex items-center justify-center text-xs font-bold text-slate-600 shrink-0">
                                            {{ strtoupper(substr($betaling->naam, 0, 1)) }}
                                        </div>
                                        <span class="font-medium text-slate-800">{{ $betaling->naam }}</span>
                                    </div>
                                </td>

                                {{-- Bedrag --}}
                                <td class="px-5 py-3.5 font-semibold text-slate-800">
                                    SRD {{ number_format($betaling->bedrag, 2, ',', '.') }}
                                </td>

                                {{-- Betaalmethode met icon --}}
                                <td class="px-5 py-3.5 text-slate-600">
                                    @if($betaling->methode === 'overmaking')
                                        <span class="inline-flex items-center gap-1.5">
                                            <i class="fa-solid fa-arrow-right-arrow-left text-slate-400 text-xs"></i>
                                            overmaking
                                        </span>
                                    @elseif($betaling->methode === 'fysiek')
                                        <span class="inline-flex items-center gap-1.5">
                                            <i class="fa-solid fa-building-columns text-slate-400 text-xs"></i>
                                            fysiek
                                        </span>
                                    @else
                                        <span class="text-slate-400 italic">Onbekend</span>
                                    @endif
                                </td>

                                {{-- Status badge --}}
                                <td class="px-5 py-3.5">
                                    @if($betaling->status === 'betaald')
                                        <span class="inline-flex items-center gap-1 bg-green-50 text-green-700 border border-green-200/80 text-xs font-semibold px-2.5 py-1 rounded-full">
                                            <i class="fa-solid fa-circle-check text-[0.6rem]"></i> Betaald
                                        </span>
                                    @elseif($betaling->status === 'Openstaand')
                                        <span class="inline-flex items-center gap-1 bg-yellow-50 text-yellow-700 border border-yellow-200/80 text-xs font-semibold px-2.5 py-1 rounded-full">
                                            <i class="fa-solid fa-clock text-[0.6rem]"></i> Openstaand
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 bg-red-50 text-red-700 border border-red-200/80 text-xs font-semibold px-2.5 py-1 rounded-full">
                                            <i class="fa-solid fa-circle-xmark text-[0.6rem]"></i> Niet betaald
                                        </span>
                                    @endif
                                </td>

                                {{-- Maand en jaar van de betaling --}}
                                <td class="px-5 py-3.5 text-slate-600">
                                    {{ \Carbon\Carbon::createFromDate($betaling->jaar, $betaling->maand, 1)->translatedFormat('F Y') }}
                                </td>

                                <!-- Datum waarop de betaling verwijderd is -->
                                <td class="px-5 py-3.5 text-slate-500 text-xs">
                                    {{ \Carbon\Carbon::parse($betaling->deleted_at)->format('d M Y, H:i') }}
                                </td>

                                <!-- Herstel knop -->
                                <td class="px-5 py-3.5">
                                <button
                                class="restore-betaling-btn inline-flex items-center gap-1.5 bg-green-50 hover:bg-green-100 text-green-700 border border-green-200/80 text-xs font-semibold px-3 py-1.5 rounded-lg transition-all duration-150"
                                data-id="{{ $betaling->betaling_id }}"
                                title="Betaling herstellen"
                                onclick="herstelBetaling()">
                                <i class="fa-solid fa-rotate-left text-[0.625rem]"></i>
                                Herstellen
                            </button>
                                </td>

                            </tr>

                            {{-- Lege staat als er geen records zijn --}}
                            @empty
                            <tr>
                                <td colspan="7" class="px-5 py-16 text-center">
                                    <div class="flex flex-col items-center gap-3 text-slate-400">
                                        {{-- Prullenbak icon --}}
                                        <i class="fa-solid fa-trash-can text-4xl text-slate-200"></i>
                                        <p class="text-sm font-medium">Geen verwijderde betalingen gevonden</p>
                                        <p class="text-xs text-slate-300">Verwijderde betalingen verschijnen hier</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse

                        </tbody>
                    </table>
                </div>

            </div>
            {{-- Einde tabel kaart --}}

        </div>
        {{-- Einde p-6 --}}

    </div>
    {{-- Einde hoofdinhoud --}}

</div>


@vite('resources/js/Pages/Herstel.js')

</body>
</html>