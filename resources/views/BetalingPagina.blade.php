<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="Betalingen overzicht - Beheer alle betalingen in het administratie systeem">
    <title>Betalingen | Administratie Panel</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/css/sidebar.css', 'resources/css/BetalingPagina.css', 'resources/js/UI/Sidebar.js', 'resources/js/AddModals/AddBetalingModal.js', 'resources/js/EditModals/EditBetalingModal.js'])
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="m-0 bg-[#f0f4f8] text-slate-700 font-['Inter',sans-serif] antialiased">
    <div class="flex min-h-screen">
        @include('Layouts.Sidebars.sidebar')

        <div class="flex-1 ml-0 md:ml-64 transition-all duration-300 min-w-0 overflow-x-hidden">
            @include('Layouts.Headers.header')

            <div class="bp-page-content">
                {{-- Stats & Chart Row --}}
                <div class="bp-stats-row">

                    {{-- Total Income Card --}}
                    <div class="bp-stat-card">
                        <div class="bp-stat-card-header">
                            <span class="bp-stat-label">Total Income</span>
                            <div class="bp-stat-icon">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M12 1V23M17 5H9.5C8.57174 5 7.6815 5.36875 7.02513 6.02513C6.36875 6.6815 6 7.57174 6 8.5C6 9.42826 6.36875 10.3185 7.02513 10.9749C7.6815 11.6313 8.57174 12 9.5 12H14.5C15.4283 12 16.3185 12.3687 16.9749 13.0251C17.6313 13.6815 18 14.5717 18 15.5C18 16.4283 17.6313 17.3185 16.9749 17.9749C16.3185 18.6313 15.4283 19 14.5 19H6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </div>
                        </div>
                        <div>
                            <div class="bp-stat-value">Srd {{number_format($maandTotaal, 2)}}</div>
                            <div class="bp-stat-meta">
                                {{ \Carbon\Carbon::createFromDate($jaar, $maand, 1)->translatedFormat('F Y') }}
                            </div>
                        </div>
                    </div>

                    @include('Layouts.Charts.BetalingChart')
                </div>
                 

                   <!--Tables-->
                    @include('Layouts.Shared.recente-transacties-tabel')
                    @include('Layouts.Shared.leden-betalingsstatus-tabel')
                   
              
            </div>
        </div>
    </div>

    @can('betalingen-beheren')
        @include('Layouts.AddModals.add-Betaling-modal')
        @include('Layouts.EditModals.edit-Betaling-modal')
    @endcan

    <div class="toast-notification" id="betalingSuccessToast">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M9 12L11 14L15 10M21 12C21 16.9706 16.9706 21 12 21C7.02944 21 3 16.9706 3 12C3 7.02944 7.02944 3 12 3C16.9706 3 21 7.02944 21 12Z" stroke="#10b981" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
        <span>Betaling succesvol toegevoegd!</span>
    </div>



    @vite('resources/js/Charts/TotalBetaling-chart.js')
    @vite('resources/js/Utils/OptieDisable.js')
    @vite('resources/js/UI/Button&More.js')

    @vite('resources/js/Pages/Herstel.js')

    


    
</body>
</html>