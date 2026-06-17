<!--            ============================================================
                     DETAIL PAGE (ViewBewijsFile)
                     Only shown when $betaling is NOT null (a specific payment is selected).
                     This is the proof preview + approve/reject page.
                ============================================================ -->
                @if($betaling)

                    <!-- ── PAGE HEADER ── -->
                    <!-- Back button returns to the list page -->
                    <div class="flex items-center gap-4 mb-8">

                        <a href="{{ route('BewijsReceived') }}"
                           class="w-9 h-9 rounded-xl bg-white border border-gray-200 shadow-sm flex items-center justify-center text-gray-500 hover:text-gray-800 hover:bg-gray-50 transition-colors">
                            <i class="fa-solid fa-arrow-left text-sm"></i>
                        </a>

                        <!-- Page title and member info subtitle -->
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900">Betalingsbewijs Bekijken</h1>
                            <p class="text-sm text-gray-500 mt-0.5">
                                <!-- Show the member name and their ID for quick reference -->
                                {{ $betaling->lid->gebruiker->naam ?? 'Onbekend lid' }}
                                &nbsp;·&nbsp;
                                Lid #{{ $betaling->lid->lid_id ?? '' }}
                            </p>
                        </div>

                    </div>

                    <!-- ── MAIN TWO-COLUMN LAYOUT ── -->
                    <!-- Left (wider): the file preview (image or PDF)  -->
                    <!-- Right (narrower): payment details + action buttons -->
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                        <!-- LEFT COLUMN: File Preview (takes up 2/3 of the width) -->
                        <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">

                            <!-- Card header -->
                            <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-2">
                                <i class="fa-solid fa-file-image text-gray-400"></i>
                                <h2 class="font-semibold text-gray-900 text-sm">Ingediend Bewijs</h2>
                            </div>

                            <!-- File display area -->
                            <div class="p-6">

                                @if($isPdf)
                                    {{--
                                        PDF preview using an <iframe>.
                                        The browser renders the PDF directly inside the page.
                                        Height is fixed so it does not push the layout around.
                                    --}}
                                    <iframe src="{{ $bewijsUrl }}"
                                            class="w-full rounded-lg border border-gray-200"
                                            style="height: 520px;"
                                            title="Betalingsbewijs PDF">
                                    </iframe>
                                    
                                    <!--Fallback knop als iframe het bewijs niet will wijzen-->
                                    <a href="{{ $bewijsUrl }}" target="_blank"
                                    class="mt-3 inline-flex items-center gap-2 text-sm text-indigo-600 hover:underline">
                                    <i class="fa-solid fa-arrow-up-right-from-square"></i>
                                    PDF openen in nieuw tabblad
                                    </a>
                                @else
                                    {{--
                                        Image preview (jpg, png).
                                        object-contain keeps the full image visible without cropping.
                                    --}}
                                    <div class="flex items-center justify-center bg-gray-50 rounded-lg border border-gray-200 p-4" style="min-height: 400px;">
                                        <img src="{{ $bewijsUrl }}"
                                             alt="Betalingsbewijs van {{ $betaling->lid->gebruiker->naam ?? 'lid' }}"
                                             class="max-w-full max-h-[480px] object-contain rounded">
                                    </div>
                                @endif

                            </div>

                        </div>
                        <!-- End left column -->


                        <!-- RIGHT COLUMN: Payment Details + Action Buttons -->
                        <div class="flex flex-col gap-6">

                            <!-- Payment Details Card -->
                            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">

                                <h2 class="font-bold text-gray-900 text-sm mb-4 flex items-center gap-2">
                                    <i class="fa-solid fa-receipt text-gray-400"></i>
                                    Betalingsinformatie
                                </h2>

                                <!-- Detail rows: label on the left, value on the right -->
                                <dl class="space-y-3 text-sm">

                                    <!-- Member name -->
                                    <div class="flex justify-between gap-2">
                                        <dt class="text-gray-400">Lid</dt>
                                        <dd class="font-semibold text-gray-900 text-right">
                                            {{ $betaling->lid->gebruiker->naam ?? 'Onbekend' }}
                                        </dd>
                                    </div>

                                    <!-- Lid ID -->
                                    <div class="flex justify-between gap-2">
                                        <dt class="text-gray-400">Lid ID</dt>
                                        <dd class="font-semibold text-gray-900">#{{ $betaling->lid->lid_id }}</dd>
                                    </div>

                                    <!-- Payment amount formatted -->
                                    <div class="flex justify-between gap-2">
                                        <dt class="text-gray-400">Bedrag</dt>
                                        <dd class="font-bold text-gray-900">
                                            SRD {{ number_format($betaling->bedrag, 2, ',', '.') }}
                                        </dd>
                                    </div>

                                    <!-- Date the proof was submitted -->
                                    <div class="flex justify-between gap-2">
                                        <dt class="text-gray-400">Ingediend op</dt>
                                        <dd class="text-gray-700">
                                            {{ $betaling->ingediend_op
                                                ? \Carbon\Carbon::parse($betaling->ingediend_op)->translatedFormat('d M Y')
                                                : '—' }}
                                        </dd>
                                    </div>

                                    <!-- Divider between info and status -->
                                    <div class="border-t border-gray-100 pt-3">
                                        <div class="flex justify-between gap-2">
                                            <dt class="text-gray-400">Status</dt>
                                            <dd>
                                                <!-- Status badge: colour depends on current status value -->
                                                @if($betaling->status === 'in_afwachting')
                                                    <span class="bg-blue-100 text-blue-600 text-xs font-semibold px-3 py-1 rounded-full">
                                                        In afwachting
                                                    </span>
                                                @elseif($betaling->status === 'betaald')
                                                    <span class="bg-green-100 text-green-700 text-xs font-semibold px-3 py-1 rounded-full">
                                                        Goedgekeurd
                                                    </span>
                                                @else
                                                    <span class="bg-red-100 text-red-600 text-xs font-semibold px-3 py-1 rounded-full">
                                                        Niet goedgekeurd
                                                    </span>
                                                @endif
                                            </dd>
                                        </div>
                                    </div>

                                </dl>
                            </div>
                            <!-- End payment details card -->


                            <!-- Action buttons: only shown when payment is still pending -->
                            @if($betaling->status === 'in_afwachting')
                                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">

                                    <h2 class="font-bold text-gray-900 text-sm mb-4 flex items-center gap-2">
                                        <i class="fa-solid fa-gavel text-gray-400"></i>
                                        Beoordeling
                                    </h2>

                                    <div class="flex flex-col gap-3">

                                        <!-- APPROVE button -->
                                        <!-- Sends a PATCH to the ApproveBewijsBetaling route -->
                                        <form action="{{ route('BewijsReceived.Approve', $betaling->betaling_id) }}" method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit"
                                                    class="w-full bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold px-4 py-2.5 rounded-xl transition-colors flex items-center justify-center gap-2">
                                                <i class="fa-solid fa-circle-check"></i>
                                                Goedkeuren
                                            </button>
                                        </form>

                                        <!-- REJECT button -->
                                        <!-- Sends a PATCH to the RejectBewijsBetaling route -->
                                        <form action="{{ route('BewijsReceived.Reject', $betaling->betaling_id) }}" method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit"
                                                    class="w-full bg-white hover:bg-red-50 text-red-600 border border-red-200 text-sm font-semibold px-4 py-2.5 rounded-xl transition-colors flex items-center justify-center gap-2">
                                                <i class="fa-solid fa-circle-xmark"></i>
                                                Afwijzen
                                            </button>
                                        </form>

                                    </div>

                                </div>
                            @endif
                            <!-- End action buttons card -->

                        </div>
                        <!-- End right column -->

                    </div>
                    <!-- End two-column layout -->

                @endif
                <!-- End Section B (detail page) -->


            </div>
            <!-- End page content wrapper -->

        </div>
        <!-- End main content area -->

    </div>
    <!-- End outer layout wrapper -->
