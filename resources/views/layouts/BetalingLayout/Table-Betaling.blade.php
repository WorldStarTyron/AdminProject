{{-- Payments Table Section --}}
<main class="px-8 pb-8 flex-1">
    <div class="bg-white rounded-2xl shadow-[0_1px_3px_rgba(0,0,0,0.04),0_4px_12px_rgba(0,0,0,0.03)] border border-slate-200/60 p-7 animate-[fadeSlideUp_0.5s_ease-out]">

        <!-- Table header with title, month/year filter and action buttons -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-7 gap-4">
            <div>
                <h2 class="text-xl font-extrabold text-slate-800 tracking-tight">Betaling Table</h2>
            </div>
            {{-- Month/year filter (merged from Leden Betalingsstatus) --}}
            <form method="GET" action="{{ route('betalingPagina') }}" class="flex items-center gap-2">
                <select name="maand" class="border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#1e3a8a]/20">
                    @for($m = 1; $m <= 12; $m++)
                        <option value="{{ $m }}" {{ ($maand ?? now()->month) == $m ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                        </option>
                    @endfor
                </select>
                <select name="jaar" class="border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#1e3a8a]/20">
                    @for($y = now()->year - 1; $y <= now()->year + 1; $y++)
                        <option value="{{ $y }}" {{ ($jaar ?? now()->year) == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
                <button type="submit" class="bg-slate-100 hover:bg-slate-200 px-4 py-2 rounded-lg text-sm font-medium transition-colors">Filter</button>
            </form>
            <div class="flex items-center gap-3">

                <!-- Filter dropdown (Alpine.js) -->
                <div x-data="{ open: false }" class="relative">
                    <button
                        @click="open = !open"
                        @keydown.escape="open = false"
                        type="button"
                        class="inline-flex items-center gap-2 bg-white border-[1.5px] {{ request('methode') ? 'border-[#1e3a8a] text-[#1e3a8a]' : 'border-slate-200 text-slate-600' }} font-['Inter',sans-serif] text-[0.8125rem] font-semibold px-4 py-2.5 rounded-[10px] hover:bg-slate-50 hover:border-slate-300 transition-all duration-200 cursor-pointer whitespace-nowrap">
                        <!-- Filter icon -->
                        <i class="fa-solid fa-filter text-xs"></i>
                        @if(request('methode') == 'fysiek')
                            Fysiek
                        @elseif(request('methode') == 'overmaking')
                            Overmaking
                        @else
                            Filter
                        @endif
                        <!-- Chevron rotates when dropdown is open -->
                        <i class="fa-solid fa-chevron-down text-[10px] opacity-50 transition-transform duration-200" :class="open ? 'rotate-180' : ''"></i>
                    </button>

                    <!-- Dropdown panel -->
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

                        <!-- Option: all methods -->
                        <a href="{{ route('betalingPagina') }}"
                           class="flex items-center justify-between px-4 py-2 text-[0.8125rem] text-slate-700 hover:bg-slate-50 transition-colors {{ !request('methode') ? 'font-semibold text-[#1e3a8a]' : '' }}">
                            Alle methodes
                            @if(!request('methode'))
                                <!-- Active check icon -->
                                <i class="fa-solid fa-check text-[#1e3a8a] text-xs"></i>
                            @endif
                        </a>

                        <!-- Option: fysiek -->
                        <a href="{{ route('betalingPagina', ['methode' => 'fysiek']) }}"
                           class="flex items-center justify-between px-4 py-2 text-[0.8125rem] text-slate-700 hover:bg-slate-50 transition-colors {{ request('methode') == 'fysiek' ? 'font-semibold text-[#1e3a8a]' : '' }}">
                            Fysiek
                            @if(request('methode') == 'fysiek')
                                <i class="fa-solid fa-check text-[#1e3a8a] text-xs"></i>
                            @endif
                        </a>

                        <!-- Option: overmaking -->
                        <a href="{{ route('betalingPagina', ['methode' => 'overmaking']) }}"
                           class="flex items-center justify-between px-4 py-2 text-[0.8125rem] text-slate-700 hover:bg-slate-50 transition-colors {{ request('methode') == 'overmaking' ? 'font-semibold text-[#1e3a8a]' : '' }}">
                            Overmaking
                            @if(request('methode') == 'overmaking')
                                <i class="fa-solid fa-check text-[#1e3a8a] text-xs"></i>
                            @endif
                        </a>
                    </div>
                </div>

                @can('betalingen-beheren')
                <!-- Add payment button -->
                <button id="openBetalingModal" class="inline-flex items-center gap-2 bg-gradient-to-br from-[#1e3a8a] to-[#2563eb] text-white font-['Inter',sans-serif] text-[0.8125rem] font-semibold px-5 py-2.5 rounded-[10px] shadow-[0_2px_8px_rgba(30,58,138,0.2)] hover:-translate-y-0.5 hover:shadow-[0_6px_16px_rgba(30,58,138,0.3)] active:translate-y-0 transition-all duration-200 cursor-pointer border-none whitespace-nowrap">
                    <!-- Plus icon -->
                    <i class="fa-solid fa-plus text-xs"></i>
                    Voeg Betaling
                </button>
                @endcan
            </div>
        </div>



        <!-- Leden Betalingsstatus block -->
        <div class="bg-slate-50/60 rounded-xl border border-slate-100 p-5 mb-7">
            <h3 class="text-sm font-bold text-slate-500 uppercase tracking-wider mb-4">Leden Betalingsstatus</h3>
            <div class="overflow-x-auto rounded-[10px] border border-slate-100">
                <table class="w-full border-collapse text-left">
                    <thead>
                        <tr class="bg-slate-50/80">
                            <th class="px-2 py-3.5 text-[0.6875rem] font-bold text-slate-400 uppercase tracking-wider">Naam</th>
                            <th class="px-2 py-3.5 text-[0.6875rem] font-bold text-slate-400 uppercase tracking-wider">Status</th>
                            <th class="px-2 py-3.5 text-[0.6875rem] font-bold text-slate-400 uppercase tracking-wider">Lid Type</th>
                            <th class="px-2 py-3.5 text-[0.6875rem] font-bold text-slate-400 uppercase tracking-wider">Bedrag</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($ledenStatus as $item)
                            @php
                                $ledenStatusStyles = match($item->status) {
                                    'betaald'        => 'bg-emerald-50 text-emerald-600 ring-1 ring-emerald-200',
                                    'niet_betaald'   => 'bg-red-50 text-red-500 ring-1 ring-red-200',
                                    'Openstaand'     => 'bg-amber-50 text-amber-600 ring-1 ring-amber-200',
                                    default          => 'bg-slate-100 text-slate-500 ring-1 ring-slate-200',
                                };
                                $ledenStatusLabel = match($item->status) {
                                    'betaald'        => 'BETAALD',
                                    'niet_betaald'   => 'NIET BETAALD',
                                    'Openstaand'     => 'OPENSTAAND',
                                    default          => strtoupper($item->status),
                                };
                            @endphp
                            <tr class="border-b border-slate-100/80 hover:bg-slate-50/60">
                                <td class="px-2 py-2 font-semibold text-slate-800 text-sm">{{ $item->naam }}</td>
                                <td class="px-2 py-2">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-[0.625rem] font-bold tracking-widest {{ $ledenStatusStyles }}">
                                        {{ $ledenStatusLabel }}
                                    </span>
                                </td>
                                <td class="px-5 py-4 text-slate-600 text-sm">{{ $item->lid_type }}</td>
                                <td class="px-5 py-4 font-bold text-slate-800 text-sm">SRD {{ number_format($item->bedrag, 2) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="px-5 py-8 text-center text-slate-400 text-sm">Geen leden gevonden</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Payments table -->
        <div class="overflow-x-auto rounded-[10px] border border-slate-100">
            <table class="w-full border-collapse text-left" id="betalingTable">
                <thead>
                    <tr class="bg-slate-50/80">
                        
                        <th class="px-5 py-1 text-[0.6875rem] font-bold text-slate-400 uppercase tracking-wider border-b border-slate-200 whitespace-nowrap">Naam</th>
                        <th class="px-5 py-1 text-[0.6875rem] font-bold text-slate-400 uppercase tracking-wider border-b border-slate-200 whitespace-nowrap">Datum</th>
                        <th class="px-5 py-1 text-[0.6875rem] font-bold text-slate-400 uppercase tracking-wider border-b border-slate-200 whitespace-nowrap">Bedrag</th>
                        <th class="px-5 py-1 text-[0.6875rem] font-bold text-slate-400 uppercase tracking-wider border-b border-slate-200 whitespace-nowrap">Status</th>
                        <th class="px-5 py-1 text-[0.6875rem] font-bold text-slate-400 uppercase tracking-wider border-b border-slate-200">Betaling <br> Method</th>
                        <th class="px-5 py-2 text-[0.6875rem] font-bold text-slate-400 uppercase tracking-wider border-b border-slate-200 whitespace-nowrap">Bonnummer</th>
                        @can('betalingen-beheren')
                        <th class="px-5 py-3.5 text-[0.6875rem] font-bold text-slate-400 uppercase tracking-wider border-b border-slate-200 whitespace-nowrap">Acties</th>
                        @endcan
                    </tr>
                </thead>
                <tbody>
                    @forelse($betalingen as $betaling)
                    <tr class="transition-colors duration-150 hover:bg-slate-50/60 border-b border-slate-100/80 last:border-b-0">

                        <!-- Row number based on current page -->
                        <td class="px-5 py-4 whitespace-nowrap">
                            <span class="text-slate-400 text-sm font-medium">{{ $loop->iteration + ($betalingen->currentPage() - 1) * $betalingen->perPage() }}</span>
                        </td>

                        <!-- Full name of the user -->
                        <td class="px-5 py-4 whitespace-nowrap">
                            <span class="text-slate-800 font-semibold text-sm">{{ $betaling->gebruiker_naam ?? '—' }}</span>
                        </td>

                        <!-- Payment date -->
                        <td class="px-5 py-4 whitespace-nowrap">
                            <span class="text-slate-500 text-sm">{{ \Carbon\Carbon::parse($betaling->ingediend_op)->translatedFormat('j F Y') }}</span>
                        </td>

                        <!-- Amount in SRD -->
                        <td class="px-5 py-4 whitespace-nowrap">
                            <span class="text-slate-800 text-sm font-bold">Srd {{ number_format($betaling->bedrag, 0) }}</span>
                        </td>

                        <!-- Status badge with color per status -->
                        <td class="px-5 py-4 whitespace-nowrap">
                            @php
                                $statusStyles = match($betaling->status) {
                                    'betaald'        => 'bg-emerald-50 text-emerald-600 ring-1 ring-emerald-200',
                                    'niet_betaald'   => 'bg-red-50 text-red-500 ring-1 ring-red-200',
                                    'Openstaand'     => 'bg-cyan-50 text-cyan-600 ring-1 ring-cyan-200',
                                    default          => 'bg-slate-100 text-slate-500 ring-1 ring-slate-200',
                                };
                                $statusLabel = match($betaling->status) {
                                    'betaald'        => 'BETAALD',
                                    'niet_betaald'   => 'NIET BETAALD',
                                    'Openstaand'     => 'OPENSTAAND',
                                    default          => strtoupper($betaling->status),
                                };
                            @endphp
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-[0.625rem] font-bold tracking-widest {{ $statusStyles }}">
                                {{ $statusLabel }}
                            </span>
                        </td>

                        <!-- Payment method -->
                        <td class="px-5 py-4 whitespace-nowrap">
                            <span class="text-slate-600 text-sm">{{ $betaling->methode ?? '—' }}</span>
                        </td>

                        <!-- Receipt number if available -->
                        <td class="px-5 py-4 whitespace-nowrap">
                            @if($betaling->bon)
                                <span class="text-slate-700 text-sm font-mono font-medium">{{ $betaling->bon->bon_nummer }}</span>
                            @else
                                <span class="text-slate-400 text-sm">—</span>
                            @endif
                        </td>

                        <!-- Edit button (only visible for admins) -->
                        @can('betalingen-beheren')
                        <td class="px-5 py-4 whitespace-nowrap">
                            <button
                                type="button"
                                class="edit-betaling-btn text-blue-600 hover:text-blue-900 transition-colors bg-transparent border-none cursor-pointer p-1"
                                data-id="{{ $betaling->betaling_id }}"
                                data-naam="{{ $betaling->gebruiker_naam ?? '' }}"
                                data-datum="{{ \Carbon\Carbon::parse($betaling->ingediend_op)->format('Y-m-d') }}"
                                data-bedrag="{{ $betaling->bedrag }}"
                                data-status="{{ $betaling->status }}"
                                data-methode="{{ $betaling->methode }}"
                                title="Bewerken">
                                
                                <!-- Edit icon -->
                                <i class="fa-solid fa-pen-to-square text-base"></i>
                            </button>
                        </td>
                        @endcan
                    </tr>
                    @empty
                    <!-- Empty state when no payments are found -->
                    <tr>
                        <td colspan="8" class="px-5 py-12 text-center">
                            <div class="flex flex-col items-center gap-3">
                                <div class="w-12 h-12 bg-slate-100 rounded-xl flex items-center justify-center">
                                    <i class="fa-regular fa-folder-open text-xl text-slate-400"></i>
                                </div>
                                <p class="text-sm text-slate-400 font-medium">Geen betalingen gevonden</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>



        <!-- Pagination -->
        @if($betalingen->hasPages())
        <div class="mt-6 flex flex-col sm:flex-row justify-between items-center gap-4">
            <p class="text-[0.8125rem] text-slate-400 font-medium">
                Toont {{ $betalingen->firstItem() }}–{{ $betalingen->lastItem() }} van {{ $betalingen->total() }} betalingen
            </p>
            <div class="flex items-center gap-1">

                <!-- Previous page button -->
                @if($betalingen->onFirstPage())
                    <span class="w-[34px] h-[34px] flex items-center justify-center rounded-lg text-slate-300 cursor-not-allowed">
                        <i class="fa-solid fa-chevron-left text-xs"></i>
                    </span>
                @else
                    <a href="{{ $betalingen->previousPageUrl() }}" class="w-[34px] h-[34px] flex items-center justify-center rounded-lg text-slate-500 hover:bg-slate-100 hover:text-[#1e3a8a] cursor-pointer transition-all duration-200">
                        <i class="fa-solid fa-chevron-left text-xs"></i>
                    </a>
                @endif

                <!-- Page number buttons -->
                @foreach($betalingen->links()->elements as $element)
                    @if(is_string($element))
                        <span class="text-slate-300 font-semibold text-xs tracking-widest px-1">•••</span>
                    @endif
                    @if(is_array($element))
                        @foreach($element as $page => $url)
                            @if($page == $betalingen->currentPage())
                                <!-- Active page -->
                                <span class="w-[34px] h-[34px] flex items-center justify-center rounded-lg text-[0.8125rem] font-semibold text-white bg-[#1e3a8a] cursor-default">{{ $page }}</span>
                            @else
                                <a href="{{ $url }}" class="w-[34px] h-[34px] flex items-center justify-center rounded-lg text-[0.8125rem] font-semibold text-slate-500 hover:bg-slate-100 hover:text-[#1e3a8a] cursor-pointer transition-all duration-200">{{ $page }}</a>
                            @endif
                        @endforeach
                    @endif
                @endforeach

                <!-- Next page button -->
                @if($betalingen->hasMorePages())
                    <a href="{{ $betalingen->nextPageUrl() }}" class="w-[34px] h-[34px] flex items-center justify-center rounded-lg text-slate-500 hover:bg-slate-100 hover:text-[#1e3a8a] cursor-pointer transition-all duration-200">
                        <i class="fa-solid fa-chevron-right text-xs"></i>
                    </a>
                @else
                    <span class="w-[34px] h-[34px] flex items-center justify-center rounded-lg text-slate-300 cursor-not-allowed">
                        <i class="fa-solid fa-chevron-right text-xs"></i>
                    </span>
                @endif
            </div>
        </div>
        @endif

    </div>
</main>