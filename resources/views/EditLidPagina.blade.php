<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="Lid bewerken - Wijzig lid gegevens">
    <title>Lid Bewerken | Administratie Panel</title>
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

            <!-- Edit Form Card -->
            <div class="edit-card">
                <h2 class="info-card-title">Lid bewerken</h2>

                <form action="{{ route('ledenpagina.update', $lid->lid_id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="edit-form-grid">
                        <div class="form-group">
                            <label for="naam">Naam</label>
                            <input type="text" name="naam" id="naam" value="{{ old('naam', $lid->naam) }}" required>
                        </div>
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" name="email" id="email" value="{{ old('email', $lid->email) }}" required>
                        </div>
                        <div class="form-group">
                            <label for="telefoonnummer">Telefoonnummer</label>
                            <input type="text" name="telefoonnummer" id="telefoonnummer" value="{{ old('telefoonnummer', $lid->telefoonnummer) }}" required>
                        </div>
                        <div class="form-group">
                            <label for="adres">Adres</label>
                            <input type="text" name="adres" id="adres" value="{{ old('adres', $lid->adres) }}" required>
                        </div>
                        <div class="form-group">
                            <label for="woonplaats">Woonplaats</label>
                            <input type="text" name="woonplaats" id="woonplaats" value="{{ old('woonplaats', $lid->woonplaats) }}" required>
                        </div>
                        <div class="form-group">
                            <label for="geboortedatum">Geboortedatum</label>
                            <input type="date" name="geboortedatum" id="geboortedatum" value="{{ old('geboortedatum', $lid->geboortedatum) }}" required>
                        </div>
                    </div>

                    @if($errors->any())
                        <div class="form-errors">
                            @foreach($errors->all() as $error)
                                <p>{{ $error }}</p>
                            @endforeach
                        </div>
                    @endif

                    <div class="form-actions">
                        <a href="{{ route('ledenpagina.show', $lid->lid_id) }}" class="btn-cancel">Annuleren</a>
                        <button type="submit" class="btn-update">Opslaan</button>
                    </div>
                </form>
            </div>

        </section>
    </div>
</body>
</html>
