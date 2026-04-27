<div class="leden_overzicht_container">
    <div class="leden_card">
        <div class="card_left">
            <h2 class="card_title">Totaal Leden</h2>
            <p class="card_subtitle">Totaal leden in het systeem</p>
        </div>
        <div class="card_right">
            <span class="card_number">{{ $totaalLeden }}</span>
        </div>
    </div>
    
    <div class="diagram" style="flex: 1; max-width: 600px; margin-left: 20px;">
        @include('layouts.Totalleden-Charts')
    </div>




</div>
