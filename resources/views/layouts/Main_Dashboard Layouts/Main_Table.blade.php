<!-- Table Section -->
<div class="bg-white rounded-xl border border-gray-200 shadow-[0_1px_3px_rgba(0,0,0,0.02)] overflow-hidden">
    <!-- Table Header -->
    <div class="flex justify-between items-center px-6 py-5 border-b border-gray-100">
        <h3 class="text-base font-bold text-gray-900">Leden Overzicht</h3>
        <div class="flex items-center gap-3.5">
            <span class="text-xs text-gray-500 font-medium">
                Showing {{ $dashboardLeden->firstItem() ?? 0 }} to {{ $dashboardLeden->lastItem() ?? 0 }} of {{ $dashboardLeden->total() }} entries
            </span>
            <div class="flex items-center gap-1.5">
                @if($dashboardLeden->onFirstPage())
                    <span class="w-7 h-7 flex items-center justify-center rounded-lg border border-gray-200 bg-white text-gray-300 cursor-not-allowed">
                        <i class="fa-solid fa-chevron-left text-[10px]"></i>
                    </span>
                @else
                    <a href="{{ $dashboardLeden->previousPageUrl() }}" class="w-7 h-7 flex items-center justify-center rounded-lg border border-gray-200 bg-white text-gray-400 hover:text-gray-600 transition hover:bg-gray-50">
                        <i class="fa-solid fa-chevron-left text-[10px]"></i>
                    </a>
                @endif

                @if($dashboardLeden->hasMorePages())
                    <a href="{{ $dashboardLeden->nextPageUrl() }}" class="w-7 h-7 flex items-center justify-center rounded-lg border border-gray-200 bg-white text-gray-400 hover:text-gray-600 transition hover:bg-gray-50">
                        <i class="fa-solid fa-chevron-right text-[10px]"></i>
                    </a>
                @else
                    <span class="w-7 h-7 flex items-center justify-center rounded-lg border border-gray-200 bg-white text-gray-300 cursor-not-allowed">
                        <i class="fa-solid fa-chevron-right text-[10px]"></i>
                    </span>
                @endif
            </div>
        </div>
    </div>

    <!-- Table Content -->
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="text-[11px] font-extrabold tracking-wider text-gray-500 uppercase bg-gray-50/50 border-b border-gray-100">
                    <th class="py-3.5 px-6">Leden ID</th>
                    <th class="py-3.5 px-6">Naam</th>
                    <th class="py-3.5 px-6">Telefoon</th>
                    <th class="py-3.5 px-6 text-center">Betaling Status</th>
                    <th class="py-3.5 px-6 w-20 text-center">Acties</th>
                </tr>
            </thead>
            <tbody class="text-sm divide-y divide-gray-100">
                @forelse($dashboardLeden as $lid)
                    @php
                        // Determine payment status based on latest payment
                        $latestPayment = $lid->betalingen->sortByDesc('ingediend_op')->first();
                        $isPaid = $latestPayment && strtolower($latestPayment->status) === 'betaald';

                        // Dynamic gradient for avatar
                        $gradients = [
                            'from-blue-500 to-indigo-600',
                            'from-pink-500 to-rose-600',
                            'from-emerald-400 to-teal-500',
                            'from-amber-400 to-orange-500',
                            'from-violet-500 to-purple-600',
                        ];
                        $gradient = $gradients[$lid->lid_id % count($gradients)];

                        // Initials calculation
                        $initials = '';
                        if ($lid->gebruiker && $lid->gebruiker->naam) {
                            $nameParts = explode(' ', $lid->gebruiker->naam);
                            $initials = strtoupper(substr($nameParts[0], 0, 1));
                            if (count($nameParts) > 1) {
                                $initials .= strtoupper(substr($nameParts[1], 0, 1));
                            }
                        } else {
                            $initials = '??';
                        }
                    @endphp
                    <tr class="hover:bg-gray-50/50 transition-colors">
                        <td class="py-4 px-6">
                            <span class="inline-flex px-2 py-0.5 text-[11px] font-extrabold tracking-wide text-indigo-600 bg-indigo-50 border border-indigo-100 rounded-md font-mono">
                                {{ str_pad($lid->lid_id, 3,) }}
                            </span>
                        </td>
                        <td class="py-4 px-6">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-gradient-to-tr {{ $gradient }} flex items-center justify-center text-[11px] font-extrabold text-white shadow-sm">
                                    {{ $initials }}
                                </div>
                                <span class="font-semibold text-gray-900">{{ $lid->gebruiker->naam ?? '—' }}</span>
                            </div>
                        </td>
                        <td class="py-4 px-6 text-gray-500 font-medium">
                            {{ $lid->telefoonnummer ?? '—' }}
                        </td>
                        <td class="py-4 px-6 text-center">
                            @if($isPaid)
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-100/50 shadow-[0_1px_2px_rgba(16,185,129,0.02)]">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                    Betaald
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold bg-red-50 text-red-700 border border-red-100/50 shadow-[0_1px_2px_rgba(239,68,68,0.02)]">
                                    <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>
                                    Niet betaald
                                </span>
                            @endif
                        </td>
                        <td class="py-4 px-6 text-center">
                            <a href="{{ route('ledenpagina.show', $lid->lid_id) }}" class="inline-block text-gray-400 hover:text-gray-600 p-1 rounded-lg hover:bg-gray-100 transition" title="Bekijken">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="1"></circle><circle cx="12" cy="5" r="1"></circle><circle cx="12" cy="19" r="1"></circle></svg>
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="py-8 text-center text-gray-400">
                            Geen leden gevonden in het systeem.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Table Footer / View all members button -->
    <div class="bg-gray-50 border-t border-gray-100 px-6 py-4 flex items-center justify-center">
        <a href="{{ route('ledenpagina') }}" class="text-xs text-gray-600 hover:text-gray-900 font-bold tracking-wide uppercase transition">
            View all members
        </a>
    </div>
</div>