<div class="leden_overzicht_container">

    <div class="leden_card">
        <div class="card_top">
            <div class="card_icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                     stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="8" r="4"/>
                    <path d="M4 20c0-4 3.6-7 8-7s8 3 8 7"/>
                </svg>
            </div>
            <span class="card_number">{{ $totaalLeden }}</span>
        </div>
        <div class="card_bottom">
            <h2 class="card_title">Totaal leden</h2>
            <p class="card_subtitle">Geregistreerde leden</p>
            <span class="card_trend">↑ Actief {{ date('Y') }}</span>
        </div>
    </div>

    <div class="diagram">
        @include('layouts.Totalleden-Charts')
    </div>

</div>