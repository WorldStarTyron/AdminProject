<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Ledenpagina</title>
    @vite(['resources/css/app.css', 'resources/css/sidebar.css','resources/css/totalleden.css', 
    'resources/css/Toevoegen.css', 'resources/js/app.js', 'resources/js/AddLidModal.js',])
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    @include('layouts.sidebar')
 <div class="main-content">
        @include('layouts.header')       
         <br>
        
        @include('layouts.leden-overzicht')
        
        <main class="page-content">
            <div class="table-container">
                <div class="table-header-top">
                    <div class="table-title-section">
                        <h2 class="table-title">Leden Overzicht</h2>
                        <p style="color: #64748b; margin: 0; font-size: 0.875rem;">Beheer alle geregistreerde leden in het systeem</p>
                    </div>
                    <div class="table-actions" style="display: flex; gap: 1rem; align-items: center;">
                        <button class="btn-add" id="openModalBtn" type="button">+ Voeg lid</button>
                        <button class="btn-filter">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M3 6H21M6 12H18M10 18H14" stroke="#1e3a8a" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="table-wrapper">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Telefoon</th>
                                <th>Email</th>
                                <th>Adress</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($leden as $lid)
                                <tr>
                                    <td>{{ $lid->lid_id }}</td>
                                    <td class="font-semibold">{{ $lid->naam }}</td>
                                    <td>{{ $lid->telefoonnummer }}</td>
                                    <td>{{ $lid->email }}</td>
                                    <td>{{ $lid->adres }}</td>
                                    <td class="text-right">
                                        <button class="btn-more">
                                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M5 12H5.01M12 12H12.01M19 12H19.01" stroke="#1e3a8a" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                            </svg>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="table-footer">
                    <div class="pagination">
                        @php
                            $currentPage = $leden->currentPage();
                            $lastPage = $leden->lastPage();
                        @endphp

                        {{-- First pages 1 through min(5, lastPage) --}}
                        @for($i = 1; $i <= min(5, $lastPage); $i++)
                            @if($i == $currentPage)
                                <span class="page-link active">{{ $i }}</span>
                            @else
                                <a href="{{ $leden->url($i) }}" class="page-link">{{ $i }}</a>
                            @endif
                        @endfor

                        {{-- Dots + last page if more than 5 pages --}}
                        @if($lastPage > 5)
                            <span class="page-dots">...</span>
                            @if($currentPage == $lastPage)
                                <span class="page-link active">{{ $lastPage }}</span>
                            @else
                                <a href="{{ $leden->url($lastPage) }}" class="page-link">{{ $lastPage }}</a>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Modal Overlay -->
    <div class="modal-overlay" id="addLidModal">
        <div class="modal-container">
            <div class="form-container">
                <div class="form-header">
                    <h2 class="form-title">Add new Lid</h2>
                    <button type="button" class="close-btn" id="closeModalBtn">&times;</button> 
                </div>

                <div class="form-section-title">Lid details</div>
                
                <div class="error-container" id="modalErrors" style="display: none;">
                    <ul id="modalErrorList"></ul>
                </div>
                
                <form id="addLidForm">
                    @csrf
                    
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="name">Naam</label>
                            <input type="text" id="name" name="name" placeholder="Naam" required>
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
                            <input type="email" id="email" name="email" placeholder="email@address.com" required>
                        </div>

                        <div class="form-group">
                            <label for="adres">Adres*</label>
                            <input type="text" id="adres" name="adres" placeholder="Adres" required>
                        </div>

                        <div class="form-group">
                            <label for="telefoonnummer">Telefoon*</label>
                            <input type="text" id="telefoonnummer" name="telefoonnummer" placeholder="Telefoon (+597)" required>
                        </div>

                        <div class="form-group">
                            <label for="geboortedatum">Geboortedatum*</label>
                            <input type="date" id="geboortedatum" name="geboortedatum" value="{{ date('Y-m-d') }}" required>
                        </div>
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" class="btn-add-user" id="submitBtn">Add Lid</button>
                        <button type="button" class="btn-cancel" id="cancelModalBtn">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Success Toast -->
    <div class="toast-notification" id="successToast">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M9 12L11 14L15 10M21 12C21 16.9706 16.9706 21 12 21C7.02944 21 3 16.9706 3 12C3 7.02944 7.02944 3 12 3C16.9706 3 21 7.02944 21 12Z" stroke="#10b981" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
        <span>Lid succesvol toegevoegd!</span>
    </div>

   
</body>
</html>
