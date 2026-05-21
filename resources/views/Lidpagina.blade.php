<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lidpagina</title>
    @vite(['resources/css/app.css', 'resources/css/sidebar.css', 'resources/css/Lidpagina.css', 'resources/js/app.js'])
</head>
<body>

        <!-- Navigation bar -->
    @include('layouts.sidebar')


    <div class="main-content">
        @include('layouts.header')

        <section class="w-full justify-between h-[450px] flex flex-row border-6  border-red-200 p-7 ">
            <div class="w-1/4 justify-center items-center text-center h-full border-2 border-blue-500 p-5">
                <div class="bg-gray-200 h-full w-full border-1 border-black">

                </div>
            </div>
            <div class="w-1/4 h-full border-2 border-red-500 p-5">
                <h1>sadadddsd2</h1>
            </div>
        </section>
    </div>
</body>
</html>
