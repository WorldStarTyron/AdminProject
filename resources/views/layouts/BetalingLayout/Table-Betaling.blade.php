{{-- Payments Table Section --}}
<main class="px-8 pb-8 flex-1">
    <div class="bg-white rounded-2xl shadow-[0_1px_3px_rgba(0,0,0,0.04),0_4px_12px_rgba(0,0,0,0.03)] border border-slate-200/60 p-7 animate-[fadeSlideUp_0.5s_ease-out]">

        {{-- Table Header --}}
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-7 gap-4">
            <div>
                <h2 class="text-xl font-extrabold text-slate-800 tracking-tight">Betaling per lid</h2>
            </div>
            <div class="flex items-center gap-3">
                {{-- FILTER DROPDOWN --}}
                <div x-data="{ open: false }" class="relative">
                    <button
                        @click="open = !open"
                        @keydown.escape="open = false"
                        type="button"
                        class="inline-flex items-center gap-2 bg-white border-[1.5px] {{ request('methode') ? 'border-[#1e3a8a] text-[#1e3a8a]' : 'border-slate-200 text-slate-600' }} font-['Inter',sans-serif] text-[0.8125rem] font-semibold px-4 py-2.5 rounded-[10px] hover:bg-slate-50 hover:border-slate-300 transition-all duration-200 cursor-pointer whitespace-nowrap"
                    >
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M22 3H2L10 12.46V19L14 21V12.46L22 3Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        @if(request('methode') == 'fysiek')
                            Fysiek
                        @elseif(request('methode') == 'overmaking')
                            Overmaking
                        @else
                            Filter
                        @endif
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" :class="open ? 'rotate-180' : ''" class="transition-transform duration-200 opacity-50">
                            <path d="M6 9L12 15L18 9" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </button>

                    {{-- Dropdown panel --}}
                    <div
                        x-show="open"
                        x-transition:enter="transition ease-out duration-100"
                        x-transition:enter-start="opacity-0 scale-95"
                        x-transition:enter-end="opacity-100 scale-100"
                        x-transition:leave="transition ease-in duration-75"
                        x-transition:leave-start="opacity-100 scale-100"
                        x-transition:leave-end="opacity-0 scale-95"
                        @click.outside="open = false"
                        class="absolute left-0 mt-1.5 w-44 bg-white rounded-xl shadow-lg border border-slate-200 py-1.5 z-50" style="display: none;">
                        <p class="px-4 pt-1 pb-2 text-[0.6875rem] font-bold text-slate-400 uppercase tracking-wider">Betaalmethode</p>

                        <a href="{{ route('betalingPagina') }}"
                           class="flex items-center justify-between px-4 py-2 text-[0.8125rem] text-slate-700 hover:bg-slate-50 transition-colors {{ !request('methode') ? 'font-semibold text-[#1e3a8a]' : '' }}">
                            Alle methodes
                            @if(!request('methode'))
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none"><path d="M20 6L9 17L4 12" stroke="#1e3a8a" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            @endif
                        </a>

                        <a href="{{ route('betalingPagina', ['methode' => 'fysiek']) }}"
                           class="flex items-center justify-between px-4 py-2 text-[0.8125rem] text-slate-700 hover:bg-slate-50 transition-colors {{ request('methode') == 'fysiek' ? 'font-semibold text-[#1e3a8a]' : '' }}">
                            Fysiek
                            @if(request('methode') == 'fysiek')
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none"><path d="M20 6L9 17L4 12" stroke="#1e3a8a" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            @endif
                        </a>

                        <a href="{{ route('betalingPagina', ['methode' => 'overmaking']) }}"
                           class="flex items-center justify-between px-4 py-2 text-[0.8125rem] text-slate-700 hover:bg-slate-50 transition-colors {{ request('methode') == 'overmaking' ? 'font-semibold text-[#1e3a8a]' : '' }}">
                            Overmaking
                            @if(request('methode') == 'overmaking')
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none"><path d="M20 6L9 17L4 12" stroke="#1e3a8a" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            @endif
                        </a>
                    </div>
                </div>

                <!-- Check Subscriptie knop - checkt of alle leden betaald hebben deze maand -->
                <button
                    id="checkSubscriptieBtn"
                    onclick="checkSubscriptie()"
                    class="inline-flex items-center gap-2 bg-gradient-to-br from-amber-500 to-orange-500 text-white font-['Inter',sans-serif] text-[0.8125rem] font-semibold px-5 py-2.5 rounded-[10px] shadow-[0_2px_8px_rgba(245,158,11,0.25)] hover:-translate-y-0.5 hover:shadow-[0_6px_16px_rgba(245,158,11,0.35)] active:translate-y-0 transition-all duration-200 cursor-pointer border-none whitespace-nowrap"
                >
                    <!-- Refresh icon -->
                    <svg id="subscriptieIcon" width="14" height="14" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M1 4V10H7" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M23 20V14H17" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M20.49 9A9 9 0 005.64 5.64L1 10M23 14L18.36 18.36A9 9 0 013.51 15" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    Check Subscriptie
                </button>

                <button id="openBetalingModal" class="inline-flex items-center gap-2 bg-gradient-to-br from-[#1e3a8a] to-[#2563eb] text-white font-['Inter',sans-serif] text-[0.8125rem] font-semibold px-5 py-2.5 rounded-[10px] shadow-[0_2px_8px_rgba(30,58,138,0.2)] hover:-translate-y-0.5 hover:shadow-[0_6px_16px_rgba(30,58,138,0.3)] active:translate-y-0 transition-all duration-200 cursor-pointer border-none whitespace-nowrap">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M12 5V19M5 12H19" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    Voeg Betaling
                </button>
            </div>
        </div>

        {{-- Table --}}
        <div class="overflow-x-auto rounded-[10px] border border-slate-100">
            <table class="w-full border-collapse text-left" id="betalingTable">
                <thead>
                    <tr class="bg-slate-50/80">
                        <th class="px-5 py-3.5 text-[0.6875rem] font-bold text-slate-400 uppercase tracking-wider border-b border-slate-200 whitespace-nowrap">ID</th>
                        <th class="px-5 py-3.5 text-[0.6875rem] font-bold text-slate-400 uppercase tracking-wider border-b border-slate-200 whitespace-nowrap">Naam</th>
                        <th class="px-5 py-3.5 text-[0.6875rem] font-bold text-slate-400 uppercase tracking-wider border-b border-slate-200 whitespace-nowrap">Datum</th>
                        <th class="px-5 py-3.5 text-[0.6875rem] font-bold text-slate-400 uppercase tracking-wider border-b border-slate-200 whitespace-nowrap">Bedrag</th>
                        <th class="px-5 py-3.5 text-[0.6875rem] font-bold text-slate-400 uppercase tracking-wider border-b border-slate-200 whitespace-nowrap">Status</th>
                        <th class="px-5 py-3.5 text-[0.6875rem] font-bold text-slate-400 uppercase tracking-wider border-b border-slate-200">Betaling <br> Method</th>
                        <th class="px-5 py-3.5 text-[0.6875rem] font-bold text-slate-400 uppercase tracking-wider border-b border-slate-200 whitespace-nowrap">Bonnummer</th>
                        <th class="px-5 py-3.5 text-[0.6875rem] font-bold text-slate-400 uppercase tracking-wider border-b border-slate-200 whitespace-nowrap w-10"></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($betalingen as $betaling)
                    <tr class="transition-colors duration-150 hover:bg-slate-50/60 border-b border-slate-100/80 last:border-b-0">

                        {{-- ID --}}
                        <td class="px-5 py-4 whitespace-nowrap">
                            <span class="text-slate-400 text-sm font-medium">{{ $loop->iteration + ($betalingen->currentPage() - 1) * $betalingen->perPage() }}</span>
                        </td>

                        {{-- Naam --}}
                        <td class="px-5 py-4 whitespace-nowrap">
                            <span class="text-slate-800 font-semibold text-sm">{{ $betaling->gebruiker_naam ?? '—' }}</span>
                        </td>

                        {{-- Datum --}}
                        <td class="px-5 py-4 whitespace-nowrap">
                            <span class="text-slate-500 text-sm">{{ \Carbon\Carbon::parse($betaling->ingediend_op)->translatedFormat('j F Y') }}</span>
                        </td>

                        {{-- Bedrag --}}
                        <td class="px-5 py-4 whitespace-nowrap">
                            <span class="text-slate-800 text-sm font-bold">Srd {{ number_format($betaling->bedrag, 0) }}</span>
                        </td>

                        {{-- Status --}}
                        <td class="px-5 py-4 whitespace-nowrap">
                            @php
                                $statusStyles = match($betaling->status) {
                                    'betaald'        => 'bg-emerald-50 text-emerald-600 ring-1 ring-emerald-200',
                                    'niet_betaald'   => 'bg-red-50 text-red-500 ring-1 ring-red-200',
                                    'in_behandeling' => 'bg-amber-50 text-amber-600 ring-1 ring-amber-200',
                                    'afgewezen'      => 'bg-slate-100 text-slate-500 ring-1 ring-slate-200',
                                    default          => 'bg-slate-100 text-slate-500 ring-1 ring-slate-200',
                                };
                                $statusLabel = match($betaling->status) {
                                    'betaald'        => 'BETAALD',
                                    'niet_betaald'   => 'NIET BETAALD',
                                    'in_behandeling' => 'IN BEHANDELING',
                                    'afgewezen'      => 'AFGEWEZEN',
                                    default          => strtoupper($betaling->status),
                                };
                            @endphp
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-[0.625rem] font-bold tracking-widest {{ $statusStyles }}">
                                {{ $statusLabel }}
                            </span>
                        </td>

                        {{-- Methode --}}
                        <td class="px-5 py-4 whitespace-nowrap">
                            <span class="text-slate-600 text-sm">{{ $betaling->methode ?? '—' }}</span>
                        </td>

                        {{-- Bonnummer --}}
                        <td class="px-5 py-4 whitespace-nowrap">
                            @if($betaling->bon)
                                <span class="text-slate-700 text-sm font-mono font-medium">{{ $betaling->bon->bon_nummer }}</span>
                            @else
                                <span class="text-slate-400 text-sm">—</span>
                            @endif
                        </td>

                        {{-- Actions --}}
                        <td class="px-5 py-4 whitespace-nowrap text-right">
                            <div class="relative inline-block">
                                <button onclick="toggleDropdown('dropdown-{{ $betaling->betaling_id }}', event)" class="w-8 h-8 flex items-center justify-center rounded-lg text-slate-400 hover:bg-slate-100 hover:text-slate-600 transition-all duration-200 cursor-pointer">
                                    <svg width="4" height="16" viewBox="0 0 4 20" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                        <circle cx="2" cy="2" r="2"/>
                                        <circle cx="2" cy="10" r="2"/>
                                        <circle cx="2" cy="18" r="2"/>
                                    </svg>
                                </button>

                                <!-- Dropdown -->
                                <div id="dropdown-{{ $betaling->betaling_id }}" class="absolute right-0 mt-1 w-40 bg-white rounded-xl shadow-lg border border-slate-200 py-1.5 z-50 hidden">
                                    <button onclick="deleteBetaling({{ $betaling->betaling_id }})" class="flex items-center gap-2.5 px-4 py-2 text-sm text-red-500 hover:bg-red-50 transition-colors w-full text-left cursor-pointer">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" class="text-red-400"><path d="M3 6H5H21" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M8 6V4C8 3.46957 8.21071 2.96086 8.58579 2.58579C8.96086 2.21071 9.46957 2 10 2H14C14.5304 2 15.0391 2.21071 15.4142 2.58579C15.7893 2.96086 16 3.46957 16 4V6M19 6V20C19 20.5304 18.7893 21.0391 18.4142 21.4142C18.0391 21.7893 17.5304 22 17 22H7C6.46957 22 5.96086 21.7893 5.58579 21.4142C5.21071 21.0391 5 20.5304 5 20V6H19Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                        Verwijder
                                    </button>
                                </div>
                            </div>
                        </td>

                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-5 py-12 text-center">
                            <div class="flex flex-col items-center gap-3">
                                <div class="w-12 h-12 bg-slate-100 rounded-xl flex items-center justify-center">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" class="text-slate-400"><path d="M22 19C22 19.5304 21.7893 20.0391 21.4142 20.4142C21.0391 20.7893 20.5304 21 20 21H4C3.46957 21 2.96086 20.7893 2.58579 20.4142C2.21071 20.0391 2 19.5304 2 19V5C2 4.46957 2.21071 3.96086 2.58579 3.58579C2.96086 3.21071 3.46957 3 4 3H9L11 6H20C20.5304 6 21.0391 6.21071 21.4142 6.58579C21.7893 6.96086 22 7.46957 22 8V19Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                </div>
                                <p class="text-sm text-slate-400 font-medium">Geen betalingen gevonden</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($betalingen->hasPages())
        <div class="mt-6 flex flex-col sm:flex-row justify-between items-center gap-4">
            <p class="text-[0.8125rem] text-slate-400 font-medium">
                Toont {{ $betalingen->firstItem() }}–{{ $betalingen->lastItem() }} van {{ $betalingen->total() }} betalingen
            </p>
            <div class="flex items-center gap-1">
                {{-- Previous --}}
                @if($betalingen->onFirstPage())
                    <span class="w-[34px] h-[34px] flex items-center justify-center rounded-lg text-slate-300 cursor-not-allowed">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none"><path d="M15 18L9 12L15 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </span>
                @else
                    <a href="{{ $betalingen->previousPageUrl() }}" class="w-[34px] h-[34px] flex items-center justify-center rounded-lg text-slate-500 hover:bg-slate-100 hover:text-[#1e3a8a] cursor-pointer transition-all duration-200">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none"><path d="M15 18L9 12L15 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </a>
                @endif

                {{-- Page Numbers --}}
                @foreach($betalingen->links()->elements as $element)
                    @if(is_string($element))
                        <span class="text-slate-300 font-semibold text-xs tracking-widest px-1">•••</span>
                    @endif
                    @if(is_array($element))
                        @foreach($element as $page => $url)
                            @if($page == $betalingen->currentPage())
                                <span class="w-[34px] h-[34px] flex items-center justify-center rounded-lg text-[0.8125rem] font-semibold text-white bg-[#1e3a8a] cursor-default">{{ $page }}</span>
                            @else
                                <a href="{{ $url }}" class="w-[34px] h-[34px] flex items-center justify-center rounded-lg text-[0.8125rem] font-semibold text-slate-500 hover:bg-slate-100 hover:text-[#1e3a8a] cursor-pointer transition-all duration-200">{{ $page }}</a>
                            @endif
                        @endforeach
                    @endif
                @endforeach

                {{-- Next --}}
                @if($betalingen->hasMorePages())
                    <a href="{{ $betalingen->nextPageUrl() }}" class="w-[34px] h-[34px] flex items-center justify-center rounded-lg text-slate-500 hover:bg-slate-100 hover:text-[#1e3a8a] cursor-pointer transition-all duration-200">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none"><path d="M9 18L15 12L9 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </a>
                @else
                    <span class="w-[34px] h-[34px] flex items-center justify-center rounded-lg text-slate-300 cursor-not-allowed">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none"><path d="M9 18L15 12L9 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </span>
                @endif
            </div>
        </div>
        @endif
       
    </div>
</main>
