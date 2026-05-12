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
    @vite(['resources/css/app.css', 'resources/css/sidebar.css', 'resources/css/Toevoegen.css', 'resources/js/app.js', 'resources/js/AddBetalingModal.js'])
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
</head>
<body class="m-0 bg-[#f0f4f8] text-slate-700 font-['Inter',sans-serif] antialiased">
    @include('layouts.sidebar')

    <div class="main-content">
        @include('layouts.header') 

        {{-- Stats & Chart Row --}}
        <div class="flex flex-col lg:flex-row items-stretch gap-6 p-6 px-8 animate-[fadeSlideUp_0.4s_ease-out]">

            {{-- Total Income Card --}}
            <div class="relative bg-gradient-to-br from-[#1e3a8a] via-[#1d4ed8] to-[#2563eb] rounded-2xl p-6 flex flex-col justify-between min-h-[160px] w-full lg:max-w-[340px] lg:min-w-[260px] shrink-0 overflow-hidden shadow-[0_4px_16px_rgba(30,58,138,0.2),0_1px_3px_rgba(30,58,138,0.1)] transition-all duration-300 hover:-translate-y-1 hover:shadow-[0_12px_32px_rgba(30,58,138,0.3),0_4px_12px_rgba(30,58,138,0.15)]">
                {{-- Decorative circles --}}
                <div class="absolute -bottom-[50px] -right-[30px] w-[150px] h-[150px] rounded-full bg-white/[0.06] pointer-events-none"></div>
                <div class="absolute -top-[30px] right-[50px] w-[90px] h-[90px] rounded-full bg-white/[0.04] pointer-events-none"></div>

                <div class="flex justify-between items-center">
                    <div class="mt-5">
                        <p class="text-sm font-semibold text-white/90 mb-1">Total Income</p>
                        <p class="text-[2.5rem] font-extrabold text-white leading-none tracking-tight">Srd 34,323.30</p>
                        <p class="text-xs text-white/50 mt-1">elk maand</p>
                    </div>
                    <div class="w-10 h-10 bg-white/[0.12] rounded-xl flex items-center justify-center backdrop-blur-sm">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M12 1V23M17 5H9.5C8.57174 5 7.6815 5.36875 7.02513 6.02513C6.36875 6.6815 6 7.57174 6 8.5C6 9.42826 6.36875 10.3185 7.02513 10.9749C7.6815 11.6313 8.57174 12 9.5 12H14.5C15.4283 12 16.3185 12.3687 16.9749 13.0251C17.6313 13.6815 18 14.5717 18 15.5C18 16.4283 17.6313 17.3185 16.9749 17.9749C16.3185 18.6313 15.4283 19 14.5 19H6" stroke="rgba(255,255,255,0.9)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>
                </div>
            </div>

           <!-- TotalBetaling-Chart -->
           @include('layouts.BetalingLayout.BetalingChart')
        </div>
    
        <!--this table is for showing the payments-->
        @include('layouts.BetalingLayout.Table-Betaling')
    </div>

    <!-- Add Betaling Modal -->
    @include('layouts.Add-Modal.add-Betaling-modal')

    <!-- Success Toast -->
    <div class="toast-notification" id="betalingSuccessToast">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M9 12L11 14L15 10M21 12C21 16.9706 16.9706 21 12 21C7.02944 21 3 16.9706 3 12C3 7.02944 7.02944 3 12 3C16.9706 3 21 7.02944 21 12Z" stroke="#10b981" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
        <span>Betaling succesvol toegevoegd!</span>
    </div>

   <!-- TotalBetaling-Chart -->
   @vite('resources/js/TotalBetaling-chart.js')
</body>
</html>