<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="Leden overzicht - Beheer alle geregistreerde leden in het administratie systeem">
    <title>Leden Overzicht | Administratie Panel</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    @vite(['resources/css/app.css', 'resources/css/sidebar.css', 'resources/css/ledenpagina-new.css', 'resources/css/Toevoegen.css', 'resources/js/app.js', 'resources/js/FormValidator.js', 'resources/js/Modal/AddLidModal.js'])
</head>
<body>
    <div class="flex min-h-screen">
        @include('layouts.Sidebars.sidebar')

        <div class="flex-1 ml-0 md:ml-64 transition-all duration-300 min-w-0 overflow-x-hidden">
            @include('layouts.Header-layout.AddLid-Header')
            <!-- Main Leden Overzicht Table with all details -->
            <main class="lp-page-content">
                <div class="lp-page-header">
                    <div class="lp-page-header-left">
                        <h1 class="lp-page-title">Leden</h1>
                        <p class="lp-page-subtitle">Beheer en bekijk alle geregistreerde leden van de organisatie.</p>
                    </div>
                    <div class="lp-page-header-right">
                        <form method="GET" action="{{ route('ledenpagina') }}" class="lp-filter-form">
                            <div class="lp-btn-filters" id="filterBtn">
                                <i class="fa-solid fa-sliders"></i>
                                <select name="woonplaats" id="filter" onchange="this.form.submit()">
                                    <option value="">Filters</option>
                                    @foreach ($woonplaatsen as $woonplaats)
                                        <option value="{{ $woonplaats }}"
                                            {{ request('woonplaats') == $woonplaats ? 'selected' : '' }}>
                                            {{ $woonplaats }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </form>
                    </div>
                </div>
 
                <div class="lp-info-banner">
                    <div class="lp-info-icon">
                        <i class="fa-solid fa-circle-info"></i>
                    </div>
                    <span>Er zijn momenteel <strong>{{ number_format($totaalLeden, 0, ',', '.') }}</strong> actieve leden geregistreerd in het systeem.</span>
                </div>

                @include('layouts.Totalleden-Charts')

                
                  <!--- Leden Table with all details -->
                <div class="lp-table-section">
                    <div class="lp-table-wrapper">
                        <table class="lp-data-table" id="ledenTable">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>NAAM</th>
                                    <th>TELEFOON</th>
                                    <th>EMAIL</th>
                                    <th>ADRES</th>
                                    <th>ACTIE</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($leden as $lid)
                                    <tr class="lp-table-row">
                                        <td>
                                            <span class="lp-id-badge">{{ $lid->lid_id }}</span>
                                        </td>
                                        <td>
                                            <div class="lp-member-cell">
                                                <div class="lp-member-avatar">
                                                    {{ strtoupper(substr($lid->naam, 0, 1)) }}{{ strtoupper(substr(explode(' ', $lid->naam)[1] ?? '', 0, 1)) }}
                                                </div>
                                                <span class="lp-member-name">{{ $lid->naam }}</span>
                                            </div>
                                        </td>
                                        <td>{{ $lid->telefoonnummer }}</td>
                                        <td class="lp-email">{{ $lid->email }}</td>
                                        <td>{{ $lid->adres }}{{ $lid->woonplaats ? ', ' . $lid->woonplaats : '' }}</td>
                                        <td>
                                            <div class="dropdown-container">
                                                <button class="btn-more" title="Meer opties">
                                                    <i class="fa-solid fa-ellipsis-vertical"></i>
                                                </button>
                                                <ul class="dropdown-menu hidden">
                                                    <li><a href="{{ route('ledenpagina.show', $lid->lid_id) }}">
                                                        <i class="fa-regular fa-eye"></i>
                                                        Bekijken
                                                    </a></li>
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="lp-empty-state">
                                            <div class="lp-empty-content">
                                                <i class="fa-regular fa-users" style="font-size: 48px; color: #94a3b8;"></i>
                                                <p>Geen leden gevonden</p>
                                                <span>Voeg een nieuw lid toe om te beginnen</span>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>


                           <!-- Pagination --> 
                    @if($leden->hasPages())
                    <div class="lp-table-footer">
                        <div class="lp-pagination-info">
                            Weergeven van {{ $leden->firstItem() }} tot {{ $leden->lastItem() }} van {{ $leden->total() }} leden
                        </div>
                        <div class="lp-pagination">
                            @if($leden->onFirstPage())
                                <span class="lp-page-nav disabled"><i class="fa-solid fa-chevron-left"></i></span>
                            @else
                                <a href="{{ $leden->previousPageUrl() }}" class="lp-page-nav"><i class="fa-solid fa-chevron-left"></i></a>
                            @endif

                            @php
                                $currentPage = $leden->currentPage();
                                $lastPage = $leden->lastPage();
                            @endphp

                            @for($i = 1; $i <= min(3, $lastPage); $i++)
                                @if($i == $currentPage)
                                    <span class="lp-page-link active">{{ $i }}</span>
                                @else
                                    <a href="{{ $leden->url($i) }}" class="lp-page-link">{{ $i }}</a>
                                @endif
                            @endfor

                            @if($lastPage > 3)
                                <span class="lp-page-dots">...</span>
                                @if($currentPage == $lastPage)
                                    <span class="lp-page-link active">{{ $lastPage }}</span>
                                @else
                                    <a href="{{ $leden->url($lastPage) }}" class="lp-page-link">{{ $lastPage }}</a>
                                @endif
                            @endif

                            @if($leden->hasMorePages())
                                <a href="{{ $leden->nextPageUrl() }}" class="lp-page-nav"><i class="fa-solid fa-chevron-right"></i></a>
                            @else
                                <span class="lp-page-nav disabled"><i class="fa-solid fa-chevron-right"></i></span>
                            @endif
                        </div>
                    </div>
                    @endif
                </div> 

                <!-- Stats: Total members, active members -->
                <div class="lp-stats-row">
                    <div class="lp-stat-card lp-stat-card-light">
                        <div class="lp-stat-card-header">
                            <span class="lp-stat-label">NIEUWE LEDEN</span>
                            <div class="lp-stat-icon-light"><i class="fa-solid fa-user-plus"></i></div>
                        </div>
                        <div class="lp-stat-value-row">
                            <span class="lp-stat-number">{{ $totaalLeden }}</span>
                            <span class="lp-stat-trend lp-trend-up">↗+12%</span>
                        </div>
                    </div>

                    <div class="lp-stat-card lp-stat-card-dark">
                        <div class="lp-stat-card-header">
                            <span class="lp-stat-label-dark">TOTAAL ACTIEF</span>
                        </div>
                        <div class="lp-stat-value-row-dark">
                            <span class="lp-stat-number-dark">{{ number_format($totaalLeden, 0, ',', '.') }}</span>
                        </div>
                        <div class="lp-stat-retention">
                            <i class="fa-solid fa-circle-check"></i>
                            <span>98% retentie dit jaar</span>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    @can('leden-beheren')
        @include('layouts.Add-Modal.add-lid-modal')
    @endcan

    <div class="toast-notification" id="successToast">
        <i class="fa-solid fa-circle-check" style="color: #10b981;"></i>
        <span>Lid succesvol toegevoegd!</span>
    </div>

    @vite(['resources/js/Button&More.js', 'resources/js/Charts/TotalLeden-Chart.js'])
</body>
</html>