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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
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
                @include('Layouts.Shared.flash-messages')

                <!-- Stats & Chart Row -->
                <div class="bp-stats-row">
                         
             
                    <!-- Total Income Card -->
                    <div class="bp-stat-card">
                        <div class="bp-stat-card-header">
                            <span class="bp-stat-label">Inkomsten deze maand</span>
                            <div class="bp-stat-icon">
                                <i class="fa-solid fa-sack-dollar"></i>
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
                    @include('Layouts.Tables.recente-transacties-tabel')
                    @include('Layouts.Tables.leden-betalingsstatus-tabel')


            </div>
        </div>
    </div>

    @can('betalingen-beheren')
        @include('Layouts.AddModals.add-Betaling-modal')
        @include('Layouts.EditModals.edit-Betaling-modal')
    @endcan

    <div class="toast-notification" id="betalingSuccessToast">
        <i class="fa-solid fa-circle-check" style="color: #10b981;"></i>
        <span>Betaling succesvol toegevoegd!</span>
    </div>

    @vite('resources/js/Charts/TotalBetaling-chart.js')
    @vite('resources/js/Utils/OptieDisable.js')
    @vite('resources/js/UI/Button&More.js')
    @vite('resources/js/Pages/Herstel.js')

</body>
</html>