<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ledenpagina</title>
    @vite(['resources/css/app.css', 'resources/css/sidebar.css','resources/css/totalleden.css', 'resources/js/app.js'])
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
                        <h2 class="table-title">Leden Table</h2>
                        <a href="{{ route('addlid.create') }}" class="btn-add" style="text-decoration: none;">Voeg lid</a>
                    </div>
                    <div class="table-actions">
                        <button class="btn-filter">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M3 6H21M6 12H18M10 18H14" stroke="#4A5568" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
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
                                                <path d="M5 12H5.01M12 12H12.01M19 12H19.01" stroke="#4A5568" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
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
                        <button class="page-link active">1</button>
                        <button class="page-link ">2</button>
                        <button class="page-link">3</button>
                        <button class="page-link">4</button>
                        <button class="page-link">5</button>
                        <span class="page-dots">...</span>
                        <button class="page-link">20</button>
                    </div>
                </div>
            </div>
        </main>
    </div>













    
</body>
</html>
