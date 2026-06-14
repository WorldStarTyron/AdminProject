<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Activiteit Log | Administratie Panel</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    @vite(['resources/css/app.css', 'resources/css/sidebar.css', 'resources/js/UI/Sidebar.js'])
</head>
<body class="m-0 bg-[#f0f4f8] text-slate-700 font-['Inter',sans-serif] antialiased">

<!-- ==========================================================================
     MAIN WRAPPER: Responsive dashboard container with flexible sidebar placement
     ========================================================================== -->
<div class="flex min-h-screen">
    
    <!-- INCLUDE SIDEBAR LAYOUT -->
    @include('Layouts.Sidebars.sidebar')

    <!-- MAIN PAGE CONTENT CONTAINER (offsets to make room for fixed sidebar) -->
    <div class="flex-1 ml-0 md:ml-64 transition-all duration-300 min-w-0 overflow-x-hidden">
        
        <!-- INCLUDE HEADER BAR LAYOUT -->
        @include('Layouts.Headers.header')
        
        <!-- ==========================================================================
             MAIN CONTENT SPACE
             ========================================================================== -->
        <main class="p-6 md:p-8 max-w-[1600px] mx-auto space-y-6">

            <!-- PREPARATION & FALLBACK DATA LAYER -->
            @php
                // Fetch dynamic items from controller pagination
                $displayLogs = $activiteiten->items();
                
                // Determine whether to display sample mockup data when database logs are empty
                $showMockData = count($displayLogs) === 0 && empty($search) && $tab === 'Alle';
                
                if ($showMockData) {
                    // Populate mockup objects mimicking the design layout screenshot
                    $displayLogs = [
                        (object)[
                            'log_id' => 10284,
                            'gebruiker' => (object)['naam' => 'Pieter Heijn', 'gebruiker_id' => 42],
                            'actie' => 'ingelogd',
                            'details' => 'Gebruiker Regeffio heeft ingelogd.',
                            'aangemaakt_op' => \Carbon\Carbon::parse('2023-10-24 14:32:01')
                        ],
                        (object)[
                            'log_id' => 10283,
                            'gebruiker' => (object)['naam' => 'Sarah de Vries', 'gebruiker_id' => 15],
                            'actie' => 'lid_aangemaakt',
                            'details' => 'Gebruiker Regeffio heeft een nieuw lid toegevoegd',
                            'aangemaakt_op' => \Carbon\Carbon::parse('2023-10-24 12:15:45')
                        ],
                        (object)[
                            'log_id' => 10282,
                            'gebruiker' => (object)['naam' => 'Systeem', 'gebruiker_id' => 0],
                            'actie' => 'systeem_scan',
                            'details' => ['event' => 'security_scan', 'result' => 'clean'],
                            'aangemaakt_op' => \Carbon\Carbon::parse('2023-10-24 09:00:12')
                        ],
                        (object)[
                            'log_id' => 10281,
                            'gebruiker' => (object)['naam' => 'Mark Rutten', 'gebruiker_id' => 29],
                            'actie' => 'betaling_geregistreerd',
                            'details' => ['batch' => 'Q3_2023', 'status' => 'initiated'],
                            'aangemaakt_op' => \Carbon\Carbon::parse('2023-10-23 17:45:30')
                        ],
                        (object)[
                            'log_id' => 10280,
                            'gebruiker' => (object)['naam' => 'Admin User', 'gebruiker_id' => 1],
                            'actie' => 'wachtwoord_gewijzigd',
                            'details' => ['setting' => 'reporting_frequency', 'old' => 'daily'],
                            'aangemaakt_op' => \Carbon\Carbon::parse('2023-10-23 11:10:05')
                        ]
                    ];
                }
            @endphp

            <!-- DEMO MODE NOTICE (Renders only when showing fallback mock logs) -->
            @if($showMockData)
                <!-- Alert box to inform developer/admin about demo mode fallback -->
                <div class="bg-blue-50 border border-blue-200/80 rounded-2xl p-4 flex items-start gap-3 shadow-sm">
                    <span class="text-blue-600 mt-0.5"><i class="fa-solid fa-circle-info text-base"></i></span>
                    <div>
                        <p class="text-sm font-semibold text-blue-900">Demo modus actief</p>
                        <p class="text-xs text-blue-700/90 mt-0.5">Er zijn momenteel geen activiteitenlogs geregistreerd in de database. De onderstaande tabel toont de representatieve ontwerp-mockup data van het administratie paneel.</p>
                    </div>
                </div>
            @endif

            <!-- ==========================================================================
                 HEADER SECTION: Page title and context description
                 ========================================================================== -->
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <!-- Breadcrumb navigation navigation links -->
                    <nav class="flex items-center gap-2 text-xs text-slate-400 mb-2 font-medium">
                        <a href="{{ route('MainDashboardPagina') }}" class="hover:text-blue-600 transition-colors">Dashboard</a>
                        <i class="fa-solid fa-chevron-right text-[8px]"></i>
                        <span class="text-slate-600">Activiteitenlog</span>
                    </nav>
                    <!-- Main Title and Subheading -->
                    <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">Activiteit Log</h1>
                    <p class="text-sm text-slate-500 font-medium mt-1">Monitor alle acties, systeemmeldingen en beveiligingsgebeurtenissen binnen het platform.</p>
                </div>
            </div>

            <!-- ==========================================================================
                 KPI SUMMARY CARDS GRID
                 ========================================================================== -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                
                <!-- CARD: Totaal Activiteiten -->
                <div class="bg-white rounded-2xl border border-slate-100 shadow-[0_2px_12px_-3px_rgba(0,0,0,0.04)] p-6 transition-all duration-200 hover:-translate-y-0.5 hover:shadow-[0_8px_20px_-6px_rgba(0,0,0,0.08)] flex items-start justify-between">
                    <div class="space-y-3">
                        <!-- Tiny descriptive category uppercase tag -->
                        <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider block">Totaal Activiteiten</span>
                        <div class="space-y-1">
                            <!-- Large numeric output showcasing total log rows count -->
                            <h3 class="text-3xl font-extrabold text-slate-950 tracking-tight">
                                {{ number_format($showMockData ? 1284 : $totalCount) }}
                            </h3>
                            <!-- Positive green growth index indicators -->
                            <span class="inline-flex items-center gap-1 text-xs text-emerald-600 font-semibold bg-emerald-50 px-2 py-0.5 rounded-md">
                                <i class="fa-solid fa-arrow-trend-up"></i>
                                {{ $weeklyTrend }}
                            </span>
                        </div>
                    </div>
                    <!-- Icon container mimicking standard KPI styling with mini-chart vector -->
                    <div class="w-12 h-12 rounded-xl bg-slate-50 text-slate-600 border border-slate-150 flex items-center justify-center shadow-inner hover:bg-slate-100 transition-colors duration-150 cursor-pointer" title="Bekijk rapporten">
                        <i class="fa-solid fa-chart-simple text-lg"></i>
                    </div>
                </div>
            </div>

            <!-- ==========================================================================
                 FILTER & SEARCH BAR CONTROL SECTION
                 ========================================================================== -->
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                
                <!-- SEARCH INPUT CONTROL FORM -->
                <form method="GET" action="{{ route('ActiviteitLog') }}" class="w-full lg:w-96 flex items-center">
                    <!-- Maintain tab status in hidden input across search events -->
                    <input type="hidden" name="tab" value="{{ $tab }}">
                    
                    <div class="relative w-full">
                        <!-- Centered magnifying glass search icon vector -->
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-slate-400">
                            <i class="fa-solid fa-magnifying-glass text-sm"></i>
                        </span>
                        <!-- Clean focus-scaling input design -->
                        <input type="text" name="search" value="{{ $search }}" placeholder="Zoek op gebruiker of details..." class="w-full bg-white border border-slate-200 text-slate-700 placeholder-slate-400 rounded-xl pl-10 pr-4 py-2.5 text-sm focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 transition-all shadow-sm">
                    </div>
                </form>

                <!-- TABS CONTAINER SYSTEM: Dynamic active design indicator -->
                <div class="flex items-center gap-1 bg-slate-200/50 p-1 rounded-2xl border border-slate-200/20 self-start lg:self-auto">
                    @foreach(['Alle', 'Inloggen', 'Leden', 'Betaling', 'Systeem'] as $t)
                        @php
                            $isActive = ($tab === $t);
                        @endphp
                        <!-- Dynamic active tab button wrapper with micro-shadowing -->
                        <a href="{{ route('ActiviteitLog', ['tab' => $t, 'search' => $search]) }}" class="px-4 py-2 rounded-xl text-xs font-semibold tracking-wide transition-all duration-200 {{ $isActive ? 'bg-white text-slate-900 shadow-sm border border-slate-200/40' : 'text-slate-500 hover:text-slate-800 hover:bg-slate-200/30' }}">
                            {{ $t }}
                        </a>
                    @endforeach
                </div>
            </div>

            <!-- ==========================================================================
                 LOGS DATA PRESENTATION TABLE CARD
                 ========================================================================== -->
            <div class="bg-white rounded-2xl border border-slate-100 shadow-[0_2px_12px_-3px_rgba(0,0,0,0.04)] overflow-hidden">
                
                <!-- Table View container -->
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead>
                            <!-- Header Rows -->
                            <tr class="bg-slate-50/70 border-b border-slate-100 text-slate-400 text-xs font-semibold uppercase tracking-wider">
                                <th class="px-6 py-4 text-center w-24">Log ID</th>
                                <th class="px-6 py-4 w-64">Gebruiker</th>
                                <th class="px-6 py-4 w-40">Actie</th>
                                <th class="px-6 py-4">Details</th>
                                <th class="px-6 py-4 w-48">Tijdstip</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-slate-600">
                            @forelse($displayLogs as $act)
                                <!-- Row Item with smooth hover states -->
                                <tr class="hover:bg-slate-50/50 transition-colors duration-150">
                                    
                                    <!-- LOG ID Column with Monospace layout -->
                                    <td class="px-6 py-4 text-center font-mono text-slate-400 text-xs font-semibold">
                                        #{{ $act->log_id }}
                                    </td>
                                    
                                    <!-- GEBRUIKER Column: Photo/Initials Bubble + Identity Sub-texting -->
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            @if($act->gebruiker && $act->gebruiker->naam === 'Systeem')
                                                <!-- Custom Cog layout for system automated activities -->
                                                <div class="w-9 h-9 rounded-full bg-slate-100 border border-slate-200 text-slate-500 flex items-center justify-center shadow-inner">
                                                    <i class="fa-solid fa-gear text-xs"></i>
                                                </div>
                                            @else
                                                <!-- Dynamic avatar with gradient based on user initials to match image mockup -->
                                                @php
                                                    $initials = '';
                                                    if ($act->gebruiker) {
                                                        $parts = explode(' ', $act->gebruiker->naam);
                                                        $initials = strtoupper(substr($parts[0], 0, 1) . (isset($parts[1]) ? substr($parts[1], 0, 1) : ''));
                                                    } else {
                                                        $initials = 'U';
                                                    }
                                                    
                                                    // Map specific initials to custom visual gradients
                                                    $colors = [
                                                        'PH' => 'from-blue-400 to-indigo-500', // Pieter Heijn
                                                        'SV' => 'from-emerald-400 to-teal-500', // Sarah de Vries
                                                        'MR' => 'from-fuchsia-400 to-purple-500', // Mark Rutten
                                                        'AU' => 'from-rose-400 to-pink-500' // Admin User
                                                    ];
                                                    $gradient = $colors[$initials] ?? 'from-slate-400 to-slate-500';
                                                @endphp
                                                <div class="w-9 h-9 rounded-full bg-gradient-to-tr {{ $gradient }} text-white text-xs font-bold flex items-center justify-center shadow-inner border border-white/20 shrink-0">
                                                    {{ $initials }}
                                                </div>
                                            @endif
                                            
                                            <!-- User Name + User Database ID labels -->
                                            <div class="flex flex-col">
                                                <span class="font-bold text-slate-800 leading-tight">
                                                    {{ $act->gebruiker ? $act->gebruiker->naam : 'Onbekende Gebruiker' }}
                                                </span>
                                                <span class="text-[10px] text-slate-400 font-bold tracking-wider uppercase mt-0.5">
                                                    {{ $act->gebruiker ? $act->gebruiker->rollen->pluck('naam')->join(', ') : 'Geen rol' }}
                                                </span> 
                                            </div>
                                        </div>
                                    </td>
                                    
                                    <!-- ACTIE Column: Categorized Dynamic Colored Badge Pills -->
                                    <td class="px-6 py-4">
                                       @php
                                        $actStr = strtolower($act->actie);

                                        // login actie
                                        if ($actStr === 'ingelogd') {
                                            $badgeLabel = 'Login';
                                            $badgeStyle = 'bg-blue-50 text-blue-700 ring-1 ring-blue-600/15';

                                        // logout actie
                                        } elseif ($actStr === 'uitgelogd') {
                                            $badgeLabel = 'Logout';
                                            $badgeStyle = 'bg-slate-100 text-slate-700 ring-1 ring-slate-600/15';

                                        // lid aangemaakt
                                        } elseif ($actStr === 'lid_aangemaakt') {

                                        // check of het een gebruiker is
                                        $isUser = false;

                                            if (is_array($act->details)) {
                                                $text = $act->details['details'] ?? '';

                                            // check tekst bevat "nieuwe gebruiker"
                                                if (str_contains(strtolower($text), 'nieuwe gebruiker')) {
                                                    $isUser = true;
                                                }
                                            }

                                            // label kiezen
                                            $badgeLabel = $isUser ? 'Gebruiker aangemaakt' : 'Lid toegevoegd';
                                            $badgeStyle = 'bg-emerald-50 text-emerald-700 ring-1 ring-emerald-600/15';

                                        // lid aangepast
                                        } elseif ($actStr === 'lid_bijgewerkt') {
                                            $badgeLabel = 'Lid gewijzigd';
                                            $badgeStyle = 'bg-teal-50 text-teal-700 ring-1 ring-teal-600/15';

                                        // lid verwijderd
                                        } elseif ($actStr === 'lid_verwijderd') {
                                            $badgeLabel = 'Lid verwijderd';
                                            $badgeStyle = 'bg-rose-50 text-rose-700 ring-1 ring-rose-600/15';

                                        // betaling gemaakt
                                        } elseif ($actStr === 'betaling_geregistreerd') {
                                            $badgeLabel = 'Betaling geregistreerd';
                                            $badgeStyle = 'bg-purple-50 text-purple-700 ring-1 ring-purple-600/15';

                                        // betaling goedgekeurd
                                        } elseif ($actStr === 'betaling_goedgekeurd') {
                                            $badgeLabel = 'Betaling goedgekeurd';
                                            $badgeStyle = 'bg-indigo-50 text-indigo-700 ring-1 ring-indigo-600/15';

                                        // betaling afgekeurd
                                        } elseif ($actStr === 'betaling_afgewezen') {
                                            $badgeLabel = 'Betaling afgewezen';
                                            $badgeStyle = 'bg-amber-50 text-amber-700 ring-1 ring-amber-600/15';

                                        // wachtwoord veranderd
                                        } elseif ($actStr === 'wachtwoord_gewijzigd') {
                                            $badgeLabel = 'Wachtwoord gewijzigd';
                                            $badgeStyle = 'bg-orange-50 text-orange-700 ring-1 ring-orange-600/15';

                                        // bon gemaakt of gedownload
                                        } elseif (in_array($actStr, ['bon_gedownload', 'bon_aangemaakt'])) {
                                            $badgeLabel = 'Rapport gegenereerd';
                                            $badgeStyle = 'bg-sky-50 text-sky-700 ring-1 ring-sky-600/15';

                                        // betaling verwijderd
                                        } elseif ($actStr === 'betaling_verwijderd') {
                                            $badgeLabel = 'Betaling verwijderd';
                                            $badgeStyle = 'bg-amber-50 text-amber-700 ring-1 ring-amber-600/15';
                                        

                                            //Betaling hersteld
                                        }elseif (in_array($actStr, ['betaling_hersteld'])) {
                                            $badgeLabel = 'Betaling hersteld';
                                            $badgeStyle = 'bg-green-50 text-green-700 ring-1 ring-green-600/15';
                                        
                                        // Upload actie
                                        }elseif ($actStr === 'bewijs_geüpload') {
                                            $badgeLabel = 'Bewijs geüpload';
                                            $badgeStyle = 'bg-teal-50 text-teal-700 ring-1 ring-teal-600/15';
                                        
                                        // gebruiker acties
                                        }elseif (in_array($actStr, ['gebruiker_toegevoegd', 'gebruiker_verwijderd', 'gebruiker_gewijzigd'])) {
                                            $badgeLabel = 'Gebruiker gewijzigd';
                                            $badgeStyle = 'bg-emerald-50 text-emerald-700 ring-1 ring-emerald-600/15';
                                        
                                        // fallback (alles wat niet past)
                                        } else {
                                            $badgeLabel = 'Systeem';
                                            $badgeStyle = 'bg-slate-50 text-slate-600 ring-1 ring-slate-500/10';
                                        }
                                        @endphp
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold tracking-wider {{ $badgeStyle }}">
                                            {{ $badgeLabel }}
                                        </span>
                                    </td>
                                    
                                    <!-- DETAILS Column: Formats JSON blocks and wraps text strings -->
                                    <td class="px-6 py-4 font-medium text-slate-600">
                                        @if(is_array($act->details))
                                            @php
                                                // Extract main text message if present in the array
                                                $textMsg = $act->details['details'] ?? $act->details['message'] ?? null;
                                                $metadata = $act->details;
                                                if ($textMsg) {
                                                    unset($metadata['details']);
                                                    unset($metadata['message']);
                                                }
                                            @endphp
                                            
                                            @if($textMsg)
                                                <div class="text-slate-800 font-semibold text-sm leading-snug">
                                                    {{ $textMsg }}
                                                </div>
                                            @endif
                                            
                                            @if(count($metadata) > 0)
                                                <!-- Render remaining metadata JSON configurations in monospace dark bubbles -->
                                                <code class="text-[11px] font-mono text-slate-500 bg-slate-50 border border-slate-100 rounded-lg px-2 py-1 select-all break-all shadow-inner inline-block max-w-lg mt-1.5">
                                                    {
                                                    @foreach($metadata as $k => $v)
                                                        "{{ $k }}": "{{ is_array($v) ? json_encode($v) : $v }}"{{ !$loop->last ? ',' : '' }}
                                                    @endforeach
                                                    }
                                                </code>
                                            @endif
                                        @else
                                            <!-- Render normal strings standard quoting -->
                                            <span class="text-slate-800 text-sm font-semibold leading-snug">
                                                {{ $act->details }}
                                            </span>
                                        @endif
                                    </td>
                                    
                                    <!-- TIJDSTIP Column: Clock Icon prefix + formatted timestamp -->
                                    <td class="px-6 py-4 text-slate-400 text-xs font-medium">
                                        <i class="fa-regular fa-clock mr-1 text-slate-300 text-sm"></i>
                                        {{ $act->aangemaakt_op ? $act->aangemaakt_op->format('Y-m-d') : 'Nvt' }}
                                    </td>
                                </tr>
                            @empty
                                <!-- EMPTY SCREEN FEEDBACK: Renders when searches yield no output -->
                                <tr>
                                    <td colspan="5" class="px-6 py-12 text-center text-slate-400">
                                        <div class="flex flex-col items-center justify-center gap-3">
                                            <i class="fa-solid fa-clipboard-question text-4xl text-slate-300"></i>
                                            <span class="font-bold text-slate-500">Geen activiteiten gevonden</span>
                                            <span class="text-xs text-slate-400">Pas uw zoekopdracht of filters aan om logs te filteren.</span>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- ==========================================================================
                     TABLE FOOTER & PAGINATION CONTROL LAYER
                     ========================================================================== -->
                @if(!$showMockData)
                    <div class="px-6 py-5 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-4">
                        
                        <!-- Page Index Count indicator -->
                        <span class="text-xs text-slate-400 font-semibold tracking-wide uppercase">
                            Toont {{ $activiteiten->firstItem() ?? 0 }}-{{ $activiteiten->lastItem() ?? 0 }} van {{ $activiteiten->total() }} resultaten
                        </span>
                        
                        <!-- Beautiful Paginated Link controllers mapped directly to backend parameters -->
                        <div class="flex items-center gap-1">
                            <!-- Left arrow control link -->
                            @if($activiteiten->onFirstPage())
                                <span class="w-8 h-8 rounded-lg bg-slate-50 border border-slate-200/60 text-slate-300 flex items-center justify-center text-xs cursor-not-allowed"><i class="fa-solid fa-chevron-left"></i></span>
                            @else
                                <a href="{{ $activiteiten->previousPageUrl() }}" class="w-8 h-8 rounded-lg bg-white border border-slate-200 text-slate-600 hover:bg-slate-50 hover:text-slate-900 flex items-center justify-center text-xs shadow-sm transition-all"><i class="fa-solid fa-chevron-left"></i></a>
                            @endif

                            <!-- Direct Page items -->
                            @foreach($activiteiten->getUrlRange(max(1, $activiteiten->currentPage() - 1), min($activiteiten->lastPage(), $activiteiten->currentPage() + 1)) as $page => $url)
                                @if($page == $activiteiten->currentPage())
                                    <span class="w-8 h-8 rounded-lg bg-slate-950 border border-slate-950 text-white flex items-center justify-center text-xs font-bold shadow-sm shadow-slate-900/10">{{ $page }}</span>
                                @else
                                    <a href="{{ $url }}" class="w-8 h-8 rounded-lg bg-white border border-slate-200 text-slate-600 hover:bg-slate-50 hover:text-slate-900 flex items-center justify-center text-xs font-semibold shadow-sm transition-all">{{ $page }}</a>
                                @endif
                            @endforeach

                            <!-- Right arrow control link -->
                            @if($activiteiten->hasMorePages())
                                <a href="{{ $activiteiten->nextPageUrl() }}" class="w-8 h-8 rounded-lg bg-white border border-slate-200 text-slate-600 hover:bg-slate-50 hover:text-slate-900 flex items-center justify-center text-xs shadow-sm transition-all"><i class="fa-solid fa-chevron-right"></i></a>
                            @else
                                <span class="w-8 h-8 rounded-lg bg-slate-50 border border-slate-200/60 text-slate-300 flex items-center justify-center text-xs cursor-not-allowed"><i class="fa-solid fa-chevron-right"></i></span>
                            @endif
                        </div>
                    </div>
                @else
                    <!-- STATIC MOCK PAGINATION DESIGN (Matches the mockup 100% when DB yields empty states) -->
                    <div class="px-6 py-5 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-4">
                        <span class="text-xs text-slate-400 font-semibold tracking-wide uppercase">
                            Toont 1-5 van 1,284 resultaten
                        </span>
                        <div class="flex items-center gap-1">
                            <span class="w-8 h-8 rounded-lg bg-slate-50 border border-slate-200/60 text-slate-300 flex items-center justify-center text-xs cursor-not-allowed"><i class="fa-solid fa-chevron-left"></i></span>
                            <span class="w-8 h-8 rounded-lg bg-slate-950 border border-slate-950 text-white flex items-center justify-center text-xs font-bold shadow-sm shadow-slate-900/10">1</span>
                            <span class="w-8 h-8 rounded-lg bg-white border border-slate-200 text-slate-600 hover:bg-slate-50 hover:text-slate-900 flex items-center justify-center text-xs font-semibold shadow-sm cursor-pointer">2</span>
                            <span class="w-8 h-8 rounded-lg bg-white border border-slate-200 text-slate-600 hover:bg-slate-50 hover:text-slate-900 flex items-center justify-center text-xs font-semibold shadow-sm cursor-pointer">3</span>
                            <span class="w-8 h-8 rounded-lg bg-white border border-slate-200 text-slate-600 hover:bg-slate-50 hover:text-slate-900 flex items-center justify-center text-xs shadow-sm cursor-pointer"><i class="fa-solid fa-chevron-right"></i></span>
                        </div>
                    </div>
                @endif
            </div>
        </main>
    </div>
</div>

</body>
</html>