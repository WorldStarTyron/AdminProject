<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lid Toevoegen</title>
    @vite(['resources/css/app.css', 'resources/css/sidebar.css','resources/css/totalleden.css', 'resources/js/app.js', 'resources/css/Toevoegen.css'])
</head>
<body>
    @include('layouts.sidebar')

    <div class="main-content">
        @include('layouts.header')
        
        <main class="page-content">
            <div class="form-container">
                <div class="form-header">
                    <h2 class="form-title">Add new Lid</h2>
                    <a href="{{ route('ledenpagina') }}" class="close-btn">&times;</a>
                </div>

                <div class="form-section-title">Lid details</div>
                
                @if ($errors->any())
                    <div class="error-container">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                
                <form action="{{ route('addlid.store') }}" method="POST">
                    @csrf
                    
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="name">Naam</label>
                            <input type="text" id="name" name="name" placeholder="Naam" value="{{ old('name') }}" required>
                        </div>

                        <div class="form-group">
                            <label for="woonplaats">Woonplaats</label>
                            <select id="woonplaats" name="woonplaats" required>
                                <option value="" disabled selected>Select</option>
                                <option value="Latour">Latour</option>
                                <option value="Paramaribo">Paramaribo</option>
                                <option value="Wanica">Wanica</option>
                                <option value="Nickerie">Nickerie</option>
                                <option value="Commewijne">Commewijne</option>
                                <option value="Saramacca">Saramacca</option>
                                <option value="Para">Para</option>
                                <option value="Coronie">Coronie</option>
                                <option value="Marowijne">Marowijne</option>
                                <option value="Brokopondo">Brokopondo</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="email">Email *</label>
                            <input type="email" id="email" name="email" placeholder="email@address.com" value="{{ old('email') }}" required>
                        </div>

                        <div class="form-group">
                            <label for="adres">Adres*</label>
                            <input type="text" id="adres" name="adres" placeholder="password" value="{{ old('adres') }}" required>
                        </div>

                        <div class="form-group">
                            <label for="telefoonnummer">Telefoon*</label>
                            <input type="text" id="telefoonnummer" name="telefoonnummer" placeholder="Telefoon" value="{{ old('telefoonnummer') }}" required>
                        </div>

                        <div class="form-group">
                            <label for="geboortedatum">Geboortedatum*</label>
                            <input type="date" id="geboortedatum" name="geboortedatum" value="{{ old('geboortedatum', date('Y-m-d')) }}" required>
                        </div>
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" class="btn-add-user">Add User</button>
                        <a href="{{ route('ledenpagina') }}" class="btn-cancel">Cancel</a>
                    </div>
                </form>
            </div>
        </main>
    </div>
</body>
</html>
