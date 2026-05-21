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
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/css/sidebar.css', 'resources/css/totalleden.css', 'resources/css/Toevoegen.css', 'resources/js/app.js', 'resources/js/FormValidator.js', 'resources/js/AddLidModal.js', 'resources/js/TotalLeden-Chart.js'])
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    @include('layouts.sidebar')

    <div class="main-content">
        @include('layouts.header')

        {{-- Stats & Chart Row --}}
        @include('layouts.leden-overzicht')

        <!-- Main Table Section -->
        <main class="page-content">
            <div class="table-container">
                {{-- Table Header --}}
                <div class="table-header-top">
                    <div class="table-title-section">
                        <h2 class="table-title">Leden Overzicht</h2>
                        <p class="table-subtitle">Beheer alle geregistreerde leden in het systeem</p>
                    </div>
                    <div class="table-actions">
                        <div class="search-box" id="searchBox">
                            <svg class="search-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M21 21L16.65 16.65M19 11C19 15.4183 15.4183 19 11 19C6.58172 19 3 15.4183 3 11C3 6.58172 6.58172 3 11 3C15.4183 3 19 6.58172 19 11Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            <input type="text" id="searchInput" placeholder="Zoek op naam, email..." autocomplete="off">
                        </div>

                        <!-- Filter Dropdown -->
                        <form method="GET" action="{{ route('ledenpagina') }}" class="filter-form">
                            <div class="btn-filter" id="filterBtn" title="Filter opties">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M3 6H21M6 12H18M10 18H14" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                                <select name="woonplaats" id="filter" onchange="this.form.submit()">
                                    <option value="">Woonplaats</option>
                                    @foreach ($woonplaatsen as $woonplaats)
                                        <option value="{{ $woonplaats }}"
                                            {{ request('woonplaats') == $woonplaats ? 'selected' : '' }}>
                                            {{ $woonplaats }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </form>

                        <button class="btn-add" id="openModalBtn" type="button">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M12 5V19M5 12H19" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            Voeg lid toe
                        </button>
                    </div>
                </div>

                <!-- Table with responsive wrapper -->
                <div class="table-wrapper">
                    <table class="data-table" id="ledenTable">
                        <thead>
                            <tr>
                                <th>
                                    <div class="th-content">
                                        <span>ID</span>
                                    </div>
                                </th>
                                <th>
                                    <div class="th-content">
                                        <span>Naam</span>
                                    </div>
                                </th>
                                <th>
                                    <div class="th-content">
                                        <span>Telefoon</span>
                                    </div>
                                </th>
                                <th>
                                    <div class="th-content">
                                        <span>Email</span>
                                    </div>
                                </th>
                                <th>
                                    <div class="th-content">
                                        <span>Adres</span>
                                    </div>
                                </th>
                                <th class="th-actions">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            
                            <!--this foreach loop is for showing the members -->
                            @forelse($leden as $lid)
                                <tr class="table-row">
                                    <td>
                                        <span class="id-badge">{{ Str::limit($lid->lid_id, 8, '…') }}</span>
                                    </td>
                                    <td>
                                        <div class="member-name">
                                            <div class="member-avatar">{{ strtoupper(substr($lid->naam, 0, 1)) }}</div>
                                            <span class="font-semibold">{{ $lid->naam }}</span>
                                        </div>
                                    </td>
                                    <td>{{ $lid->telefoonnummer }}</td>
                                    <td>
                                        <span class="email-text">{{ $lid->email }}</span>
                                    </td>
                                    <td>{{ $lid->adres }}</td>

                                    <!-- More Button-->
                                    <td class="dropdown-container">
                                        <button class="btn-more" title="Meer opties">
                                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                                                <circle cx="12" cy="5" r="1.5" fill="currentColor"/>
                                                <circle cx="12" cy="12" r="1.5" fill="currentColor"/>
                                                <circle cx="12" cy="19" r="1.5" fill="currentColor"/>
                                            </svg>
                                        </button>

                                        <!-- Dropdown menu -->
                                        <ul class="dropdown-menu hidden absolute  shadow bg-white cursor-pointer rounded p-1">
                                            <li><a class="text-gray-800 hover:text-green-600" href="{{ route('ledenpagina.show', $lid->lid_id) }}">Bekijken</a></li>
                                        </ul>
                                    </td>



                            @empty
                            <!-- this is for showing the empty state when there are no members-->
                                <tr>
                                    <td colspan="6" class="empty-state">
                                        <div class="empty-state-content">
                                            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M17 21V19C17 17.9391 16.5786 16.9217 15.8284 16.1716C15.0783 15.4214 14.1609 15 13 15H5C3.93913 15 3.02174 15.4214 2.27157 16.1716C1.52143 16.9217 1 17.9391 1 19V21" stroke="#94a3b8" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                                <circle cx="9" cy="7" r="4" stroke="#94a3b8" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                                <path d="M23 21V19C22.9993 18.1137 22.7044 17.2528 22.1614 16.5523C21.6184 15.8519 20.8581 15.3516 20 15.13" stroke="#94a3b8" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                                <path d="M16 3.13C16.8604 3.35031 17.623 3.85071 18.1676 4.55232C18.7122 5.25393 19.0078 6.11683 19.0078 7.005C19.0078 7.89318 18.7122 8.75607 18.1676 9.45769C17.623 10.1593 16.8604 10.6597 16 10.88" stroke="#94a3b8" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                            </svg>
                                            <p>Geen leden gevonden</p>
                                            <span>Voeg een nieuw lid toe om te beginnen</span>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @include('layouts.Table footer')

            </div>
        </main>
    </div>

    <!-- Modal: Lid toevoegen -->
    @include('layouts.Add-Modal.add-lid-modal')

    {{-- Success Toast --}}
    <div class="toast-notification" id="successToast">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M9 12L11 14L15 10M21 12C21 16.9706 16.9706 21 12 21C7.02944 21 3 16.9706 3 12C3 7.02944 7.02944 3 12 3C16.9706 3 21 7.02944 21 12Z" stroke="#10b981" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
        <span>Lid succesvol toegevoegd!</span>
    </div>

    {{-- JS files loaded via Vite in <head> --}}
    @vite('resources/js/Button&More.js')
</body>
</html>