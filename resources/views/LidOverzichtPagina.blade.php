<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="Lid bewerken - Bekijk lid gegevens">
    <title>Lid Overzicht | Administratie Panel</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/css/sidebar.css', 'resources/css/editpagina.css', 'resources/js/app.js'])
</head>
<body>
    @include('layouts.sidebar')

    <div class="main-content">
        @include('layouts.header')

        <!-- Edit Page Content -->
        <section class="edit-page-content">

            <!-- Back Button -->
            <a href="{{ route('ledenpagina') }}" class="btn-back">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M19 12H5M5 12L12 19M5 12L12 5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                Terug naar overzicht
            </a>

            <!-- Top Row: Profile, Info, Actions -->
            <div class="edit-top-row">

                <!-- Profile Card -->
                <div class="edit-card profile-card">
                    <div class="profile-avatar-wrapper">
                        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M4 16L8.586 11.414C8.96106 11.0391 9.46967 10.8284 10 10.8284C10.5303 10.8284 11.0389 11.0391 11.414 11.414L16 16M14 14L15.586 12.414C15.9611 12.0391 16.4697 11.8284 17 11.8284C17.5303 11.8284 18.0389 12.0391 18.414 12.414L20 14M14 8H14.01M6 20H18C18.5304 20 19.0391 19.7893 19.4142 19.4142C19.7893 19.0391 20 18.5304 20 18V6C20 5.46957 19.7893 4.96086 19.4142 4.58579C19.0391 4.21071 18.5304 4 18 4H6C5.46957 4 4.96086 4.21071 4.58579 4.58579C4.21071 4.96086 4 5.46957 4 6V18C4 18.5304 4.21071 19.0391 4.58579 19.4142C4.96086 19.7893 5.46957 20 6 20Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>
                    <h2 class="profile-name">{{ $lid->naam }}</h2>
                </div>

                <!-- Information Card -->
                <div class="edit-card">
                    <h3 class="info-card-title">Informatie:</h3>
                    <ul class="info-list">
                        <li>
                            <span class="info-label">Email:</span>
                            <span class="info-value">{{ $lid->email ?? '—' }}</span>
                        </li>
                        <li>
                            <span class="info-label">Leeftijd:</span>
                            <span class="info-value">
                                @if($lid->geboortedatum)
                                    {{ \Carbon\Carbon::parse($lid->geboortedatum)->age }} jaar
                                @else
                                    —
                                @endif
                            </span>
                        </li>
                        <li>
                            <span class="info-label">Geboort datum:</span>
                            <span class="info-value">
                                @if($lid->geboortedatum)
                                    {{ \Carbon\Carbon::parse($lid->geboortedatum)->format('d-m-Y') }}
                                @else
                                    —
                                @endif
                            </span>
                        </li>
                        <li>
                            <span class="info-label">Lid ID:</span>
                            <span class="info-value">{{ Str::limit($lid->lid_id, 8, '…') }}</span>
                        </li>
                    </ul>
                </div>

                <!-- Action Card -->
                <div class="edit-card">
                    <h3 class="action-card-title">Action:</h3>
                    <div class="action-buttons">

                        <!-- edit button -->
                        <a href="{{ route('ledenpagina.edit', $lid->lid_id) }}" type="button" class="btn-update" id="btnUpdate">Update</a>

                        <!-- delete button -->
                        <form action="{{ route('ledenpagina.delete', $lid->lid_id) }}" method="POST" id="deleteForm">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn-delete" onclick="return confirm('Weet u zeker dat u dit lid wilt verwijderen?')">Verwijder</button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Bottom Row: Contact + Betaling History -->
            <div class="edit-bottom-row">

                <!-- Contact Card -->
                <div class="edit-card">
                    <h3 class="contact-card-title">Contact:</h3>
                    <ul class="contact-list">
                        <li>
                            <strong>Adres:</strong>
                            {{ $lid->adres ?? '—' }}
                        </li>
                        <li>
                            <strong>Woonplaats:</strong>
                            {{ $lid->woonplaats ?? '—' }}
                        </li>
                        <li>
                            <strong>Telefoon:</strong>
                            {{ $lid->telefoonnummer ?? '—' }}
                        </li>
                    </ul>
                </div>

                <!-- Betaling History Card -->
                <div class="edit-card">
                    <div class="history-header">
                        <h3 class="history-title">Geschiedenis Betaling</h3>
                        <button class="btn-history-filter" title="Filter">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M3 6H21M6 12H18M10 18H14" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </button>
                    </div>

                    <div class="history-table-wrapper">
                        <table class="history-table">
                            <thead>
                                <tr>
                                    <th>Betaling_ID</th>
                                    <th>Datum</th>
                                    <th>Contributiebedrag</th>
                                    <th>Betaling</th>
                                    <th>Status</th>
                                    <th>Bonnummer</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($betalingen as $betaling)
                                    <tr>
                                        <td>
                                            <span class="betaling-id-link">{{ $loop->iteration }}</span>
                                        </td>
                                        <td>{{ \Carbon\Carbon::parse($betaling->betalingsdatum)->format('j F Y') }}</td>
                                        <td>
                                            <span class="contributiebedrag-text">Srd {{ number_format($betaling->bedrag, 0) }}</span>
                                        </td>
                                        <td>
                                            <span class="amount-text">SRD {{ number_format($betaling->bedrag, 0) }}</span>
                                        </td>
                                        <td>
                                            @php
                                                $statusClass = match($betaling->status) {
                                                    'Betaald' => 'betaald',
                                                    'Niet Betaald' => 'niet-betaald',
                                                    default => 'afwachting',
                                                };
                                            @endphp
                                            <span class="status-badge {{ $statusClass }}">{{ $betaling->status }}</span>
                                        </td>
                                        <td>
                                            @if($betaling->bewijs_bestand)
                                                <span class="bonnummer-badge">BON-{{ strtoupper(substr($betaling->betaling_id, 0, 4)) }}-{{ $betaling->jaar }}</span>
                                            @else
                                                —
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" style="text-align: center; padding: 2rem; color: #94a3b8;">
                                            Geen betalingen gevonden voor dit lid.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if($betalingen->hasPages())
                        <div class="history-pagination">
                            @foreach($betalingen->links()->elements[0] as $page => $url)
                                <a href="{{ $url }}" class="history-page-link {{ $betalingen->currentPage() == $page ? 'active' : '' }}">{{ $page }}</a>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

        </section>
    </div>

    @vite('resources/js/EditPagina.js')
</body>
</html>
